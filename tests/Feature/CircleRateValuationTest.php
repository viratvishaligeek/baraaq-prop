<?php

namespace Tests\Feature;

use App\Models\CircleRate;
use App\Models\CircleRateDistrict;
use App\Models\CircleRateFinancialYear;
use App\Models\CircleRatePropertyType;
use App\Models\CircleRateSource;
use App\Models\CircleRateUnit;
use App\Services\CircleRate\SpreadsheetReader;
use App\Services\CircleRate\ValuationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
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
        ]);

        return [$district, $propertyType, $year, $unit, $source];
    }
}
