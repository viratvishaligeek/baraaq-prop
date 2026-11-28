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
use App\Models\Setting;
use App\Models\CircleRateCalculatorField;
use Illuminate\Validation\Rule;
use App\Services\CircleRate\ValuationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CircleRateCalculatorController extends Controller
{
    public function index()
    {
        abort_unless(Setting::read('circle_rate.calculator_enabled', true), 404);

        return view('frontend.circle-rates.calculator', [
            'districts' => CircleRateDistrict::query()->where('is_active', true)->orderBy('name')->get(),
            'areaTypes' => CircleRateAreaType::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'propertyTypes' => CircleRatePropertyType::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'financialYears' => CircleRateFinancialYear::query()->where('is_active', true)->where('show_on_frontend', true)->orderByDesc('starts_on')->get(),
            'units' => CircleRateUnit::query()->where('is_active', true)->where('dimension', 'area')->orderBy('name')->get(),
            'fields' => CircleRateCalculatorField::query()->where('is_active', true)->with('options')->orderBy('sort_order')->get(),
            'disclaimer' => Setting::read('circle_rate.valuation_disclaimer', 'This is an estimate based on published, verified circle rate records. It is not a market-price appraisal or a guarantee of the final registration assessment.'),
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
            ->when($data['tehsil_id'] ?? null, fn ($q, $id) => $q->where(fn ($scope) => $scope->where('tehsil_id', $id)->orWhereNull('tehsil_id')))
            ->when($data['area_type_id'] ?? null, fn ($q, $id) => $q->where(fn ($scope) => $scope->where('area_type_id', $id)->orWhereNull('area_type_id')))
            ->when($request->has('parent_id'), fn ($q) => $q->where('parent_id', $data['parent_id'] ?? null), fn ($q) => $q->whereNull('parent_id'))
            ->where('is_active', true)->orderBy('name')->get(['id', 'name', 'level_id', 'parent_id']));
    }

    public function categories(Request $request)
    {
        $data = $request->validate([
            'property_type_id' => ['required', 'integer', 'exists:circle_rate_property_types,id'],
            'location_id' => ['nullable', 'integer', 'exists:circle_rate_locations,id'],
        ]);

        $locationIds = $this->locationAndAncestorIds($data['location_id'] ?? null);
        $categories = CircleRatePropertyCategory::query()
            ->where('is_active', true)
            ->whereHas('propertyTypes', fn ($q) => $q->whereKey($data['property_type_id']))
            ->when($locationIds, function ($q) use ($locationIds): void {
                $q->where(function ($match) use ($locationIds): void {
                    $match->whereDoesntHave('locations')->orWhereHas('locations', fn ($locations) => $locations->whereIn('circle_rate_locations.id', $locationIds));
                });
            })
            ->orderByDesc('priority')->orderBy('name')->get(['id', 'name']);

        return response()->json($categories);
    }

    public function roadWidths(Request $request)
    {
        $data = $request->validate([
            'property_type_id' => ['required', 'integer', 'exists:circle_rate_property_types,id'],
            'location_id' => ['nullable', 'integer', 'exists:circle_rate_locations,id'],
            'district_id' => ['nullable', 'integer', 'exists:circle_rate_districts,id'],
        ]);
        $today = now()->toDateString();

        $ranges = CircleRateRoadWidthRange::query()
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('effective_from')->orWhereDate('effective_from', '<=', $today))
            ->where(fn ($q) => $q->whereNull('effective_until')->orWhereDate('effective_until', '>=', $today))
            ->orderByDesc('priority')->orderBy('name')->get();

        $locationIds = $this->locationAndAncestorIds($data['location_id'] ?? null);
        $ranges = $ranges->filter(function (CircleRateRoadWidthRange $range) use ($data, $locationIds): bool {
            $applicability = $range->applicability ?? [];
            foreach (['property_type_ids' => 'property_type_id', 'location_ids' => 'location_id', 'district_ids' => 'district_id'] as $configured => $selected) {
                if (! empty($applicability[$configured])) {
                    $matches = $selected === 'location_id' ? array_intersect(array_map('strval', $locationIds), array_map('strval', $applicability[$configured])) : (in_array((string) ($data[$selected] ?? ''), array_map('strval', $applicability[$configured]), true) ? [true] : []);
                    if (! $matches) {
                        return false;
                    }
                }
            }
            return true;
        })->values()->map(fn ($range) => ['id' => $range->id, 'name' => $range->name]);

        return response()->json($ranges);
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
            'area' => ['required', 'numeric', 'gt:0', 'max:'.(float) Setting::read('circle_rate.max_property_area', 100000000)],
            'area_unit_id' => ['required', 'integer'],
            'valuation_date' => ['required', 'date'],
            'custom' => ['nullable', 'array'],
        ]);

        $customRules = [];
        $applicableFields = [];
        foreach (CircleRateCalculatorField::query()->where('is_active', true)->with('options')->get() as $field) {
            $applicability = $field->applicability ?? [];
            $matches = true;
            foreach (['district_ids' => 'district_id', 'tehsil_ids' => 'tehsil_id', 'area_type_ids' => 'area_type_id', 'location_ids' => 'location_id', 'property_type_ids' => 'property_type_id'] as $configured => $selected) {
                if (! empty($applicability[$configured]) && ! in_array((string) ($input[$selected] ?? ''), array_map('strval', $applicability[$configured]), true)) {
                    $matches = false;
                    break;
                }
            }
            if (! $matches || ! $this->customFieldVisible($field->visibility_conditions ?? [], $input)) continue;

            $key = 'custom.'.$field->name;
            $rules = [$field->is_required ? 'required' : 'nullable'];
            if (in_array($field->input_type, ['select', 'dropdown', 'radio'])) {
                $rules[] = Rule::in($field->options->pluck('value')->all());
            } elseif (in_array($field->input_type, ['number', 'range'])) {
                $rules[] = 'numeric';
            } elseif ($field->input_type === 'date') {
                $rules[] = 'date';
            } elseif ($field->input_type === 'checkbox') {
                $rules[] = 'boolean';
            } elseif ($field->input_type === 'multi-select') {
                $rules[] = 'array';
                $customRules[$key.'.*'] = [Rule::in($field->options->pluck('value')->all())];
            } else {
                $rules[] = 'string';
                $rules[] = 'max:1000';
            }
            $configuredRules = $field->validation_rules ?? [];
            if (is_array($configuredRules)) {
                foreach ($configuredRules as $ruleName => $ruleValue) {
                    if (in_array($ruleName, ['min', 'max'], true) && is_numeric($ruleValue)) {
                        $rules[] = $ruleName.':'.$ruleValue;
                    } elseif (in_array($ruleName, ['integer', 'numeric', 'email', 'url', 'date', 'alpha', 'alpha_num'], true) && $ruleValue) {
                        $rules[] = $ruleName;
                    }
                }
            }
            $customRules[$key] = $rules;
            $applicableFields[] = $field->name;
        }

        if ($customRules) {
            $customInput = $request->validate($customRules);
            $input['custom'] = collect($customInput['custom'] ?? [])->only($applicableFields)->all();
        } else {
            $input['custom'] = [];
        }

        $result = $service->calculate($input);
        $rate = $result['rate'];

        $record = CircleRateValuationRecord::query()->create([
            'reference' => (string) Str::uuid(),
            'circle_rate_id' => $rate->id,
            'previous_circle_rate_id' => $result['previous_year']['circle_rate_id'] ?? null,
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

    private function locationAndAncestorIds(?int $locationId): array
    {
        $ids = [];
        $location = $locationId ? CircleRateLocation::query()->with('parent')->find($locationId) : null;
        while ($location) {
            $ids[] = $location->id;
            $location = $location->parent;
        }
        return $ids;
    }

    private function customFieldVisible(array $conditions, array $input): bool
    {
        foreach ($conditions as $condition) {
            if (! is_array($condition) || empty($condition['field']) || ! array_key_exists('value', $condition)) {
                return false;
            }

            $actual = data_get($input, $condition['field']);
            $expected = $condition['value'];
            $matched = match ($condition['operator'] ?? 'equals') {
                'equals' => (string) $actual === (string) $expected,
                'not_equals' => (string) $actual !== (string) $expected,
                'greater_than' => is_numeric($actual) && is_numeric($expected) && (float) $actual > (float) $expected,
                'less_than' => is_numeric($actual) && is_numeric($expected) && (float) $actual < (float) $expected,
                'greater_than_or_equal' => is_numeric($actual) && is_numeric($expected) && (float) $actual >= (float) $expected,
                'less_than_or_equal' => is_numeric($actual) && is_numeric($expected) && (float) $actual <= (float) $expected,
                'between' => is_array($expected) && count($expected) === 2 && is_numeric($actual) && (float) $actual >= (float) $expected[0] && (float) $actual <= (float) $expected[1],
                'in' => is_array($expected) && in_array((string) $actual, array_map('strval', $expected), true),
                default => false,
            };
            if (! $matched) return false;
        }

        return true;
    }

    public function result(string $reference)
    {
        $record = CircleRateValuationRecord::query()->with([
            'rate.district', 'rate.tehsil', 'rate.location', 'rate.propertyType',
            'rate.category', 'rate.unit', 'rate.source', 'financialYear', 'previousRate.unit', 'previousRate.financialYear',
        ])->where('reference', $reference)->firstOrFail();

        return view('frontend.circle-rates.result', [
            'record' => $record,
            'showPreviousComparison' => Setting::read('circle_rate.show_previous_year_comparison', true),
            'allowSharing' => Setting::read('circle_rate.allow_public_result_sharing', true),
            'disclaimer' => Setting::read('circle_rate.valuation_disclaimer', 'This is an estimate based on published, verified circle rate records. It is not a market-price appraisal or a guarantee of the final registration assessment.'),
        ]);
    }
}
