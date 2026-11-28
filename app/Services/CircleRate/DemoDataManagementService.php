<?php

namespace App\Services\CircleRate;

use App\Models\CircleRateAuditLog;
use Illuminate\Support\Facades\DB;

class DemoDataManagementService
{
    public function statistics(): array
    {
        $tables = [
            'Districts' => 'circle_rate_districts', 'Tehsils' => 'circle_rate_tehsils',
            'Area types' => 'circle_rate_area_types', 'Location levels' => 'circle_rate_location_levels',
            'City locations / sub-areas / mohallas' => 'circle_rate_locations',
            'Property types' => 'circle_rate_property_types', 'Property categories' => 'circle_rate_property_categories',
            'Rate units' => 'circle_rate_units', 'Unit conversions' => 'circle_rate_unit_conversions',
            'Road-width ranges' => 'circle_rate_road_width_ranges', 'Financial years' => 'circle_rate_financial_years',
            'Rate sources' => 'circle_rate_sources', 'Circle rates' => 'circle_rates',
            'Rate versions' => 'circle_rate_versions', 'Import batches' => 'circle_rate_import_batches',
            'Calculation rules' => 'circle_rate_rules', 'Calculator fields' => 'circle_rate_calculator_fields',
            'Calculator field options' => 'circle_rate_calculator_field_options',
            'Valuation records' => 'circle_rate_valuation_records',
        ];
        $stats = [];
        foreach ($tables as $label => $table) {
            $stats[$label] = DB::table($table)->where('is_demo', true)->count();
        }
        return $stats;
    }

    public function reset(?int $userId = null): array
    {
        return DB::transaction(function () use ($userId): array {
            $counts = $this->statistics();

            $batchIds = DB::table('circle_rate_import_batches')->where('is_demo', true)->pluck('id');
            if ($batchIds->isNotEmpty()) DB::table('circle_rate_import_rows')->whereIn('batch_id', $batchIds)->delete();

            $demoRateIds = DB::table('circle_rates')->where('is_demo', true)->pluck('id');
            if ($demoRateIds->isNotEmpty()) {
                DB::table('circle_rate_valuation_records')->whereIn('circle_rate_id', $demoRateIds)->delete();
                DB::table('circle_rate_valuation_records')->whereIn('previous_circle_rate_id', $demoRateIds)->update(['previous_circle_rate_id' => null]);
                DB::table('circle_rate_versions')->whereIn('circle_rate_id', $demoRateIds)->delete();
                DB::table('circle_rates')->whereIn('id', $demoRateIds)->delete();
            }
            DB::table('circle_rate_valuation_records')->where('is_demo', true)->delete();
            DB::table('circle_rate_rules')->where('is_demo', true)->delete();
            DB::table('circle_rate_calculator_field_options')->where('is_demo', true)->delete();
            DB::table('circle_rate_calculator_fields')->where('is_demo', true)->delete();
            DB::table('circle_rate_import_batches')->where('is_demo', true)->delete();
            DB::table('circle_rate_unit_conversions')->where('is_demo', true)->delete();
            DB::table('circle_rate_road_width_ranges')->where('is_demo', true)->delete();

            // Source references on historical audit rows are nullable; audit history is retained.
            DB::table('circle_rate_sources')->where('is_demo', true)->delete();

            $locationIds = DB::table('circle_rate_locations')->where('is_demo', true)->orderByDesc('id')->pluck('id');
            foreach ($locationIds as $id) DB::table('circle_rate_locations')->where('id', $id)->delete();

            DB::table('circle_rate_property_categories')->where('is_demo', true)->delete();
            DB::table('circle_rate_property_types')->where('is_demo', true)->delete();
            DB::table('circle_rate_financial_years')->where('is_demo', true)->delete();
            DB::table('circle_rate_units')->where('is_demo', true)->delete();
            DB::table('circle_rate_area_types')->where('is_demo', true)->delete();
            DB::table('circle_rate_location_levels')->where('is_demo', true)->delete();

            CircleRateAuditLog::query()->create([
                'user_id' => $userId, 'subject_type' => self::class, 'subject_id' => null,
                'event' => 'demo_reset', 'old_values' => $counts, 'new_values' => null,
                'reason' => 'Removed only records carrying the explicit is_demo marker. Official and manually managed records were retained.',
                'source_id' => null, 'ip_address' => request()?->ip(),
            ]);

            return $counts;
        });
    }
}
