<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CircleRate;
use App\Models\CircleRateDistrict;
use App\Models\CircleRateAreaType;
use App\Models\CircleRateFinancialYear;
use App\Models\CircleRateImportBatch;
use App\Models\CircleRateLocation;
use App\Models\CircleRatePropertyType;
use App\Models\CircleRateTehsil;
use Illuminate\Http\Request;

class CircleRateDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $filters = $request->validate([
            'financial_year_id' => ['nullable', 'integer'],
            'tehsil_id' => ['nullable', 'integer'],
            'area_type_id' => ['nullable', 'integer'],
            'property_type_id' => ['nullable', 'integer'],
            'verification_status' => ['nullable', 'in:pending,verified,rejected'],
        ]);
        $year = CircleRateFinancialYear::query()->where('is_current', true)->where('is_active', true)->first();
        $rateQuery = CircleRate::query()
            ->when($filters['financial_year_id'] ?? null, fn ($q, $id) => $q->where('financial_year_id', $id))
            ->when($filters['tehsil_id'] ?? null, fn ($q, $id) => $q->where('tehsil_id', $id))
            ->when($filters['area_type_id'] ?? null, fn ($q, $id) => $q->where('area_type_id', $id))
            ->when($filters['property_type_id'] ?? null, fn ($q, $id) => $q->where('property_type_id', $id))
            ->when($filters['verification_status'] ?? null, fn ($q, $status) => $q->where('verification_status', $status));

        return view('admin.circle-rates.dashboard', [
            'currentYear' => $year,
            'districtCount' => CircleRateDistrict::query()->count(),
            'tehsilCount' => CircleRateTehsil::query()->count(),
            'locationCount' => CircleRateLocation::query()->count(),
            'propertyTypeCount' => CircleRatePropertyType::query()->count(),
            'rateCount' => (clone $rateQuery)->count(),
            'verifiedRateCount' => (clone $rateQuery)->where('verification_status', 'verified')->where('status', 'published')->count(),
            'pendingRateCount' => (clone $rateQuery)->where('verification_status', 'pending')->count(),
            'latestRates' => (clone $rateQuery)->with(['district:id,name', 'location:id,name', 'propertyType:id,name', 'financialYear:id,name', 'unit:id,name'])
                ->latest()->limit(8)->get(),
            'filters' => $filters,
            'financialYears' => CircleRateFinancialYear::query()->where('is_active', true)->orderByDesc('starts_on')->get(),
            'tehsils' => CircleRateTehsil::query()->where('is_active', true)->orderBy('name')->get(),
            'areaTypes' => CircleRateAreaType::query()->where('is_active', true)->orderBy('name')->get(),
            'propertyTypes' => CircleRatePropertyType::query()->where('is_active', true)->orderBy('name')->get(),
            'recentImports' => CircleRateImportBatch::query()->latest()->limit(5)->get(),
        ]);
    }
}
