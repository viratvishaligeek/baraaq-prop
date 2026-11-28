<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CircleRateAreaType;
use App\Models\CircleRateAuditLog;
use App\Models\CircleRateDistrict;
use App\Models\CircleRateImportBatch;
use App\Models\CircleRateImportRow;
use App\Models\CircleRateLocation;
use App\Models\CircleRateLocationLevel;
use App\Models\CircleRateTehsil;
use App\Services\CircleRate\CircleRateCache;
use App\Services\CircleRate\SpreadsheetReader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CircleRateLocationImportController extends Controller
{
    public function index()
    {
        return view('admin.circle-rates.location-import.index', [
            'batches' => CircleRateImportBatch::query()->where('status', 'like', 'location_%')->latest()->paginate(20),
        ]);
    }

    public function store(Request $request, SpreadsheetReader $reader)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt,xlsx,xls', 'max:51200']]);
        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $preview = $reader->read($file->getRealPath(), $extension);
        $path = $file->store('circle-rate-location-imports', 'local');

        $batch = CircleRateImportBatch::query()->create([
            'created_by' => $request->user()->id,
            'original_filename' => $file->getClientOriginalName(),
            'stored_path' => $path,
            'file_type' => $extension,
            'file_hash' => hash_file('sha256', Storage::disk('local')->path($path)),
            'column_mapping' => $this->detectMapping($preview['headers']),
            'total_rows' => count($preview['rows']),
            'status' => 'location_uploaded',
            'summary' => 'Location file uploaded. Map columns and preview validation before creating locations.',
        ]);

        return redirect()->route('admin.circle-rates.locations.import.show', $batch);
    }

    public function show(CircleRateImportBatch $batch, SpreadsheetReader $reader)
    {
        abort_unless(str_starts_with($batch->status, 'location_'), 404);

        return view('admin.circle-rates.location-import.show', [
            'batch' => $batch,
            'rows' => $batch->rows()->orderBy('row_number')->paginate(30),
            'validCount' => $batch->rows()->where('status', 'valid')->count(),
            'duplicateCount' => $batch->rows()->where('status', 'duplicate')->count(),
            'headers' => $reader->read(Storage::disk('local')->path($batch->stored_path), $batch->file_type)['headers'],
        ]);
    }

    public function preview(Request $request, CircleRateImportBatch $batch, SpreadsheetReader $reader)
    {
        abort_unless(str_starts_with($batch->status, 'location_'), 404);
        $data = $request->validate(['column_mapping' => ['required', 'array']]);
        $preview = $reader->read(Storage::disk('local')->path($batch->stored_path), $batch->file_type);
        $mapping = $data['column_mapping'];
        $batch->rows()->delete();

        $valid = $invalid = $duplicates = 0;
        $seen = [];
        $seenNames = [];

        foreach ($preview['rows'] as $index => $row) {
            $get = fn (string $field): string => trim((string) ($row[$mapping[$field] ?? '__missing__'] ?? ''));
            $errors = [];
            $district = $get('district') !== '' ? CircleRateDistrict::query()->where('is_active', true)->whereRaw('LOWER(name) = ?', [mb_strtolower($get('district'))])->first() : null;
            $tehsil = $get('tehsil') !== '' && $district ? CircleRateTehsil::query()->where('district_id', $district->id)->where('is_active', true)->whereRaw('LOWER(name) = ?', [mb_strtolower($get('tehsil'))])->first() : null;
            $areaType = $get('area_type') !== '' ? CircleRateAreaType::query()->where('is_active', true)->whereRaw('LOWER(name) = ?', [mb_strtolower($get('area_type'))])->first() : null;
            $level = $get('level') !== '' ? CircleRateLocationLevel::query()->where('is_active', true)->whereRaw('LOWER(name) = ?', [mb_strtolower($get('level'))])->first() : null;
            $name = $get('name');
            $slug = $get('slug') !== '' ? Str::slug($get('slug')) : Str::slug(implode('-', array_filter([$tehsil?->slug, $parentName !== '' ? Str::slug($parentName) : null, $name])));
            $code = $get('code') ?: null;
            $parentName = $get('parent');

            if (! $district) $errors[] = 'District is missing or not found in master data.';
            if ($get('tehsil') !== '' && ! $tehsil) $errors[] = 'Tehsil does not belong to the selected district or is inactive.';
            if ($get('area_type') !== '' && ! $areaType) $errors[] = 'Area type was not found in master data.';
            if ($get('level') !== '' && ! $level) $errors[] = 'Location level was not found in master data.';
            if ($name === '') $errors[] = 'Location name is required.';
            if ($parentName !== '' && $district && ! CircleRateLocation::query()->where('district_id', $district->id)->whereRaw('LOWER(name) = ?', [mb_strtolower($parentName)])->exists() && ! isset($seenNames[($district?->id ?? 0).':'.($tehsil?->id ?? 0).':'.mb_strtolower($parentName)])) $errors[] = 'Parent location must exist already or appear earlier in this file.';
            if ($slug === '') $errors[] = 'A valid location slug could not be generated.';

            $normalized = [
                'district_id' => $district?->id,
                'tehsil_id' => $tehsil?->id,
                'area_type_id' => $areaType?->id,
                'level_id' => $level?->id,
                'parent_name' => $parentName,
                'name' => $name,
                'slug' => $slug,
                'code' => $code,
            ];

            $signature = hash('sha256', json_encode([$normalized['district_id'], $normalized['tehsil_id'], $normalized['parent_name'], $slug, mb_strtolower($name)]));
            if (! $errors && isset($seen[$signature])) $errors[] = 'This location is duplicated within the uploaded file.';
            $seen[$signature] = true;

            if (! $errors && (CircleRateLocation::query()->where('district_id', $district->id)->where('slug', $slug)->exists()
                || CircleRateLocation::query()->where('district_id', $district->id)->where('tehsil_id', $tehsil?->id)
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
                    ->when($parentName !== '', fn ($query) => $query->whereHas('parent', fn ($parentQuery) => $parentQuery->whereRaw('LOWER(name) = ?', [mb_strtolower($parentName)])), fn ($query) => $query->whereNull('parent_id'))
                    ->exists())) {
                $errors[] = 'A location with this slug already exists in the selected district.';
            }

            $status = ! $errors ? 'valid' : (str_contains(implode(' ', $errors), 'already exists') || str_contains(implode(' ', $errors), 'duplicated') ? 'duplicate' : 'invalid');
            if ($status === 'valid') {
                $valid++;
                $seenNames[($district?->id ?? 0).':'.($tehsil?->id ?? 0).':'.mb_strtolower($name)] = true;
            } elseif ($status === 'duplicate') $duplicates++;
            else $invalid++;

            $batch->rows()->create([
                'row_number' => $index + 2,
                'original_data' => $row,
                'normalized_data' => $normalized,
                'validation_errors' => $errors,
                'status' => $status,
            ]);
        }

        $batch->update([
            'column_mapping' => $mapping,
            'total_rows' => count($preview['rows']),
            'valid_rows' => $valid,
            'invalid_rows' => $invalid,
            'conflict_rows' => 0,
            'status' => 'location_previewed',
            'summary' => $valid.' valid, '.$duplicates.' duplicate and '.$invalid.' invalid location rows. No locations have been created.',
        ]);

        return redirect()->route('admin.circle-rates.locations.import.show', $batch)->with('success', 'Location import preview completed.');
    }

    public function confirm(Request $request, CircleRateImportBatch $batch)
    {
        abort_unless($batch->status === 'location_previewed', 422, 'Preview the location import before confirming.');
        $request->validate(['confirm' => ['accepted']]);

        $count = DB::transaction(function () use ($request, $batch): int {
            $created = 0;
            foreach ($batch->rows()->where('status', 'valid')->orderBy('row_number')->lockForUpdate()->get() as $row) {
                $data = $row->normalized_data ?? [];
                $parent = null;
                if (! empty($data['parent_name'])) {
                    $parent = CircleRateLocation::query()
                        ->where('district_id', $data['district_id'])
                        ->whereRaw('LOWER(name) = ?', [mb_strtolower($data['parent_name'])])
                        ->first();
                    if (! $parent) {
                        throw ValidationException::withMessages(['rows' => 'Parent location "'.$data['parent_name'].'" must already exist or appear earlier in the file.']);
                    }
                }

                $location = CircleRateLocation::query()->create([
                    'district_id' => $data['district_id'],
                    'tehsil_id' => $data['tehsil_id'],
                    'area_type_id' => $data['area_type_id'],
                    'level_id' => $data['level_id'],
                    'parent_id' => $parent?->id,
                    'name' => $data['name'],
                    'slug' => $data['slug'],
                    'code' => $data['code'],
                    'is_active' => true,
                ]);

                $row->update(['status' => 'imported', 'location_id' => $location->id]);
                CircleRateAuditLog::query()->create([
                    'user_id' => $request->user()->id,
                    'subject_type' => CircleRateLocation::class,
                    'subject_id' => $location->id,
                    'event' => 'location_imported',
                    'old_values' => null,
                    'new_values' => $location->getAttributes(),
                    'reason' => 'Imported from location batch #'.$batch->id.' row #'.$row->row_number,
                    'ip_address' => $request->ip(),
                ]);
                $created++;
            }

            $batch->update([
                'status' => 'location_imported',
                'confirmed_at' => $created > 0 ? now() : null,
                'summary' => $created.' locations imported. Existing duplicate rows were not overwritten.',
            ]);

            return $created;
        });

        if ($count > 0) CircleRateCache::bumpVersion();

        return redirect()->route('admin.circle-rates.locations.import.show', $batch)->with('success', $count.' locations imported.');
    }

    private function detectMapping(array $headers): array
    {
        $aliases = [
            'district' => ['district', 'zilla'],
            'tehsil' => ['tehsil'],
            'area_type' => ['area type', 'urban rural', 'urban/rural'],
            'level' => ['level', 'location level', 'administrative level'],
            'parent' => ['parent', 'parent location', 'parent name'],
            'name' => ['name', 'location', 'village', 'mohalla', 'locality'],
            'slug' => ['slug'],
            'code' => ['code', 'location code'],
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
}
