<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class LegacySchemaRetentionTest extends TestCase
{
    use RefreshDatabase;

    public function test_fixed_homepage_cleanup_migrations_retain_legacy_data_and_unrelated_schema(): void
    {
        $areaId = DB::table('widget_areas')->insertGetId([
            'name' => 'Owner-customized footer',
            'location' => 'owner-footer',
            'description' => 'Existing custom widget area.',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $widgetId = DB::table('widgets')->insertGetId([
            'name' => 'Owner widget',
            'type' => 'text',
            'config' => json_encode(['content' => 'Keep this content.']),
            'status' => 'published',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('widget_area_items')->insert([
            'widget_area_id' => $areaId,
            'widget_id' => $widgetId,
            'sort_order' => 0,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $legacyCleanup = require database_path('migrations/2026_11_22_000001_remove_legacy_page_builder_and_widget_schema.php');
        $legacyCleanup->up();

        $this->assertDatabaseHas('widget_areas', ['id' => $areaId, 'name' => 'Owner-customized footer']);
        $this->assertDatabaseHas('widgets', ['id' => $widgetId, 'name' => 'Owner widget']);
        $this->assertDatabaseHas('widget_area_items', ['widget_area_id' => $areaId, 'widget_id' => $widgetId]);
        $this->assertTrue(Schema::hasTable('reusable_sections'));
        $this->assertTrue(Schema::hasColumn('homepage_sections', 'instance_key'));
        $this->assertTrue(Schema::hasColumn('homepage_sections', 'source_design'));
        $this->assertTrue(Schema::hasColumn('homepages', 'design_key'));
        $this->assertTrue(Schema::hasColumn('homepages', 'is_default'));
    }

    public function test_fixed_homepage_schema_enforces_unique_section_keys_without_dropping_legacy_columns(): void
    {
        $indexes = collect(Schema::getIndexes('homepage_sections'));
        $uniqueIndex = $indexes->first(
            fn (array $index): bool => ($index['unique'] ?? false)
                && ($index['columns'] ?? []) === ['homepage_id', 'section_key']
        );

        $this->assertNotNull($uniqueIndex);
        $this->assertTrue(Schema::hasColumn('homepage_sections', 'instance_key'));
        $this->assertTrue(Schema::hasColumn('homepage_sections', 'source_design'));
        $this->assertTrue(Schema::hasColumn('homepages', 'design_key'));
        $this->assertTrue(Schema::hasColumn('homepages', 'is_default'));
    }

    public function test_legacy_page_section_keys_with_null_homepage_ids_do_not_block_homepage_uniqueness(): void
    {
        foreach ([1, 2] as $position) {
            DB::table('homepage_sections')->insert([
                'homepage_id' => null,
                'instance_key' => (string) Str::uuid(),
                'section_key' => 'legacy.page.section',
                'section_type' => 'text',
                'source_design' => 'legacy',
                'sort_order' => $position,
                'is_enabled' => true,
                'config' => json_encode(['content' => 'Preserved legacy content '.$position]),
                'data_source' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $fixedSchema = require database_path('migrations/2026_11_22_000002_simplify_fixed_homepage_schema.php');
        $fixedSchema->up();

        $this->assertSame(
            2,
            DB::table('homepage_sections')->whereNull('homepage_id')->where('section_key', 'legacy.page.section')->count()
        );
    }

    public function test_footer_area_migration_preserves_customized_existing_areas_on_up_and_down(): void
    {
        $areaId = DB::table('widget_areas')->insertGetId([
            'name' => 'Custom footer column',
            'location' => 'footer-1',
            'description' => 'Owner-authored footer content.',
            'is_active' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $footerDefaults = require database_path('migrations/2026_11_18_000020_create_four_footer_widget_areas.php');
        $footerDefaults->up();

        $this->assertDatabaseHas('widget_areas', [
            'id' => $areaId,
            'name' => 'Custom footer column',
            'description' => 'Owner-authored footer content.',
            'is_active' => 0,
        ]);

        $footerDefaults->down();

        $this->assertDatabaseHas('widget_areas', [
            'id' => $areaId,
            'location' => 'footer-1',
        ]);
    }
}
