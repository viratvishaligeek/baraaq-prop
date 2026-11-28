<?php

namespace Tests\Feature;

use App\Models\CircleRate;
use App\Models\CircleRateAreaType;
use App\Models\CircleRateDistrict;
use App\Models\CircleRateFinancialYear;
use App\Models\CircleRatePropertyType;
use App\Models\CircleRateSource;
use App\Models\CircleRateRule;
use App\Models\CircleRateUnitConversion;
use App\Models\CircleRateUnit;
use App\Services\CircleRate\SpreadsheetReader;
use App\Services\CircleRate\ValuationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Database\Seeders\CircleRateReferenceSeeder;
use Tests\TestCase;

class CircleRateValuationTest extends TestCase
{
    use RefreshDatabase;

    public function test_valuation_uses_a_published_and_verified_rate(): void
    {
        [$district, $propertyType, $year, $unit, $source] = $this->makeMasterData();

        CircleRate::query()->create([
            'district_id' => $district->id,
            'property_type_id' => $propertyType->id,
            'financial_year_id' => $year->id,
            'unit_id' => $unit->id,
            'source_id' => $source->id,
            'rate' => 5000,
            'priority' => 1,
            'effective_from' => '2026-04-01',
            'verification_status' => 'verified',
            'status' => 'published',
        ]);

        $result = app(ValuationService::class)->calculate([
            'district_id' => $district->id,
            'property_type_id' => $propertyType->id,
            'financial_year_id' => $year->id,
            'area' => 100,
            'area_unit_id' => $unit->id,
            'valuation_date' => '2026-08-01',
        ]);

        $this->assertSame(500000.0, (float) $result['estimated_value']);
        $this->assertSame('verified', $result['breakdown']['verification_status']);
        $this->assertSame($source->title, $result['breakdown']['source']);
    }

    public function test_unverified_or_draft_rates_are_not_used_for_public_valuation(): void
    {
        [$district, $propertyType, $year, $unit, $source] = $this->makeMasterData();

        CircleRate::query()->create([
            'district_id' => $district->id,
            'property_type_id' => $propertyType->id,
            'financial_year_id' => $year->id,
            'unit_id' => $unit->id,
            'source_id' => $source->id,
            'rate' => 5000,
            'effective_from' => '2026-04-01',
            'verification_status' => 'pending',
            'status' => 'draft',
        ]);

        $this->expectException(ValidationException::class);

        app(ValuationService::class)->calculate([
            'district_id' => $district->id,
            'property_type_id' => $propertyType->id,
            'financial_year_id' => $year->id,
            'area' => 100,
            'area_unit_id' => $unit->id,
            'valuation_date' => '2026-08-01',
        ]);
    }

    public function test_ambiguous_equally_specific_rates_are_rejected(): void
    {
        [$district, $propertyType, $year, $unit, $source] = $this->makeMasterData();

        foreach ([5000, 5200] as $amount) {
            CircleRate::query()->create([
                'district_id' => $district->id,
                'property_type_id' => $propertyType->id,
                'financial_year_id' => $year->id,
                'unit_id' => $unit->id,
                'source_id' => $source->id,
                'rate' => $amount,
                'effective_from' => '2026-04-01',
                'verification_status' => 'verified',
                'status' => 'published',
            ]);
        }

        $this->expectException(ValidationException::class);

        app(ValuationService::class)->calculate([
            'district_id' => $district->id,
            'property_type_id' => $propertyType->id,
            'financial_year_id' => $year->id,
            'area' => 100,
            'area_unit_id' => $unit->id,
            'valuation_date' => '2026-08-01',
        ]);
    }

    public function test_verified_source_backed_rule_is_included_in_breakdown(): void
    {
        [$district, $propertyType, $year, $unit, $source] = $this->makeMasterData();
        CircleRate::query()->create([
            'district_id' => $district->id,
            'property_type_id' => $propertyType->id,
            'financial_year_id' => $year->id,
            'unit_id' => $unit->id,
            'source_id' => $source->id,
            'rate' => 5000,
            'effective_from' => '2026-04-01',
            'verification_status' => 'verified',
            'status' => 'published',
        ]);
        CircleRateRule::query()->create([
            'name' => 'Verified source adjustment',
            'conditions' => [],
            'actions' => ['rate_multiplier' => 1.1],
            'source_id' => $source->id,
            'is_verified' => true,
            'priority' => 1,
            'stop_processing' => true,
            'is_active' => true,
        ]);

        $result = app(ValuationService::class)->calculate([
            'district_id' => $district->id,
            'property_type_id' => $propertyType->id,
            'financial_year_id' => $year->id,
            'area' => 100,
            'area_unit_id' => $unit->id,
            'valuation_date' => '2026-08-01',
        ]);

        $this->assertSame(550000.0, (float) $result['estimated_value']);
        $this->assertSame('Verified source adjustment', $result['breakdown']['applied_rules'][0]['name']);
    }

