<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CircleRate;
use App\Models\CircleRateAuditLog;
use App\Models\CircleRateSource;
use App\Services\CircleRate\DemoDataManagementService;
use Database\Seeders\BareillyCityDemoSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CircleRateDemoDataController extends Controller
{
    public function index(DemoDataManagementService $service)
    {
        $source = CircleRateSource::query()->where('is_demo', true)->where('reference_number', 'DEMO-BR-CITY-2026')->first();
        $rates = CircleRate::query()->where('is_demo', true)
            ->with(['location:id,name', 'propertyType:id,name', 'financialYear:id,name', 'unit:id,name'])
            ->latest()->paginate(20);

        return view('admin.circle-rates.demo.index', [
            'stats' => $service->statistics(), 'source' => $source, 'rates' => $rates,
            'total' => array_sum($service->statistics()),
        ]);
    }

    public function seed(Request $request)
    {
        DB::transaction(function (): void {
            app(BareillyCityDemoSeeder::class)->run();
            CircleRateAuditLog::query()->create([
                'user_id' => auth()->id(), 'subject_type' => self::class, 'event' => 'demo_seed_requested',
                'new_values' => ['scope' => 'Bareilly city only'], 'reason' => 'Authorized admin requested Bareilly city demo data seeding.',
                'ip_address' => request()->ip(),
            ]);
        });

        return redirect()->route('admin.circle-rates.demo.index')->with('success', 'Bareilly city DEMO data seeded. All sample rates remain pending and are visibly marked DEMO.');
    }

    public function reset(Request $request, DemoDataManagementService $service)
    {
        $data = $request->validate(['confirmation' => ['required', 'in:RESET DEMO DATA']]);
        $counts = $service->reset($request->user()->id);

        return redirect()->route('admin.circle-rates.demo.index')->with('success', 'Demo records were reset. Official and manually managed records were not targeted.');
    }
}
