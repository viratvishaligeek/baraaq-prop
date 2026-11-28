<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\CircleRateAreaType;
use App\Models\CircleRateFinancialYear;
use App\Models\CircleRateLocation;
use App\Models\CircleRatePropertyCategory;
use App\Models\CircleRatePropertyType;
use App\Models\CircleRateRoadWidthRange;
use App\Models\CircleRateTehsil;
use App\Models\CircleRateUnit;
use App\Models\CircleRateValuationRecord;
use App\Models\CircleRateDistrict;
use App\Services\CircleRate\ValuationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CircleRateCalculatorController extends Controller
{
    public function index()
    {
        return view('frontend.circle-rates.calculator', [
            'districts' => CircleRateDistrict::query()->where('is_active', true)->orderBy('name')->get(),
            'areaTypes' => CircleRateAreaType::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'propertyTypes' => CircleRatePropertyType::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'financialYears' => CircleRateFinancialYear::query()->where('is_active', true)->where('show_on_frontend', true)->orderByDesc('starts_on')->get(),
            'units' => CircleRateUnit::query()->where('is_active', true)->where('dimension', 'area')->orderBy('name')->get(),
            'fields' => \App\Models\CircleRateCalculatorField::query()->where('is_active', true)->with('options')->orderBy('sort_order')->get(),
        ]);
    }

    public function tehsils(Request $request)
    {
        $data = $request->validate(['district_id' => ['required', 'integer', 'exists:circle_rate_districts,id']]);
        return response()->json(CircleRateTehsil::query()->where('district_id', $data['district_id'])->where('is_active', true)->orderBy('name')->get(['id', 'name']));
    }

    public function locations(Request $request)
    {
        $data = $request->validate([
            'district_id' => ['required', 'integer', 'exists:circle_rate_districts,id'],
            'tehsil_id' => ['nullable', 'integer', 'exists:circle_rate_tehsils,id'],
            'area_type_id' => ['nullable', 'integer', 'exists:circle_rate_area_types,id'],
            'parent_id' => ['nullable', 'integer', 'exists:circle_rate_locations,id'],
        ]);

        return response()->json(CircleRateLocation::query()
            ->where('district_id', $data['district_id'])
            ->when($data['tehsil_id'] ?? null, fn ($q, $id) => $q->where('tehsil_id', $id))
            ->when($data['area_type_id'] ?? null, fn ($q, $id) => $q->where('area_type_id', $id))
            ->when($request->has('parent_id'), fn ($q) => $q->where('parent_id', $data['parent_id'] ?? null), fn ($q) => $q->whereNull('parent_id'))
            ->where('is_active', true)->orderBy('name')->get(['id', 'name', 'level_id', 'parent_id']));
    }

    public function categories(Request $request)
    {
        $data = $request->validate([
            'property_type_id' => ['required', 'integer', 'exists:circle_rate_property_types,id'],
            'location_id' => ['nullable', 'integer', 'exists:circle_rate_locations,id'],
        ]);

        $categories = CircleRatePropertyCategory::query()
            ->where('is_active', true)
            ->whereHas('propertyTypes', fn ($q) => $q->whereKey($data['property_type_id']))
            ->when($data['location_id'] ?? null, function ($q, $locationId): void {
                $q->where(function ($match) use ($locationId): void {
                    $match->whereDoesntHave('locations')->orWhereHas('locations', fn ($locations) => $locations->whereKey($locationId));
                });
            })
            ->orderByDesc('priority')->orderBy('name')->get(['id', 'name']);

        return response()->json($categories);
    }

    public function roadWidths(Request $request)
    {
        $data = $request->validate(['property_type_id' => ['required', 'integer', 'exists:circle_rate_property_types,id']]);
        $today = now()->toDateString();

        return response()->json(CircleRateRoadWidthRange::query()
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('effective_from')->orWhereDate('effective_from', '<=', $today))
            ->where(fn ($q) => $q->whereNull('effective_until')->orWhereDate('effective_until', '>=', $today))
            ->orderByDesc('priority')->orderBy('name')->get(['id', 'name']));
    }

    public function calculate(Request $request, ValuationService $service)
    {
        $input = $request->validate([
            'district_id' => ['required', 'integer'],
            'tehsil_id' => ['nullable', 'integer'],
            'area_type_id' => ['nullable', 'integer'],
            'location_id' => ['nullable', 'integer'],
            'property_type_id' => ['required', 'integer'],
            'category_id' => ['nullable', 'integer'],
            'road_width_range_id' => ['nullable', 'integer'],
            'financial_year_id' => ['required', 'integer'],
            'area' => ['required', 'numeric', 'gt:0', 'max:100000000'],
            'area_unit_id' => ['required', 'integer'],
            'valuation_date' => ['required', 'date'],
            'custom' => ['nullable', 'array'],
        ]);

        $result = $service->calculate($input);
        $rate = $result['rate'];

        $record = CircleRateValuationRecord::query()->create([
            'reference' => (string) Str::uuid(),
            'circle_rate_id' => $rate->id,
            'previous_circle_rate_id' => null,
            'financial_year_id' => $result['financial_year']->id,
            'input_data' => $input,
            'calculation_breakdown' => $result['breakdown'],
            'property_area' => $input['area'],
            'area_unit_id' => $input['area_unit_id'],
            'applicable_rate' => $rate->rate,
            'estimated_value' => $result['estimated_value'],
            'previous_year_value' => $result['previous_year']['estimated_value'] ?? null,
            'difference_amount' => $result['difference_amount'],
            'difference_percent' => $result['difference_percent'],
            'result_status' => 'calculated',
            'valued_at' => now(),
        ]);

        return redirect()->route('circle-rates.result', $record->reference);
    }

    public function result(string $reference)
    {
        $record = CircleRateValuationRecord::query()->with([
            'rate.district', 'rate.tehsil', 'rate.location', 'rate.propertyType',
            'rate.category', 'rate.unit', 'rate.source', 'financialYear',
        ])->where('reference', $reference)->firstOrFail();

        return view('frontend.circle-rates.result', compact('record'));
    }
}
