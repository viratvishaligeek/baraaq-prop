<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CircleRate;
use App\Models\CircleRateAreaType;
use App\Models\CircleRateAuditLog;
use App\Models\CircleRateDistrict;
use App\Models\CircleRateFinancialYear;
use App\Models\CircleRateLocation;
use App\Models\CircleRatePropertyCategory;
use App\Models\CircleRatePropertyType;
use App\Models\CircleRateRoadWidthRange;
use App\Models\CircleRateSource;
use App\Models\CircleRateTehsil;
use App\Models\CircleRateUnit;
use App\Models\CircleRateVersion;
use App\Services\CircleRate\CircleRateCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CircleRateController extends Controller
{
    private function formData(): array
    {
        return [
            'districts' => CircleRateDistrict::query()->where('is_active', true)->orderBy('name')->get(),
            'tehsils' => CircleRateTehsil::query()->where('is_active', true)->orderBy('name')->get(),
            'areaTypes' => CircleRateAreaType::query()->where('is_active', true)->orderBy('name')->get(),
            'locations' => CircleRateLocation::query()->where('is_active', true)->orderBy('name')->get(),
            'propertyTypes' => CircleRatePropertyType::query()->where('is_active', true)->orderBy('name')->get(),
            'categories' => CircleRatePropertyCategory::query()->where('is_active', true)->orderBy('name')->get(),
            'roadWidths' => CircleRateRoadWidthRange::query()->where('is_active', true)->orderBy('name')->get(),
            'financialYears' => CircleRateFinancialYear::query()->where('is_active', true)->orderByDesc('starts_on')->get(),
            'units' => CircleRateUnit::query()->where('is_active', true)->orderBy('name')->get(),
            'sources' => CircleRateSource::query()->orderByDesc('id')->get(),
        ];
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'financial_year_id' => ['nullable', 'integer'],
            'district_id' => ['nullable', 'integer'],
            'property_type_id' => ['nullable', 'integer'],
            'verification_status' => ['nullable', Rule::in(['pending', 'verified', 'rejected'])],
            'status' => ['nullable', Rule::in(['draft', 'published', 'archived'])],
            'effective_dates' => ['nullable', 'boolean'],
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $rates = CircleRate::query()->with(['district:id,name', 'tehsil:id,name', 'location:id,name', 'propertyType:id,name', 'financialYear:id,name', 'unit:id,name', 'source:id,title'])
            ->when($filters['financial_year_id'] ?? null, fn ($q, $id) => $q->where('financial_year_id', $id))
            ->when($filters['district_id'] ?? null, fn ($q, $id) => $q->where('district_id', $id))
            ->when($filters['property_type_id'] ?? null, fn ($q, $id) => $q->where('property_type_id', $id))
            ->when($filters['verification_status'] ?? null, fn ($q, $status) => $q->where('verification_status', $status))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['q'] ?? null, function ($q, $term): void {
                $q->where(function ($search) use ($term): void {
                    $search->whereHas('location', fn ($relation) => $relation->where('name', 'like', '%'.$term.'%'))
                        ->orWhereHas('district', fn ($relation) => $relation->where('name', 'like', '%'.$term.'%'))
                        ->orWhereHas('tehsil', fn ($relation) => $relation->where('name', 'like', '%'.$term.'%'))
                        ->orWhereHas('propertyType', fn ($relation) => $relation->where('name', 'like', '%'.$term.'%'));
                });
            });

        if (! empty($filters['effective_dates'])) $rates->orderByDesc('effective_from')->orderByDesc('id');
        else $rates->latest();
        $rates = $rates->paginate(25)->withQueryString();

        return view('admin.circle-rates.rates.index', array_merge($this->formData(), compact('rates', 'filters')));
    }

    public function create()
    {
        return view('admin.circle-rates.rates.form', array_merge($this->formData(), ['rate' => new CircleRate()]));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['created_by'] = $request->user()->id;
        $data['status'] = 'draft';
        $data['verification_status'] = 'pending';

        $rate = DB::transaction(function () use ($data, $request): CircleRate {
            $rate = CircleRate::query()->create($data);
            $this->audit($request, $rate, 'created', null, $rate->getAttributes(), $request->input('change_reason'));
            return $rate;
        });

        CircleRateCache::bumpVersion();
        return redirect()->route('admin.circle-rates.rates.index')->with('success', 'Rate saved as a draft. It must be verified before it can be used by the public calculator.');
    }

    public function edit(CircleRate $circleRate)
    {
        return view('admin.circle-rates.rates.form', array_merge($this->formData(), ['rate' => $circleRate]));
    }

    public function update(Request $request, CircleRate $circleRate)
    {
        $request->validate(['change_reason' => ['required', 'string', 'max:2000']]);
        $data = $this->validated($request);
        $data['status'] = $circleRate->status === 'published' ? 'draft' : $circleRate->status;
        $data['verification_status'] = $circleRate->verification_status === 'verified' ? 'pending' : $circleRate->verification_status;
        $data['verified_by'] = null;
        $data['verified_at'] = null;

        DB::transaction(function () use ($request, $circleRate, $data): void {
            $old = $circleRate->getOriginal();
            $nextVersion = (int) $circleRate->versions()->max('version_number') + 1;
            CircleRateVersion::query()->create([
                'circle_rate_id' => $circleRate->id,
                'version_number' => $nextVersion,
                'snapshot' => $old,
                'change_reason' => $request->input('change_reason'),
                'changed_by' => $request->user()->id,
            ]);
            $circleRate->update($data);
            $this->audit($request, $circleRate, 'updated', $old, $circleRate->fresh()->getAttributes(), $request->input('change_reason'));
        });

        CircleRateCache::bumpVersion();
        return redirect()->route('admin.circle-rates.rates.index')->with('success', 'Rate updated. Previous values were preserved in rate history; the changed record requires verification again.');
    }

    public function verify(Request $request, CircleRate $circleRate)
    {
        $request->validate(['reason' => ['required', 'string', 'max:2000']]);
        abort_unless($request->user()->isAdministrator() || $request->user()->hasPermission('circle-rates.verify'), 403);
        $source = $circleRate->source;
        if (! $source || (! $source->document_path && ! $source->source_url)) {
            return back()->withErrors(['source_id' => 'Attach an official source document or documented official source URL before verifying this rate.']);
        }

        DB::transaction(function () use ($request, $circleRate): void {
            $old = $circleRate->getOriginal();
            $circleRate->update([
                'verification_status' => 'verified',
                'status' => 'published',
                'verified_by' => $request->user()->id,
                'verified_at' => now(),
            ]);
            $this->audit($request, $circleRate, 'verified_and_published', $old, $circleRate->fresh()->getAttributes(), $request->input('reason'));
        });

        CircleRateCache::bumpVersion();
        return back()->with('success', 'Rate verified and published.');
    }

    public function reject(Request $request, CircleRate $circleRate)
    {
        abort_unless($request->user()->isAdministrator() || $request->user()->hasPermission('circle-rates.verify'), 403);
        $data = $request->validate(['reason' => ['required', 'string', 'max:2000']]);

        DB::transaction(function () use ($request, $circleRate, $data): void {
            $old = $circleRate->getOriginal();
            $circleRate->update(['verification_status' => 'rejected', 'status' => 'draft', 'verified_by' => null, 'verified_at' => null]);
            $this->audit($request, $circleRate, 'rejected', $old, $circleRate->fresh()->getAttributes(), $data['reason']);
        });

        CircleRateCache::bumpVersion();
        return back()->with('success', 'Rate rejected and kept out of public calculations.');
    }

    public function duplicate(Request $request, CircleRate $circleRate)
    {
        $copy = $circleRate->replicate();
        $copy->status = 'draft';
        $copy->verification_status = 'pending';
        $copy->verified_by = null;
        $copy->verified_at = null;
        $copy->created_by = $request->user()->id;
        $copy->save();
        $this->audit($request, $copy, 'duplicated', null, $copy->getAttributes(), 'Duplicated from rate #'.$circleRate->id);

        CircleRateCache::bumpVersion();
        return redirect()->route('admin.circle-rates.rates.edit', $copy)->with('success', 'Rate duplicated as an unverified draft.');
    }

    public function destroy(Request $request, CircleRate $circleRate)
    {
        $old = $circleRate->getAttributes();
        $circleRate->delete();
        $this->audit($request, $circleRate, 'archived', $old, ['deleted_at' => now()->toDateTimeString()], $request->input('reason'));

        CircleRateCache::bumpVersion();
        return back()->with('success', 'Rate archived. Historical valuation records remain linked to its rate ID.');
    }

    public function bulk(Request $request)
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['required', 'integer', 'distinct', 'exists:circle_rates,id'],
            'action' => ['required', 'in:verify,reject,archive,update_rate'],
            'reason' => ['required', 'string', 'max:2000'],
            'new_rate' => ['required_if:action,update_rate', 'nullable', 'numeric', 'gt:0'],
        ]);

        if ($data['action'] === 'verify') {
            abort_unless($request->user()->isAdministrator() || $request->user()->hasPermission('circle-rates.verify'), 403);
        }

        $count = DB::transaction(function () use ($request, $data): int {
            $rates = CircleRate::query()->whereIn('id', $data['ids'])->with('source')->lockForUpdate()->get();
            if ($rates->count() !== count($data['ids'])) {
                throw ValidationException::withMessages(['ids' => 'One or more selected rates are no longer available.']);
            }

            if ($data['action'] === 'verify') {
                foreach ($rates as $rate) {
                    if (! $rate->source || (! $rate->source->document_path && ! $rate->source->source_url)) {
                        throw ValidationException::withMessages(['ids' => 'Every selected rate must have an official source document or URL before bulk verification.']);
                    }
                }
            }

            foreach ($rates as $rate) {
                $old = $rate->getAttributes();

                if ($data['action'] === 'verify') {
                    $rate->update(['verification_status' => 'verified', 'status' => 'published', 'verified_by' => $request->user()->id, 'verified_at' => now()]);
                    $this->audit($request, $rate, 'bulk_verified_and_published', $old, $rate->fresh()->getAttributes(), $data['reason']);
                } elseif ($data['action'] === 'reject') {
                    $rate->update(['verification_status' => 'rejected', 'status' => 'draft', 'verified_by' => $request->user()->id, 'verified_at' => now()]);
                    $this->audit($request, $rate, 'bulk_rejected', $old, $rate->fresh()->getAttributes(), $data['reason']);
                } elseif ($data['action'] === 'update_rate') {
                    $version = ((int) $rate->versions()->max('version_number')) + 1;
                    CircleRateVersion::query()->create([
                        'circle_rate_id' => $rate->id,
                        'version_number' => $version,
                        'snapshot' => $old,
                        'change_reason' => $data['reason'],
                        'changed_by' => $request->user()->id,
                    ]);
                    $rate->update(['rate' => $data['new_rate'], 'verification_status' => 'pending', 'status' => 'draft', 'verified_by' => null, 'verified_at' => null]);
                    $this->audit($request, $rate, 'bulk_rate_updated', $old, $rate->fresh()->getAttributes(), $data['reason']);
                } else {
                    $rate->delete();
                    $this->audit($request, $rate, 'bulk_archived', $old, ['deleted_at' => now()->toDateTimeString()], $data['reason']);
                }
            }

            return $rates->count();
        });

        CircleRateCache::bumpVersion();

        return back()->with('success', $count.' circle rate record(s) processed.');
    }

    private function validated(Request $request): array
    {
        $data = Validator::make($request->all(), [
            'district_id' => ['required', 'integer', 'exists:circle_rate_districts,id'],
            'tehsil_id' => ['nullable', 'integer', 'exists:circle_rate_tehsils,id'],
            'location_id' => ['nullable', 'integer', 'exists:circle_rate_locations,id'],
            'area_type_id' => ['nullable', 'integer', 'exists:circle_rate_area_types,id'],
            'property_type_id' => ['required', 'integer', 'exists:circle_rate_property_types,id'],
            'category_id' => ['nullable', 'integer', 'exists:circle_rate_property_categories,id'],
            'road_width_range_id' => ['nullable', 'integer', 'exists:circle_rate_road_width_ranges,id'],
            'financial_year_id' => ['required', 'integer', 'exists:circle_rate_financial_years,id'],
            'unit_id' => ['required', 'integer', 'exists:circle_rate_units,id'],
            'source_id' => ['nullable', 'integer', 'exists:circle_rate_sources,id'],
            'rate' => ['required', 'numeric', 'gt:0', 'max:9999999999999999'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:100000'],
            'effective_from' => ['required', 'date'],
            'effective_until' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'conditions' => ['nullable', 'json'],
            'admin_notes' => ['nullable', 'string', 'max:10000'],
            'change_reason' => ['nullable', 'string', 'max:2000'],
        ])->validate();

        if (! empty($data['conditions'])) {
            $data['conditions'] = json_decode($data['conditions'], true, 512, JSON_THROW_ON_ERROR);
            if (! is_array($data['conditions']) || ! array_is_list($data['conditions'])) {
                throw ValidationException::withMessages(['conditions' => 'Conditions must be a JSON array of field/operator/value objects.']);
            }
            foreach ($data['conditions'] as $condition) {
                if (! is_array($condition) || blank($condition['field'] ?? null) || ! array_key_exists('value', $condition) || ! in_array($condition['operator'] ?? 'equals', ['equals', 'not_equals', 'greater_than', 'less_than', 'greater_than_or_equal', 'less_than_or_equal', 'between', 'in'], true)) {
                    throw ValidationException::withMessages(['conditions' => 'Each condition needs a field, supported operator and value.']);
                }
                if (in_array($condition['operator'] ?? '', ['between', 'in'], true) && ! is_array($condition['value'])) {
                    throw ValidationException::withMessages(['conditions' => 'Between and In List conditions require an array value.']);
                }
                if (($condition['operator'] ?? '') === 'between' && count($condition['value']) !== 2) {
                    throw ValidationException::withMessages(['conditions' => 'Between conditions require exactly two values.']);
                }
            }
        }

        unset($data['change_reason']);
        $data['priority'] = $data['priority'] ?? 0;
        $this->validateRelationships($data);
        return $data;
    }

    private function locationMatchesIds(CircleRateLocation $location, array $allowedIds): bool
    {
        $allowed = array_map('strval', $allowedIds);
        $current = $location;
        while ($current) {
            if (in_array((string) $current->id, $allowed, true)) return true;
            $current = $current->parent;
        }
        return false;
    }

    private function validateRelationships(array $data): void
    {
        $district = CircleRateDistrict::query()->findOrFail($data['district_id']);
        $tehsil = ! empty($data['tehsil_id']) ? CircleRateTehsil::query()->findOrFail($data['tehsil_id']) : null;
        $location = ! empty($data['location_id']) ? CircleRateLocation::query()->findOrFail($data['location_id']) : null;
        $areaType = ! empty($data['area_type_id']) ? CircleRateAreaType::query()->findOrFail($data['area_type_id']) : null;
        $propertyType = CircleRatePropertyType::query()->findOrFail($data['property_type_id']);
        $financialYear = CircleRateFinancialYear::query()->findOrFail($data['financial_year_id']);
        $unit = CircleRateUnit::query()->findOrFail($data['unit_id']);

        if ($tehsil && (int) $tehsil->district_id !== (int) $district->id) {
            throw ValidationException::withMessages(['tehsil_id' => 'The selected tehsil does not belong to the selected district.']);
        }
        if ($location && (int) $location->district_id !== (int) $district->id) {
            throw ValidationException::withMessages(['location_id' => 'The selected location does not belong to the selected district.']);
        }
        if ($location && $tehsil && $location->tehsil_id && (int) $location->tehsil_id !== (int) $tehsil->id) {
            throw ValidationException::withMessages(['location_id' => 'The selected location does not belong to the selected tehsil.']);
        }
        if ($location && $areaType && $location->area_type_id && (int) $location->area_type_id !== (int) $areaType->id) {
            throw ValidationException::withMessages(['area_type_id' => 'The selected area type does not match the selected location.']);
        }
        if ($data['effective_from'] < $financialYear->starts_on->toDateString() || $data['effective_from'] > $financialYear->ends_on->toDateString()) {
            throw ValidationException::withMessages(['effective_from' => 'The effective date must fall within the selected financial year.']);
        }
        if (! empty($data['effective_until']) && $data['effective_until'] > $financialYear->ends_on->toDateString()) {
            throw ValidationException::withMessages(['effective_until' => 'The expiry date cannot extend beyond the selected financial year.']);
        }
        if ($unit->dimension !== 'area' && in_array($propertyType->calculation_strategy, ['land_area', 'agricultural_land', 'agricultural_area', 'residential_plot', 'commercial_plot', 'industrial_plot', 'mixed_use_land', 'building_area'], true)) {
            throw ValidationException::withMessages(['unit_id' => 'Area-based property types require a rate unit with the area dimension.']);
        }

        if (! empty($data['category_id'])) {
            $category = CircleRatePropertyCategory::query()->findOrFail($data['category_id']);
            if ($category->propertyTypes()->exists() && ! $category->propertyTypes()->whereKey($propertyType->id)->exists()) {
                throw ValidationException::withMessages(['category_id' => 'The selected category is not assigned to this property type.']);
            }
            if ($category->locations()->exists() && (! $location || ! $this->locationMatchesIds($location, $category->locations()->pluck('circle_rate_locations.id')->all()))) {
                throw ValidationException::withMessages(['category_id' => 'The selected category is not assigned to this location.']);
            }
        }

        if (! empty($data['road_width_range_id'])) {
            $range = CircleRateRoadWidthRange::query()->findOrFail($data['road_width_range_id']);
            $applicability = $range->applicability ?? [];
            if (! empty($applicability['property_type_ids']) && ! in_array((string) $propertyType->id, array_map('strval', $applicability['property_type_ids']), true)) {
                throw ValidationException::withMessages(['road_width_range_id' => 'The selected road-width range is not applicable to this property type.']);
            }
            if (! empty($applicability['location_ids']) && (! $location || ! $this->locationMatchesIds($location, $applicability['location_ids']))) {
                throw ValidationException::withMessages(['road_width_range_id' => 'The selected road-width range is not applicable to this location.']);
            }
        }
    }

    private function audit(Request $request, CircleRate $rate, string $event, ?array $old, ?array $new, ?string $reason): void
    {
        CircleRateAuditLog::query()->create([
            'user_id' => $request->user()->id,
            'subject_type' => CircleRate::class,
            'subject_id' => $rate->id,
            'event' => $event,
            'old_values' => $old,
            'new_values' => $new,
            'reason' => $reason,
            'source_id' => $rate->source_id,
            'ip_address' => $request->ip(),
        ]);
    }
}
