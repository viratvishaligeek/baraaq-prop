<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            'circle_rate_districts', 'circle_rate_tehsils', 'circle_rate_area_types',
            'circle_rate_location_levels', 'circle_rate_locations', 'circle_rate_property_types',
            'circle_rate_property_categories', 'circle_rate_units', 'circle_rate_unit_conversions',
            'circle_rate_road_width_ranges', 'circle_rate_financial_years', 'circle_rate_sources',
            'circle_rate_import_batches', 'circle_rate_valuation_records', 'circle_rate_rules', 'circle_rate_calculator_fields',
            'circle_rate_calculator_field_options', 'circle_rates', 'circle_rate_versions',
        ] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->boolean('is_demo')->default(false)->index();
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'circle_rate_versions', 'circle_rates', 'circle_rate_valuation_records', 'circle_rate_calculator_field_options',
            'circle_rate_calculator_fields', 'circle_rate_rules', 'circle_rate_import_batches',
            'circle_rate_sources', 'circle_rate_financial_years', 'circle_rate_road_width_ranges',
            'circle_rate_unit_conversions', 'circle_rate_units', 'circle_rate_property_categories',
            'circle_rate_property_types', 'circle_rate_locations', 'circle_rate_location_levels',
            'circle_rate_area_types', 'circle_rate_tehsils', 'circle_rate_districts',
        ] as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->dropColumn('is_demo');
            });
        }
    }
};
