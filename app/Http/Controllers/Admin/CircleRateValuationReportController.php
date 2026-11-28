<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CircleRateFinancialYear;
use App\Models\CircleRateValuationRecord;
use Illuminate\Http\Request;

class CircleRateValuationReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'financial_year_id' => ['nullable', 'integer'],
            'from' => ['nullable', 'date'],
            'until' => ['nullable', 'date', 'after_or_equal:from'],
            'reference' => ['nullable', 'string', 'max:36'],
        ]);

        $records = CircleRateValuationRecord::query()->with(['rate.district', 'rate.propertyType', 'rate.location', 'financialYear'])
            ->when($filters['financial_year_id'] ?? null, fn ($q, $id) => $q->where('financial_year_id', $id))
            ->when($filters['from'] ?? null, fn ($q, $date) => $q->whereDate('valued_at', '>=', $date))
            ->when($filters['until'] ?? null, fn ($q, $date) => $q->whereDate('valued_at', '<=', $date))
            ->when($filters['reference'] ?? null, fn ($q, $reference) => $q->where('reference', 'like', '%'.$reference.'%'))
            ->latest('valued_at')->paginate(30)->withQueryString();

        return view('admin.circle-rates.reports.index', [
            'records' => $records,
            'filters' => $filters,
            'financialYears' => CircleRateFinancialYear::query()->orderByDesc('starts_on')->get(),
        ]);
    }

    public function export(Request $request)
    {
        $filters = $request->validate([
            'financial_year_id' => ['nullable', 'integer'],
            'from' => ['nullable', 'date'],
            'until' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $query = CircleRateValuationRecord::query()->with(['rate.district', 'rate.propertyType', 'financialYear'])
            ->when($filters['financial_year_id'] ?? null, fn ($q, $id) => $q->where('financial_year_id', $id))
            ->when($filters['from'] ?? null, fn ($q, $date) => $q->whereDate('valued_at', '>=', $date))
            ->when($filters['until'] ?? null, fn ($q, $date) => $q->whereDate('valued_at', '<=', $date));

        return response()->streamDownload(function () use ($query): void {
            $output = fopen('php://output', 'wb');
            fputcsv($output, ['Reference', 'Financial year', 'District', 'Property type', 'Area', 'Rate', 'Estimated value', 'Previous value', 'Difference', 'Valued at']);
            foreach ($query->orderBy('valued_at')->cursor() as $record) {
                fputcsv($output, [
                    $record->reference,
                    $record->financialYear?->name,
                    $record->rate?->district?->name,
                    $record->rate?->propertyType?->name,
                    $record->property_area,
                    $record->applicable_rate,
                    $record->estimated_value,
                    $record->previous_year_value,
                    $record->difference_amount,
                    $record->valued_at?->toDateTimeString(),
                ]);
            }
            fclose($output);
        }, 'circle-rate-valuation-reports.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
