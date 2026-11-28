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
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $rates = CircleRate::query()->with(['district:id,name', 'tehsil:id,name', 'location:id,name', 'propertyType:id,name', 'financialYear:id,name', 'unit:id,name', 'source:id,title'])
            ->when($filters['financial_year_id'] ?? null, fn ($q, $id) => $q->where('financial_year_id', $id))
            ->when($filters['district_id'] ?? null, fn ($q, $id) => $q->where('district_id', $id))
            ->when($filters['property_type_id'] ?? null, fn ($q, $id) => $q->where('property_type_id', $id))
            ->when($filters['verification_status'] ?? null, fn ($q, $status) => $q->where('verification_status', $status))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['q'] ?? null, fn ($q, $term) => $q->whereHas('location', fn ($l) => $l->where('name', 'like', '%'.$term.'%')))
            ->latest()->paginate(25)->withQueryString();

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

        return redirect()->route('admin.circle-rates.rates.index')->with('success', 'Rate saved as a draft. It must be verified before it can be used by the public calculator.');
    }

    public function edit(CircleRate $circleRate)
    {
        return view('admin.circle-rates.rates.form', array_merge($this->formData(), ['rate' => $circleRate]));
    }

    public function update(Request $request, CircleRate $circleRate)
    {
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

        return redirect()->route('admin.circle-rates.rates.index')->with('success', 'Rate updated. Previous values were preserved in rate history; the changed record requires verification again.');
    }

    public function verify(Request $request, CircleRate $circleRate)
    {
        abort_unless($request->user()->isAdministrator() || $request->user()->hasPermission('circle-rates.verify'), 403);
        if (! $circleRate->source_id) {
            return back()->withErrors(['source_id' => 'Attach an official source document before verifying this rate.']);
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

        return redirect()->route('admin.circle-rates.rates.edit', $copy)->with('success', 'Rate duplicated as an unverified draft.');
    }

    public function destroy(Request $request, CircleRate $circleRate)
    {
        $old = $circleRate->getAttributes();
        $circleRate->delete();
        $this->audit($request, $circleRate, 'archived', $old, ['deleted_at' => now()->toDateTimeString()], $request->input('reason'));

        return back()->with('success', 'Rate archived. Historical valuation records remain linked to its rate ID.');
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
            if (! is_array($data['conditions'])) {
                throw ValidationException::withMessages(['conditions' => 'Conditions must be a JSON array or object.']);
            }
        }

        unset($data['change_reason']);
        $data['priority'] = $data['priority'] ?? 0;
        return $data;
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
