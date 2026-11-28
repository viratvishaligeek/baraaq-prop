<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CircleRate;
use App\Models\CircleRateDistrict;
use App\Models\CircleRateFinancialYear;
use App\Models\CircleRateImportBatch;
use App\Models\CircleRateLocation;
use App\Models\CircleRatePropertyType;
use App\Models\CircleRateTehsil;

class CircleRateDashboardController extends Controller
{
    public function __invoke()
    {
        $year = CircleRateFinancialYear::query()->where('is_current', true)->where('is_active', true)->first();

        return view('admin.circle-rates.dashboard', [
            'currentYear' => $year,
            'districtCount' => CircleRateDistrict::query()->count(),
            'tehsilCount' => CircleRateTehsil::query()->count(),
            'locationCount' => CircleRateLocation::query()->count(),
            'propertyTypeCount' => CircleRatePropertyType::query()->count(),
            'rateCount' => CircleRate::query()->count(),
            'verifiedRateCount' => CircleRate::query()->where('verification_status', 'verified')->where('status', 'published')->count(),
            'pendingRateCount' => CircleRate::query()->where('verification_status', 'pending')->count(),
            'latestRates' => CircleRate::query()->with(['district:id,name', 'location:id,name', 'propertyType:id,name', 'financialYear:id,name', 'unit:id,name'])
                ->latest()->limit(8)->get(),
            'recentImports' => CircleRateImportBatch::query()->latest()->limit(5)->get(),
        ]);
    }
}
