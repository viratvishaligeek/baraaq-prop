<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CircleRate;
use App\Models\CircleRateAreaType;
use App\Models\CircleRateDistrict;
use App\Models\CircleRateFinancialYear;
use App\Models\CircleRateImportBatch;
use App\Models\CircleRateImportRow;
use App\Models\CircleRateLocation;
use App\Models\CircleRatePropertyCategory;
use App\Models\CircleRatePropertyType;
use App\Models\CircleRateRoadWidthRange;
use App\Models\CircleRateTehsil;
use App\Models\CircleRateUnit;
use App\Services\CircleRate\SpreadsheetReader;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class CircleRateImportController extends Controller
{
    private const MAP_FIELDS = [
        'district' => 'District',
        'tehsil' => 'Tehsil',
        'area_type' => 'Area type',
        'location' => 'Village / Mohalla / Location',
        'property_type' => 'Property type',
        'category' => 'Property category',
        'road_width' => 'Road width',
        'rate' => 'Rate amount',
        'unit' => 'Rate unit',
        'effective_from' => 'Effective from',
        'effective_until' => 'Effective until',
        'conditions' => 'Conditions JSON',
    ];

    public function index()
    {
        return view('admin.circle-rates.imports.index', [
            'batches' => CircleRateImportBatch::query()->with('financialYear')->latest()->paginate(20),
            'financialYears' => CircleRateFinancialYear::query()->where('is_active', true)->orderByDesc('starts_on')->get(),
        ]);
    }

    public function upload(Request $request, SpreadsheetReader $reader)
    {
        $data = $request->validate([
            'financial_year_id' => ['required', 'integer', 'exists:circle_rate_financial_years,id'],
            'source_id' => ['nullable', 'integer', 'exists:circle_rate_sources,id'],
            'file' => ['required', 'file', 'mimes:csv,xlsx,xls', 'max:51200'],
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        try {
            $preview = $reader->read($file->getRealPath(), $extension);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['file' => $exception->getMessage()])->withInput();
        }

        if (count($preview['headers']) < 2) {
            return back()->withErrors(['file' => 'The spreadsheet must contain a header row and at least one data column.'])->withInput();
        }

        $hash = hash_file('sha256', $file->getRealPath());
        $duplicate = CircleRateImportBatch::query()->where('file_hash', $hash)->latest('id')->first();
        $storedPath = $file->store('circle-rate-imports', 'public');

        $batch = CircleRateImportBatch::query()->create([
            'financial_year_id' => $data['financial_year_id'],
            'source_id' => $data['source_id'] ?? null,
            'uploaded_by' => $request->user()->id,
            'original_filename' => $file->getClientOriginalName(),
            'stored_path' => $storedPath,
            'file_hash' => $hash,
            'file_type' => $extension,
            'status' => 'uploaded',
            'total_rows' => count($preview['rows']),
            'summary' => $duplicate ? 'File content matches earlier import batch #'.$duplicate->id.'. Review for duplicate rates before confirmation.' : null,
        ]);

        return redirect()->route('admin.circle-rates.imports.show', $batch);
    }

    public function show(CircleRateImportBatch $batch, SpreadsheetReader $reader)
    {
        $path = Storage::disk('public')->path($batch->stored_path);
        abort_unless(is_file($path), 404, 'The original import file is missing.');

        try {
            $spreadsheet = $reader->read($path, $batch->file_type);
        } catch (RuntimeException $exception) {
            return redirect()->route('admin.circle-rates.imports.index')->withErrors(['file' => $exception->getMessage()]);
        }

        return view('admin.circle-rates.imports.show', [
            'batch' => $batch->load(['financialYear', 'rows']),
            'headers' => $spreadsheet['headers'],
            'mapFields' => self::MAP_FIELDS,
            'rows' => $batch->rows()->orderBy('row_number')->paginate(30),
            'previewRows' => $spreadsheet['rows'],
            'validCount' => $batch->rows()->where('status', 'valid')->count(),
        ]);
    }

    public function preview(Request $request, CircleRateImportBatch $batch, SpreadsheetReader $reader)
    {
        $mapping = $request->validate([
            'mapping' => ['required', 'array'],
            'mapping.district' => ['required', 'string', 'max:180'],
            'mapping.property_type' => ['required', 'string', 'max:180'],
            'mapping.rate' => ['required', 'string', 'max:180'],
            'mapping.unit' => ['required', 'string', 'max:180'],
            'mapping.effective_from' => ['required', 'string', 'max:180'],
        ])['mapping'];

        $path = Storage::disk('public')->path($batch->stored_path);
        $spreadsheet = $reader->read($path, $batch->file_type);
        foreach ($mapping as $field => $header) {
            if (! in_array($header, $spreadsheet['headers'], true)) {
                return back()->withErrors(['mapping' => 'A mapped column is not present in the uploaded file.']);
            }
        }

        $year = $batch->financialYear;
        $batch->rows()->delete();
        $valid = $invalid = $conflicts = 0;

        foreach ($spreadsheet['rows'] as $index => $row) {
            $errors = [];
            $get = fn (string $key): string => isset($mapping[$key]) ? trim((string) ($row[$mapping[$key]] ?? '')) : '';

            $district = $this->findNamed(CircleRateDistrict::class, $get('district'));
            $propertyType = $this->findNamed(CircleRatePropertyType::class, $get('property_type'));
            $unit = $this->findUnit($get('unit'));
            $tehsil = $get('tehsil') !== '' ? $this->findNamed(CircleRateTehsil::class, $get('tehsil')) : null;
            $areaType = $get('area_type') !== '' ? $this->findNamed(CircleRateAreaType::class, $get('area_type')) : null;
            $location = $get('location') !== '' ? $this->findLocation($get('location'), $district?->id) : null;
            $category = $get('category') !== '' ? $this->findNamed(CircleRatePropertyCategory::class, $get('category')) : null;
            $roadWidth = $get('road_width') !== '' ? $this->findNamed(CircleRateRoadWidthRange::class, $get('road_width')) : null;
            $rate = $get('rate');
            $effectiveFrom = $this->parseDate($get('effective_from'));
            $effectiveUntil = $get('effective_until') !== '' ? $this->parseDate($get('effective_until')) : null;

            if (! $district) $errors[] = 'District was not found or is inactive.';
            if (! $propertyType) $errors[] = 'Property type was not found or is inactive.';
            if (! $unit) $errors[] = 'Rate unit was not found or is inactive.';
            if ($get('tehsil') !== '' && ! $tehsil) $errors[] = 'Tehsil was not found.';
            if ($get('area_type') !== '' && ! $areaType) $errors[] = 'Area type was not found.';
            if ($get('location') !== '' && ! $location) $errors[] = 'Location was not found under the selected district.';
            if ($get('category') !== '' && ! $category) $errors[] = 'Property category was not found.';
            if ($get('road_width') !== '' && ! $roadWidth) $errors[] = 'Road width range was not found.';
            if (! is_numeric($rate) || (float) $rate <= 0) $errors[] = 'Rate must be a positive numeric value.';
            if (! $effectiveFrom) $errors[] = 'Effective-from date is missing or invalid.';
            if ($effectiveFrom && ($effectiveFrom < $year->starts_on || $effectiveFrom > $year->ends_on)) $errors[] = 'Effective-from date is outside the selected financial year.';
            if ($effectiveUntil && $effectiveFrom && $effectiveUntil < $effectiveFrom) $errors[] = 'Effective-until date cannot precede effective-from date.';

            $conditions = [];
            if ($get('conditions') !== '') {
                $decoded = json_decode($get('conditions'), true);
                if (! is_array($decoded)) $errors[] = 'Conditions must contain valid JSON.';
                else $conditions = $decoded;
            }

            $normalized = [
                'district_id' => $district?->id,
                'tehsil_id' => $tehsil?->id,
                'area_type_id' => $areaType?->id,
                'location_id' => $location?->id,
                'property_type_id' => $propertyType?->id,
                'category_id' => $category?->id,
                'road_width_range_id' => $roadWidth?->id,
                'financial_year_id' => $year->id,
                'unit_id' => $unit?->id,
                'source_id' => $batch->source_id,
                'rate' => is_numeric($rate) ? (float) $rate : null,
                'effective_from' => $effectiveFrom?->toDateString(),
                'effective_until' => $effectiveUntil?->toDateString(),
                'conditions' => $conditions,
            ];

            $conflict = false;
            if (! $errors) {
                $existing = CircleRate::query()->where('district_id', $normalized['district_id'])
                    ->where('property_type_id', $normalized['property_type_id'])
                    ->where('financial_year_id', $normalized['financial_year_id'])
                    ->where('unit_id', $normalized['unit_id'])
                    ->where('effective_from', $normalized['effective_from'])
                    ->where('verification_status', 'verified')
                    ->where('status', 'published')
                    ->whereNull('deleted_at')
                    ->where('location_id', $normalized['location_id'])
                    ->where('category_id', $normalized['category_id'])
                    ->where('road_width_range_id', $normalized['road_width_range_id'])
                    ->exists();
                $conflict = $existing;
                if ($conflict) $errors[] = 'A verified rate already exists for this same location, property type, year and effective date. Manual review is required.';
            }

            $status = $conflict ? 'conflict' : ($errors ? 'invalid' : 'valid');
            $batch->rows()->create([
                'row_number' => $index + 2,
                'original_data' => $row,
                'normalized_data' => $normalized,
                'validation_errors' => $errors,
                'status' => $status,
            ]);

            if ($status === 'valid') $valid++;
            elseif ($status === 'conflict') $conflicts++;
            else $invalid++;
        }

        $batch->update([
            'column_mapping' => $mapping,
            'status' => 'previewed',
            'total_rows' => count($spreadsheet['rows']),
            'valid_rows' => $valid,
            'invalid_rows' => $invalid,
            'conflict_rows' => $conflicts,
            'summary' => 'Preview completed. No rates have been written to the published rate table.',
        ]);

        return redirect()->route('admin.circle-rates.imports.show', $batch)->with('success', 'Import preview generated. Review every warning before confirming.');
    }

    public function confirm(Request $request, CircleRateImportBatch $batch)
    {
        $request->validate(['confirm' => ['accepted']]);
        abort_unless($batch->status === 'previewed', 422, 'This import batch is not ready for confirmation.');

        $imported = DB::transaction(function () use ($request, $batch): int {
            $count = 0;
            foreach ($batch->rows()->where('status', 'valid')->orderBy('row_number')->lockForUpdate()->get() as $row) {
                $data = $row->normalized_data ?? [];
                $conflict = CircleRate::query()->where('district_id', $data['district_id'])
                    ->where('property_type_id', $data['property_type_id'])
                    ->where('financial_year_id', $data['financial_year_id'])
                    ->where('effective_from', $data['effective_from'])
                    ->where('verification_status', 'verified')
                    ->where('status', 'published')
                    ->whereNull('deleted_at')
                    ->where('location_id', $data['location_id'])
                    ->where('category_id', $data['category_id'])
                    ->where('road_width_range_id', $data['road_width_range_id'])
                    ->exists();

                if ($conflict) {
                    $row->update(['status' => 'conflict', 'validation_errors' => ['A verified rate appeared after preview; no data was overwritten.']]);
                    continue;
                }

                $rate = CircleRate::query()->create(array_merge($data, [
                    'status' => 'draft',
                    'verification_status' => 'pending',
                    'created_by' => $request->user()->id,
                ]));
                $row->update(['status' => 'imported', 'circle_rate_id' => $rate->id]);
                $count++;
            }

            $batch->update(['status' => $count > 0 ? 'imported' : 'review_required', 'summary' => $count.' draft rates imported. They still require source verification and publication.']);
            return $count;
        });

        return redirect()->route('admin.circle-rates.imports.show', $batch)->with('success', $imported.' draft rate records imported. No verified rate was overwritten.');
    }

    public function errors(CircleRateImportBatch $batch)
    {
        return response()->streamDownload(function () use ($batch): void {
            $output = fopen('php://output', 'wb');
            fputcsv($output, ['Row', 'Status', 'Errors', 'Original data']);
            foreach ($batch->rows()->whereIn('status', ['invalid', 'conflict'])->orderBy('row_number')->cursor() as $row) {
                fputcsv($output, [$row->row_number, $row->status, implode(' | ', $row->validation_errors ?? []), json_encode($row->original_data)]);
            }
            fclose($output);
        }, 'circle-rate-import-errors-'.$batch->id.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function findNamed(string $model, string $name): ?object
    {
        if ($name === '') return null;
        return $model::query()->where('is_active', true)->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();
    }

    private function findUnit(string $name): ?CircleRateUnit
    {
        if ($name === '') return null;
        return CircleRateUnit::query()->where('is_active', true)
            ->where(fn ($q) => $q->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->orWhereRaw('LOWER(symbol) = ?', [mb_strtolower($name)]))
            ->first();
    }

    private function findLocation(string $name, ?int $districtId): ?CircleRateLocation
    {
        if (! $districtId) return null;
        return CircleRateLocation::query()->where('district_id', $districtId)->where('is_active', true)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();
    }

    private function parseDate(string $value): ?Carbon
    {
        if ($value === '') return null;
        try {
            if (is_numeric($value) && (float) $value > 20000) {
                return Carbon::create(1899, 12, 30)->addDays((int) $value)->startOfDay();
            }
            return Carbon::parse($value)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }
}
