<?php

namespace App\Services\CircleRate;

use App\Models\CircleRate;
use App\Models\CircleRateFinancialYear;
use App\Models\CircleRateUnitConversion;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class ValuationService
{
    public function calculate(array $input): array
    {
        $data = Validator::make($input, [
            'district_id' => ['required', 'integer', 'exists:circle_rate_districts,id'],
            'property_type_id' => ['required', 'integer', 'exists:circle_rate_property_types,id'],
            'financial_year_id' => ['required', 'integer', 'exists:circle_rate_financial_years,id'],
            'area' => ['required', 'numeric', 'gt:0'],
            'area_unit_id' => ['required', 'integer', 'exists:circle_rate_units,id'],
            'valuation_date' => ['required', 'date'],
            'tehsil_id' => ['nullable', 'integer', 'exists:circle_rate_tehsils,id'],
            'location_id' => ['nullable', 'integer', 'exists:circle_rate_locations,id'],
            'area_type_id' => ['nullable', 'integer', 'exists:circle_rate_area_types,id'],
            'category_id' => ['nullable', 'integer', 'exists:circle_rate_property_categories,id'],
            'road_width_range_id' => ['nullable', 'integer', 'exists:circle_rate_road_width_ranges,id'],
        ])->validate();

        $date = Carbon::parse($data['valuation_date'])->toDateString();
        $year = CircleRateFinancialYear::query()->whereKey($data['financial_year_id'])
            ->where('is_active', true)->first();

        if (! $year || $date < $year->starts_on->toDateString() || $date > $year->ends_on->toDateString()) {
            throw ValidationException::withMessages([
                'financial_year_id' => 'The valuation date must fall within the selected active financial year.',
            ]);
        }

        $rate = $this->resolveRate($data, $date);
        $valuation = $this->applyStrategy($rate, (float) $data['area'], (int) $data['area_unit_id']);
        $previous = $this->previousYearValuation($data, $year, (float) $data['area']);

        $previousValue = $previous['estimated_value'] ?? null;
        $difference = $previousValue === null ? null : round($valuation['estimated_value'] - $previousValue, 2);
        $percentage = ($previousValue === null || $previousValue == 0)
            ? null
            : round(($difference / $previousValue) * 100, 4);

        return [
            'rate' => $rate->load(['district', 'tehsil', 'location', 'areaType', 'propertyType', 'category', 'roadWidthRange', 'financialYear', 'unit', 'source']),
            'financial_year' => $year,
            'estimated_value' => $valuation['estimated_value'],
            'previous_year' => $previous,
            'difference_amount' => $difference,
            'difference_percent' => $percentage,
            'breakdown' => [
                'formula' => $valuation['formula'],
                'input_area' => (float) $data['area'],
                'input_unit_id' => (int) $data['area_unit_id'],
                'converted_area' => $valuation['converted_area'],
                'rate' => (float) $rate->rate,
                'rate_unit' => $rate->unit->name,
                'applied_conditions' => $rate->conditions ?? [],
                'source' => $rate->source?->title,
                'verification_status' => $rate->verification_status,
                'valuation_date' => $date,
            ],
        ];
    }

    private function resolveRate(array $data, string $date): CircleRate
    {
        $optionalDimensions = [
            'tehsil_id', 'location_id', 'area_type_id',
            'category_id', 'road_width_range_id',
        ];

        $query = CircleRate::query()
            ->with(['unit', 'source'])
            ->publishedAndVerified()
            ->where('district_id', $data['district_id'])
            ->where('property_type_id', $data['property_type_id'])
            ->where('financial_year_id', $data['financial_year_id'])
            ->whereDate('effective_from', '<=', $date)
            ->where(fn ($q) => $q->whereNull('effective_until')->orWhereDate('effective_until', '>=', $date));

        foreach ($optionalDimensions as $dimension) {
            $selected = $data[$dimension] ?? null;
            $query->where(function ($q) use ($dimension, $selected): void {
                $q->whereNull($dimension);
                if ($selected !== null) {
                    $q->orWhere($dimension, $selected);
                }
            });
        }

        $candidates = $query->get()
            ->filter(fn (CircleRate $rate) => $this->conditionsMatch($rate->conditions ?? [], $data))
            ->map(function (CircleRate $rate) use ($data): CircleRate {
                $specificity = 0;
                foreach (['tehsil_id', 'location_id', 'area_type_id', 'category_id', 'road_width_range_id'] as $field) {
                    if (($rate->{$field} ?? null) !== null && (string) $rate->{$field} === (string) ($data[$field] ?? '')) {
                        $specificity++;
                    }
                }
                $rate->setAttribute('_match_specificity', $specificity);
                return $rate;
            })
            ->sortByDesc(fn (CircleRate $rate) => [(int) $rate->priority, (int) $rate->getAttribute('_match_specificity')])
            ->values();

        if ($candidates->isEmpty()) {
            throw ValidationException::withMessages([
                'property_type_id' => 'No published, verified circle rate matches the selected property, location, conditions and valuation date.',
            ]);
        }

        $first = $candidates->first();
        $top = $candidates->filter(fn (CircleRate $rate) =>
            (int) $rate->priority === (int) $first->priority
            && (int) $rate->getAttribute('_match_specificity') === (int) $first->getAttribute('_match_specificity')
        );

        if ($top->count() > 1) {
            throw ValidationException::withMessages([
                'property_type_id' => 'More than one equally specific verified rate matches. An administrator must resolve the conflicting rate records.',
            ]);
        }

        return $first;
    }

    private function conditionsMatch(array $conditions, array $input): bool
    {
        foreach ($conditions as $condition) {
            if (! is_array($condition) || empty($condition['field']) || ! array_key_exists('value', $condition)) {
                return false;
            }

            $actual = data_get($input, $condition['field']);
            $expected = $condition['value'];
            $operator = $condition['operator'] ?? 'equals';

            $matched = match ($operator) {
                'equals' => (string) $actual === (string) $expected,
                'not_equals' => (string) $actual !== (string) $expected,
                'greater_than' => is_numeric($actual) && is_numeric($expected) && (float) $actual > (float) $expected,
                'less_than' => is_numeric($actual) && is_numeric($expected) && (float) $actual < (float) $expected,
                'greater_than_or_equal' => is_numeric($actual) && is_numeric($expected) && (float) $actual >= (float) $expected,
                'less_than_or_equal' => is_numeric($actual) && is_numeric($expected) && (float) $actual <= (float) $expected,
                'between' => is_numeric($actual) && is_array($expected) && count($expected) === 2
                    && (float) $actual >= (float) $expected[0] && (float) $actual <= (float) $expected[1],
                'in' => is_array($expected) && in_array((string) $actual, array_map('strval', $expected), true),
                default => false,
            };

            if (! $matched) {
                return false;
            }
        }

        return true;
    }

    private function applyStrategy(CircleRate $rate, float $area, int $inputUnitId): array
    {
        $strategy = $rate->propertyType->calculation_strategy;

        if ($strategy !== 'land_area') {
            throw new RuntimeException("The '{$strategy}' valuation strategy is not configured for production calculations yet.");
        }

        $convertedArea = $this->convertArea($area, $inputUnitId, (int) $rate->unit_id);
        $value = round($convertedArea * (float) $rate->rate, 2);

        return [
            'converted_area' => $convertedArea,
            'estimated_value' => $value,
            'formula' => 'converted property area × verified circle rate',
        ];
    }

    private function convertArea(float $area, int $fromUnitId, int $toUnitId): float
    {
        if ($fromUnitId === $toUnitId) {
            return $area;
        }

        $conversion = CircleRateUnitConversion::query()
            ->where('from_unit_id', $fromUnitId)
            ->where('to_unit_id', $toUnitId)
            ->where('is_verified', true)
            ->first();

        if ($conversion) {
            return $area * (float) $conversion->factor;
        }

        $reverse = CircleRateUnitConversion::query()
            ->where('from_unit_id', $toUnitId)
            ->where('to_unit_id', $fromUnitId)
            ->where('is_verified', true)
            ->first();

        if ($reverse && (float) $reverse->factor > 0) {
            return $area / (float) $reverse->factor;
        }

        throw ValidationException::withMessages([
            'area_unit_id' => 'No administrator-verified conversion exists between the selected area unit and the rate unit.',
        ]);
    }

    private function previousYearValuation(array $data, CircleRateFinancialYear $year, float $area): ?array
    {
        $previousYear = CircleRateFinancialYear::query()
            ->where('is_active', true)
            ->whereDate('ends_on', '<', $year->starts_on->toDateString())
            ->orderByDesc('ends_on')
            ->first();

        if (! $previousYear) {
            return null;
        }

        $previousDate = min($previousYear->ends_on->toDateString(), $data['valuation_date']);
        $previousInput = array_merge($data, ['financial_year_id' => $previousYear->id]);
        try {
            $previousRate = $this->resolveRate($previousInput, $previousDate);
            $result = $this->applyStrategy($previousRate, $area, (int) $data['area_unit_id']);
        } catch (ValidationException|RuntimeException) {
            return null;
        }

        return [
            'financial_year' => $previousYear->name,
            'rate' => $previousRate->rate,
            'unit' => $previousRate->unit->name,
            'estimated_value' => $result['estimated_value'],
        ];
    }
}
