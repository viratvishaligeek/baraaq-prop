<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CircleRate;
use App\Models\CircleRateAuditLog;
use App\Models\CircleRateAreaType;
use App\Models\CircleRateDistrict;
use App\Models\CircleRateFinancialYear;
use App\Models\CircleRateImportBatch;
use App\Models\CircleRateImportRow;
use App\Models\CircleRateVersion;
use App\Models\CircleRateLocation;
use App\Models\CircleRatePropertyCategory;
use App\Models\CircleRatePropertyType;
use App\Models\CircleRateRoadWidthRange;
use App\Models\CircleRateSource;
use App\Models\CircleRateTehsil;
use App\Models\CircleRateUnit;
use App\Services\CircleRate\SpreadsheetReader;
use App\Services\CircleRate\CircleRateCache;
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
            'sources' => CircleRateSource::query()->orderByDesc('id')->get(),
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
        $storedPath = $file->store('circle-rate-imports', 'local');

        $batch = CircleRateImportBatch::query()->create([
            'financial_year_id' => $data['financial_year_id'],
            'source_id' => $data['source_id'] ?? null,
            'uploaded_by' => $request->user()->id,
            'original_filename' => $file->getClientOriginalName(),
            'stored_path' => $storedPath,
            'file_hash' => $hash,
            'file_type' => $extension,
            'column_mapping' => $this->detectMapping($preview['headers']),
            'status' => 'uploaded',
            'total_rows' => count($preview['rows']),
            'summary' => $duplicate ? 'File content matches earlier import batch #'.$duplicate->id.'. Review for duplicate rates before confirmation.' : null,
        ]);

        return redirect()->route('admin.circle-rates.imports.show', $batch);
    }

    public function show(CircleRateImportBatch $batch, SpreadsheetReader $reader)
    {
        $path = Storage::disk('local')->path($batch->stored_path);
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
            'duplicateCount' => $batch->rows()->where('status', 'duplicate')->count(),
        ]);
    }

    public function preview(Request $request, CircleRateImportBatch $batch, SpreadsheetReader $reader)
    {
        abort_unless(! str_starts_with($batch->status, 'location_'), 404);
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
        $valid = $invalid = $conflicts = $duplicates = 0;
        $seenRows = [];

        foreach ($spreadsheet['rows'] as $index => $row) {
            $errors = [];
            $get = fn (string $key): string => isset($mapping[$key]) ? trim((string) ($row[$mapping[$key]] ?? '')) : '';

            $district = $this->findNamed(CircleRateDistrict::class, $get('district'));
            $propertyType = $this->findNamed(CircleRatePropertyType::class, $get('property_type'));
            $unit = $this->findUnit($get('unit'));
            $tehsil = $get('tehsil') !== '' ? $this->findTehsil($get('tehsil'), $district?->id) : null;
            $areaType = $get('area_type') !== '' ? $this->findNamed(CircleRateAreaType::class, $get('area_type')) : null;
            $location = $get('location') !== '' ? $this->findLocation($get('location'), $district?->id) : null;
            $category = $get('category') !== '' ? $this->findCategory($get('category'), $propertyType?->id) : null;
            $roadWidth = $get('road_width') !== '' ? $this->findRoadWidth($get('road_width'), $propertyType?->id, $location?->id, $district?->id) : null;
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
                if (! is_array($decoded) || ! array_is_list($decoded)) $errors[] = 'Conditions must be a JSON array.';
                else {
                    foreach ($decoded as $condition) {
                        if (! is_array($condition) || blank($condition['field'] ?? null) || ! array_key_exists('value', $condition) || ! in_array($condition['operator'] ?? 'equals', ['equals', 'not_equals', 'greater_than', 'less_than', 'greater_than_or_equal', 'less_than_or_equal', 'between', 'in'], true)) {
                            $errors[] = 'Every condition needs a field, supported operator and value.';
                            break;
                        }
                    }
                    $conditions = $decoded;
                }
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
            $duplicate = false;
            if (! $errors) {
                $signature = hash('sha256', json_encode([
                    $normalized['district_id'], $normalized['tehsil_id'], $normalized['area_type_id'],
                    $normalized['location_id'], $normalized['property_type_id'], $normalized['category_id'],
                    $normalized['road_width_range_id'], $normalized['financial_year_id'],
                    $normalized['unit_id'], $normalized['effective_from'], $normalized['conditions'],
                ]));
                if (isset($seenRows[$signature])) {
                    $conflict = true;
                    $errors[] = 'This row duplicates another row in the same uploaded file.';
                }
                $seenRows[$signature] = true;

                $existingRecords = $this->matchingRates($normalized);
                $verifiedConflict = $existingRecords->contains(fn (CircleRate $existingRate) => $existingRate->verification_status === 'verified' && $existingRate->status === 'published');
                if ($verifiedConflict) {
                    $conflict = true;
                    $errors[] = 'A verified rate already exists for an overlapping effective date. It cannot be overwritten.';
                } elseif ($existingRecords->count() > 1) {
                    $conflict = true;
                    $errors[] = 'Multiple existing rates match this record. Manual review is required.';
                } elseif ($existingRecords->count() === 1) {
                    $duplicate = true;
                    $normalized['existing_rate_id'] = $existingRecords->first()->id;
                    $errors[] = 'An unverified rate already exists. Select the explicit update option during confirmation to create a historical version and replace it.';
                }
            }

            $status = $conflict ? 'conflict' : ($duplicate ? 'duplicate' : ($errors ? 'invalid' : 'valid'));
            $batch->rows()->create([
                'row_number' => $index + 2,
                'original_data' => $row,
                'normalized_data' => $normalized,
                'validation_errors' => $errors,
                'status' => $status,
            ]);

            if ($status === 'valid') $valid++;
            elseif ($status === 'conflict') $conflicts++;
            elseif ($status === 'duplicate') $duplicates++;
            else $invalid++;
        }

        $batch->update([
            'column_mapping' => $mapping,
            'status' => 'previewed',
            'total_rows' => count($spreadsheet['rows']),
            'valid_rows' => $valid,
            'invalid_rows' => $invalid,
            'conflict_rows' => $conflicts,
            'summary' => 'Preview completed. '.$valid.' new rows, '.$duplicates.' existing unverified rows, '.$invalid.' invalid rows and '.$conflicts.' conflicts. Nothing has been published.',
        ]);

        return redirect()->route('admin.circle-rates.imports.show', $batch)->with('success', 'Import preview generated. Review every warning before confirming.');
    }

    public function confirm(Request $request, CircleRateImportBatch $batch)
    {
        abort_unless(! str_starts_with($batch->status, 'location_'), 404);
        $request->validate(['confirm' => ['accepted'], 'update_unverified' => ['nullable', 'boolean']]);
        $updateUnverified = $request->boolean('update_unverified');
        abort_unless(in_array($batch->status, ['previewed', 'review_required'], true), 422, 'This import batch is not ready for confirmation.');

        $imported = DB::transaction(function () use ($request, $batch, $updateUnverified): int {
            $count = 0;
            $statuses = $updateUnverified ? ['valid', 'duplicate'] : ['valid'];
            foreach ($batch->rows()->whereIn('status', $statuses)->orderBy('row_number')->lockForUpdate()->get() as $row) {
                $data = $row->normalized_data ?? [];
                $matches = $this->matchingRates($data);
                $verifiedConflict = $matches->contains(fn (CircleRate $existingRate) => $existingRate->verification_status === 'verified' && $existingRate->status === 'published');

                if ($verifiedConflict || $matches->count() > 1) {
                    $row->update(['status' => 'conflict', 'validation_errors' => ['A conflicting or verified rate exists at confirmation time. No verified rate was overwritten.']]);
                    continue;
                }

                $existingRate = $matches->first();
                if ($existingRate && ! $updateUnverified) {
                    $row->update([
                        'status' => 'duplicate',
                        'normalized_data' => array_merge($data, ['existing_rate_id' => $existingRate->id]),
                        'validation_errors' => ['An unverified rate now exists. Confirm again with the explicit update option to replace it safely.'],
                    ]);
                    continue;
                }

                if ($existingRate) {
                    $old = $existingRate->getAttributes();
                    if (empty($data['source_id'])) $data['source_id'] = $existingRate->source_id;
                    $version = ((int) $existingRate->versions()->max('version_number')) + 1;
                    CircleRateVersion::query()->create([
                        'circle_rate_id' => $existingRate->id,
                        'version_number' => $version,
                        'snapshot' => $old,
                        'change_reason' => 'Updated from import batch #'.$batch->id.' row #'.$row->row_number,
                        'changed_by' => $request->user()->id,
                    ]);
                    unset($data['existing_rate_id']);
                    $existingRate->update(array_merge($data, [
                        'status' => 'draft',
                        'verification_status' => 'pending',
                        'verified_by' => null,
                        'verified_at' => null,
                    ]));
                    $rate = $existingRate->fresh();
                    $event = 'import_updated_existing_draft';
                    $oldValues = $old;
                } else {
                    unset($data['existing_rate_id']);
                    $rate = CircleRate::query()->create(array_merge($data, [
                        'status' => 'draft',
                        'verification_status' => 'pending',
                        'created_by' => $request->user()->id,
                    ]));
                    $event = 'imported_as_draft';
                    $oldValues = null;
                }

                $row->update(['status' => 'imported', 'circle_rate_id' => $rate->id]);
                CircleRateAuditLog::query()->create([
                    'user_id' => $request->user()->id,
                    'subject_type' => CircleRate::class,
                    'subject_id' => $rate->id,
                    'event' => $event,
                    'old_values' => $oldValues,
                    'new_values' => $rate->getAttributes(),
                    'reason' => 'Imported from batch #'.$batch->id.' row #'.$row->row_number,
                    'source_id' => $rate->source_id,
                    'ip_address' => $request->ip(),
                ]);
                $count++;
            }
            $pendingReview = $batch->rows()->whereIn('status', ['valid', 'duplicate'])->exists();
            $batch->update(['status' => $pendingReview ? 'review_required' : ($count > 0 ? 'imported' : 'review_required'), 'summary' => $count.' draft rates imported. '.($pendingReview ? 'Unverified duplicate rows remain available for explicit review.' : 'All eligible rows have been processed.').' Imported rates still require source verification and publication.', 'confirmed_at' => $count > 0 ? now() : $batch->confirmed_at]);
            return $count;
        });

        if ($imported > 0) CircleRateCache::bumpVersion();
        return redirect()->route('admin.circle-rates.imports.show', $batch)->with('success', $imported.' draft rate records imported. No verified rate was overwritten.');
    }

    public function errors(CircleRateImportBatch $batch)
    {
        return response()->streamDownload(function () use ($batch): void {
            $output = fopen('php://output', 'wb');
            fputcsv($output, ['Row', 'Status', 'Errors', 'Original data']);
            foreach ($batch->rows()->whereIn('status', ['invalid', 'conflict', 'duplicate'])->orderBy('row_number')->cursor() as $row) {
                fputcsv($output, [$row->row_number, $row->status, implode(' | ', $row->validation_errors ?? []), json_encode($row->original_data)]);
            }
            fclose($output);
        }, 'circle-rate-import-errors-'.$batch->id.'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function detectMapping(array $headers): array
    {
        $aliases = [
            'district' => ['district', 'zilla'],
            'tehsil' => ['tehsil'],
            'area_type' => ['area type', 'area category', 'urban rural', 'urban/rural'],
            'location' => ['location', 'village', 'mohalla', 'locality', 'zone'],
            'property_type' => ['property type', 'property', 'type'],
            'category' => ['category', 'property category'],
            'road_width' => ['road width', 'width range', 'road width range'],
            'rate' => ['rate', 'circle rate', 'amount', 'value'],
            'unit' => ['unit', 'rate unit', 'measurement unit'],
            'effective_from' => ['effective from', 'effective date', 'start date', 'valid from'],
            'effective_until' => ['effective until', 'expiry date', 'end date', 'valid until'],
            'conditions' => ['conditions', 'conditions json', 'rule conditions'],
        ];

        $normalized = [];
        foreach ($headers as $header) {
            $normalized[preg_replace('/[^a-z0-9]+/', ' ', strtolower(trim($header)))] = $header;
        }

        $mapping = [];
        foreach ($aliases as $field => $candidates) {
            foreach ($candidates as $candidate) {
                if (isset($normalized[$candidate])) {
                    $mapping[$field] = $normalized[$candidate];
                    break;
                }
            }
        }

        return $mapping;
    }

    private function matchingRates(array $data)
    {
        return CircleRate::query()
            ->where('district_id', $data['district_id'])
            ->where('property_type_id', $data['property_type_id'])
            ->where('tehsil_id', $data['tehsil_id'])
            ->where('area_type_id', $data['area_type_id'])
            ->where('financial_year_id', $data['financial_year_id'])
            ->where('unit_id', $data['unit_id'])
            ->whereDate('effective_from', '<=', $data['effective_from'])
            ->where(fn ($q) => $q->whereNull('effective_until')->orWhereDate('effective_until', '>=', $data['effective_from']))
            ->where('location_id', $data['location_id'])
            ->where('category_id', $data['category_id'])
            ->where('road_width_range_id', $data['road_width_range_id'])
            ->get()
            ->filter(fn (CircleRate $rate) => ($rate->conditions ?? []) == ($data['conditions'] ?? []))
            ->values();
    }

    private function findNamed(string $model, string $name): ?object
    {
        if ($name === '') return null;
        return $model::query()->where('is_active', true)->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();
    }

    private function locationMatchesIds(?CircleRateLocation $location, array $allowedIds): bool
    {
        $allowed = array_map('strval', $allowedIds);
        while ($location) {
            if (in_array((string) $location->id, $allowed, true)) return true;
            $location = $location->parent;
        }
        return false;
    }

    private function findCategory(string $name, ?int $propertyTypeId): ?CircleRatePropertyCategory
    {
        if ($name === '') return null;
        $query = CircleRatePropertyCategory::query()->where('is_active', true)->whereRaw('LOWER(name) = ?', [mb_strtolower($name)]);
        if ($propertyTypeId) {
            $query->whereHas('propertyTypes', fn ($q) => $q->whereKey($propertyTypeId));
        }
        return $query->first();
    }

    private function findRoadWidth(string $name, ?int $propertyTypeId, ?int $locationId, ?int $districtId): ?CircleRateRoadWidthRange
    {
        if ($name === '') return null;
        $range = CircleRateRoadWidthRange::query()->where('is_active', true)->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();
        if (! $range) return null;
        $applicability = $range->applicability ?? [];
        foreach (['property_type_ids' => $propertyTypeId, 'location_ids' => $locationId, 'district_ids' => $districtId] as $key => $selected) {
            if (! empty($applicability[$key])) {
                $matches = $key === 'location_ids' ? ($locationId ? $this->locationMatchesIds(CircleRateLocation::query()->find($locationId), $applicability[$key]) : false) : in_array((string) $selected, array_map('strval', $applicability[$key]), true);
                if (! $matches) return null;
            }
        }
        return $range;
    }

    private function findTehsil(string $name, ?int $districtId): ?CircleRateTehsil
    {
        if ($name === '' || ! $districtId) return null;
        return CircleRateTehsil::query()->where('district_id', $districtId)->where('is_active', true)->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first();
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
