<?php

namespace Tests\Feature;

use App\Models\CircleRate;
use App\Models\CircleRateDistrict;
use App\Models\CircleRateFinancialYear;
use App\Models\CircleRatePropertyType;
use App\Models\CircleRateSource;
use App\Models\CircleRateUnit;
use App\Models\CircleRateValuationRecord;
use App\Services\CircleRate\DemoDataManagementService;
use App\Services\CircleRate\ValuationService;
use Database\Seeders\BareillyCityDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class BareillyCityDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_bareilly_city_demo_seeder_is_complete_and_idempotent(): void
    {
        app(BareillyCityDemoSeeder::class)->run();
        $first = app(DemoDataManagementService::class)->statistics();

        app(BareillyCityDemoSeeder::class)->run();
        $second = app(DemoDataManagementService::class)->statistics();

        $this->assertSame($first, $second);
        $this->assertSame(7, CircleRatePropertyType::query()->where('is_demo', true)->count());
        $this->assertSame(8, \App\Models\CircleRatePropertyCategory::query()->where('is_demo', true)->count());
        $this->assertSame(5, CircleRateUnit::query()->where('is_demo', true)->count());
        $this->assertSame(5, \App\Models\CircleRateRoadWidthRange::query()->where('is_demo', true)->count());
        $this->assertSame(2, CircleRateFinancialYear::query()->where('is_demo', true)->count());
        $this->assertSame(7, CircleRate::query()->where('is_demo', true)->distinct('property_type_id')->count('property_type_id'));
        $this->assertSame(46, CircleRate::query()->where('is_demo', true)->count());
        $this->assertSame(46, \App\Models\CircleRateVersion::query()->where('is_demo', true)->count());
        $this->assertSame(20, \App\Models\CircleRateUnitConversion::query()->where('is_demo', true)->count());
        $this->assertSame(9, \App\Models\CircleRateRule::query()->where('is_demo', true)->count());
        $this->assertSame(12, \App\Models\CircleRateCalculatorField::query()->where('is_demo', true)->count());
        $this->assertGreaterThan(0, \App\Models\CircleRateCalculatorFieldOption::query()->where('is_demo', true)->count());
        $this->assertSame(1, \App\Models\CircleRateImportBatch::query()->where('is_demo', true)->count());

        $this->assertSame(
            ['pending'],
            CircleRate::query()->where('is_demo', true)->distinct()->pluck('verification_status')->all()
        );
        $this->assertSame(0, CircleRate::query()->where('is_demo', true)->where('verification_status', 'verified')->count());
        $this->assertSame(0, CircleRate::query()->where('is_demo', true)->where('status', 'draft')->count());

        $this->assertSame(
            ['checkbox', 'date', 'dropdown', 'multi-select', 'number', 'radio', 'select', 'text'],
            \App\Models\CircleRateCalculatorField::query()->where('is_demo', true)->distinct()->orderBy('input_type')->pluck('input_type')->all()
        );
        $this->assertGreaterThanOrEqual(14, \App\Models\CircleRateLocation::query()->where('is_demo', true)->whereNull('parent_id')->count());
        $this->assertSame('Bareilly', CircleRateDistrict::query()->where('slug', 'bareilly')->value('name'));
    }

    public function test_demo_valuation_uses_demo_only_rules_and_compares_the_previous_demo_year(): void
    {
        app(BareillyCityDemoSeeder::class)->run();

        $rate = CircleRate::query()->where('is_demo', true)
            ->whereHas('propertyType', fn ($query) => $query->where('slug', 'demo-residential_plot'))
            ->whereHas('financialYear', fn ($query) => $query->where('name', '2026-27 (DEMO)'))
            ->with(['propertyType', 'financialYear'])->firstOrFail();

        $result = app(ValuationService::class)->calculate([
            'district_id' => $rate->district_id,
            'tehsil_id' => $rate->tehsil_id,
            'area_type_id' => $rate->area_type_id,
            'location_id' => $rate->location_id,
            'property_type_id' => $rate->property_type_id,
            'category_id' => $rate->category_id,
            'road_width_range_id' => $rate->road_width_range_id,
            'financial_year_id' => $rate->financial_year_id,
            'area' => 1000,
            'area_unit_id' => CircleRateUnit::query()->where('slug', 'demo-square-foot')->value('id'),
            'valuation_date' => '2026-08-01',
            'custom' => [],
        ]);

        $this->assertTrue($result['breakdown']['is_demo']);
        $this->assertSame('pending', $result['breakdown']['verification_status']);
        $this->assertNotNull($result['previous_year']);
        $this->assertNotEmpty($result['breakdown']['applied_rules']);
    }

    public function test_demo_reset_preserves_official_and_manually_managed_records(): void
    {
        app(BareillyCityDemoSeeder::class)->run();

        $district = CircleRateDistrict::query()->where('slug', 'bareilly')->firstOrFail();
        $type = CircleRatePropertyType::query()->create([
            'name' => 'Manually Managed Official Type', 'slug' => 'manual-official-type',
            'calculation_strategy' => 'land_area', 'is_active' => true,
        ]);
        $year = CircleRateFinancialYear::query()->create([
            'name' => '2030-31', 'starts_on' => '2030-04-01', 'ends_on' => '2031-03-31',
            'is_active' => true, 'show_on_frontend' => true,
        ]);
        $unit = CircleRateUnit::query()->create([
            'name' => 'Manual Square Metre', 'symbol' => 'm2', 'slug' => 'manual-square-metre',
            'dimension' => 'area', 'is_active' => true,
        ]);
        $source = CircleRateSource::query()->create([
            'title' => 'Manually managed source', 'reference_number' => 'MANUAL-OFFICIAL-1',
            'source_url' => 'https://example.org/manual-source',
        ]);
        $officialRate = CircleRate::query()->create([
            'district_id' => $district->id, 'property_type_id' => $type->id,
            'financial_year_id' => $year->id, 'unit_id' => $unit->id, 'source_id' => $source->id,
            'rate' => 1234, 'effective_from' => '2030-04-01',
            'verification_status' => 'pending', 'status' => 'draft',
        ]);

        app(DemoDataManagementService::class)->reset();

        $this->assertDatabaseHas('circle_rates', ['id' => $officialRate->id, 'is_demo' => false]);
        $this->assertDatabaseHas('circle_rate_sources', ['id' => $source->id, 'is_demo' => false]);
        $this->assertDatabaseHas('circle_rate_property_types', ['id' => $type->id, 'is_demo' => false]);
        $this->assertSame(0, CircleRate::query()->where('is_demo', true)->count());
        $this->assertSame(0, CircleRateSource::query()->where('is_demo', true)->count());
        $this->assertDatabaseHas('circle_rate_districts', ['id' => $district->id]);
        $this->assertDatabaseHas('circle_rate_audit_logs', ['event' => 'demo_reset']);
    }

    public function test_demo_valuation_result_displays_non_official_warning(): void
    {
        app(BareillyCityDemoSeeder::class)->run();
        $rate = CircleRate::query()->where('is_demo', true)->with('financialYear')->firstOrFail();
        $reference = (string) Str::uuid();

        CircleRateValuationRecord::query()->create([
            'reference' => $reference, 'circle_rate_id' => $rate->id,
            'financial_year_id' => $rate->financial_year_id,
            'input_data' => ['demo' => true], 'property_area' => 100,
            'applicable_rate' => $rate->rate, 'estimated_value' => 12345,
            'result_status' => 'calculated', 'valued_at' => now(), 'is_demo' => true,
        ]);

        $response = $this->withSession(['circle_rate_last_reference' => $reference])
            ->get(route('circle-rates.result', $reference));

        $response->assertOk();
        $response->assertSee('DEMO MODE – This result is calculated using sample testing data and is NOT an official government circle-rate valuation.');
    }
}