    public function test_area_is_converted_only_with_verified_unit_conversion(): void
    {
        [$district, $propertyType, $year, $squareMetre, $source] = $this->makeMasterData();
        $squareFeet = CircleRateUnit::query()->create([
            'name' => 'Square Foot',
            'symbol' => 'sq ft',
            'slug' => 'square-foot',
            'dimension' => 'area',
            'is_active' => true,
        ]);
        CircleRateUnitConversion::query()->create([
            'from_unit_id' => $squareFeet->id,
            'to_unit_id' => $squareMetre->id,
            'factor' => 0.092903,
            'is_verified' => true,
            'source_note' => 'Verified test conversion',
        ]);
        CircleRate::query()->create([
            'district_id' => $district->id,
            'property_type_id' => $propertyType->id,
            'financial_year_id' => $year->id,
            'unit_id' => $squareMetre->id,
            'source_id' => $source->id,
            'rate' => 1000,
            'effective_from' => '2026-04-01',
            'verification_status' => 'verified',
            'status' => 'published',
        ]);

        $result = app(ValuationService::class)->calculate([
            'district_id' => $district->id,
            'property_type_id' => $propertyType->id,
            'financial_year_id' => $year->id,
            'area' => 100,
            'area_unit_id' => $squareFeet->id,
            'valuation_date' => '2026-08-01',
        ]);

        $this->assertEquals(92.903, $result['breakdown']['converted_area']);
        $this->assertEquals(92903.0, $result['estimated_value']);
    }

    public function test_previous_financial_year_rate_is_compared_when_available(): void
    {
        [$district, $propertyType, $year, $unit, $source] = $this->makeMasterData();
        $previousYear = CircleRateFinancialYear::query()->create([
            'name' => '2025-26',
            'starts_on' => '2025-04-01',
            'ends_on' => '2026-03-31',
            'is_current' => false,
            'is_active' => true,
            'show_on_frontend' => true,
        ]);

        foreach ([[$previousYear, 4000, '2025-04-01'], [$year, 5000, '2026-04-01']] as [$financialYear, $amount, $effectiveFrom]) {
            CircleRate::query()->create([
                'district_id' => $district->id,
                'property_type_id' => $propertyType->id,
                'financial_year_id' => $financialYear->id,
                'unit_id' => $unit->id,
                'source_id' => $source->id,
                'rate' => $amount,
                'effective_from' => $effectiveFrom,
                'verification_status' => 'verified',
                'status' => 'published',
            ]);
        }

        $result = app(ValuationService::class)->calculate([
            'district_id' => $district->id,
            'property_type_id' => $propertyType->id,
            'financial_year_id' => $year->id,
            'area' => 100,
            'area_unit_id' => $unit->id,
            'valuation_date' => '2026-08-01',
        ]);

        $this->assertSame(400000.0, (float) $result['previous_year']['estimated_value']);
        $this->assertSame(25.0, (float) $result['difference_percent']);
    }

    public function test_reference_seeder_adds_six_tehsils_without_overwriting_admin_edits_or_rates(): void
    {
        app(CircleRateReferenceSeeder::class)->run();

        $district = CircleRateDistrict::query()->where('slug', 'bareilly')->firstOrFail();
        $this->assertSame(6, $district->tehsils()->count());
        $this->assertSame(15, $district->locations()->count());
        $this->assertSame(2, CircleRateAreaType::query()->count());
        $this->assertSame(0, CircleRate::query()->count());

        $tehsil = $district->tehsils()->where('slug', 'bareilly')->firstOrFail();
        $tehsil->update(['name' => 'Administrator edited name']);

        app(CircleRateReferenceSeeder::class)->run();

        $this->assertSame('Administrator edited name', $tehsil->fresh()->name);
        $this->assertSame(6, $district->tehsils()->count());
        $this->assertSame(15, $district->locations()->count());
    }

    public function test_csv_reader_returns_header_mapped_rows(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'circle-rate-');
        file_put_contents($path, "District,Property Type,Rate,Unit\nBareilly,Residential Plot,45000,Square Metre\n");

        try {
            $result = app(SpreadsheetReader::class)->read($path, 'csv');
            $this->assertSame(['District', 'Property Type', 'Rate', 'Unit'], $result['headers']);
            $this->assertSame('Bareilly', $result['rows'][0]['District']);
            $this->assertSame('45000', $result['rows'][0]['Rate']);
        } finally {
            @unlink($path);
        }
    }

    private function makeMasterData(): array
    {
        $district = CircleRateDistrict::query()->create([
            'name' => 'Test District',
            'slug' => 'test-district',
            'state' => 'Uttar Pradesh',
            'is_active' => true,
        ]);
        $propertyType = CircleRatePropertyType::query()->create([
            'name' => 'Residential Plot',
            'slug' => 'residential-plot',
            'calculation_strategy' => 'residential_plot',
            'is_active' => true,
        ]);
        $year = CircleRateFinancialYear::query()->create([
            'name' => '2026-27',
            'starts_on' => '2026-04-01',
            'ends_on' => '2027-03-31',
            'is_current' => true,
            'is_active' => true,
            'show_on_frontend' => true,
        ]);
        $unit = CircleRateUnit::query()->create([
            'name' => 'Square Metre',
            'symbol' => 'm²',
            'slug' => 'square-metre',
            'dimension' => 'area',
            'is_active' => true,
        ]);
        $source = CircleRateSource::query()->create([
            'title' => 'Test official rate list',
            'reference_number' => 'TEST-2026',
            'source_url' => 'https://example.org/official-test-rate-list',
        ]);

        return [$district, $propertyType, $year, $unit, $source];
    }
}
