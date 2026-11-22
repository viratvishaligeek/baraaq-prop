<?php

namespace Tests\Feature;

use App\Models\Homepage;
use App\Models\HomepageSection;
use App\Models\Service;
use App\Services\Homepage\SectionDataResolver;
use Database\Seeders\HomepageTemplateSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FixedHomepageSectionRegistryTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_home_nine_is_seeded_as_the_fixed_homepage(): void
    {
        $this->seed(HomepageTemplateSeeder::class);

        $this->assertSame(1, Homepage::query()->count());
        $homepage = Homepage::query()->firstOrFail();

        $this->assertSame('home-9', $homepage->slug);
        $this->assertSame('published', $homepage->status);
    }

    public function test_active_section_registry_contains_only_the_fixed_home_nine_composition(): void
    {
        $expected = [
            'hero.home-9',
            'about.home-9',
            'statistics.home-9',
            'services.home-9',
            'properties.index',
            'featured-gallery.home-9',
            'home-details.home-9',
            'team.home-2',
            'testimonials.home-3',
            'blog.home-2',
        ];

        $this->assertSame($expected, array_keys(config('cms-sections.sections')));

        foreach ($expected as $key) {
            $definition = config('cms-sections.sections.'.$key);
            $this->assertNotEmpty($definition['view']);
            $this->assertTrue(view()->exists($definition['view']));
        }
    }

    public function test_fixed_homepage_schema_has_no_legacy_template_or_builder_columns_and_indexes(): void
    {
        foreach (['design_key', 'is_default', 'is_composition_complete'] as $column) {
            $this->assertFalse(Schema::hasColumn('homepages', $column));
        }

        foreach (['instance_key', 'source_design'] as $column) {
            $this->assertFalse(Schema::hasColumn('homepage_sections', $column));
        }

        $homepageIndexes = collect(Schema::getIndexes('homepages'));
        $sectionIndexes = collect(Schema::getIndexes('homepage_sections'));

        $this->assertFalse($homepageIndexes->contains(fn (array $index) => ($index['columns'] ?? []) === ['status']));
        $this->assertFalse($sectionIndexes->contains(fn (array $index) => ($index['columns'] ?? []) === ['section_key']));
        $this->assertFalse($sectionIndexes->contains(fn (array $index) => ($index['columns'] ?? []) === ['section_type']));
        $this->assertTrue($sectionIndexes->contains(
            fn (array $index) => ($index['unique'] ?? false) && ($index['columns'] ?? []) === ['homepage_id', 'section_key']
        ));
    }

    public function test_fixed_homepage_section_keys_are_unique_per_homepage(): void
    {
        $this->seed(HomepageTemplateSeeder::class);
        $homepage = Homepage::query()->where('slug', 'home-9')->firstOrFail();

        $attributes = [
            'homepage_id' => $homepage->id,
            'section_key' => 'hero.home-9',
            'section_type' => 'hero',
            'sort_order' => 1,
            'is_enabled' => true,
            'config' => [],
        ];

        HomepageSection::create($attributes);

        $this->expectException(QueryException::class);
        HomepageSection::create($attributes);
    }

    public function test_every_literal_section_config_reference_is_editable_in_the_homepage_admin(): void
    {
        foreach (config('cms-sections.sections') as $key => $definition) {
            $viewPath = resource_path('views/'.str_replace('.', '/', $definition['view']).'.blade.php');
            $this->assertFileExists($viewPath, "Missing section view for [{$key}].");

            $source = file_get_contents($viewPath);
            preg_match_all("/\\\$sectionConfig\\[['\"]([^'\"]+)['\"]\\]/", $source, $matches);

            $editableFields = array_keys($definition['fields'] ?? []);
            $unmanagedFields = array_values(array_diff(array_unique($matches[1]), $editableFields));

            $this->assertSame([], $unmanagedFields, "Section [{$key}] renders config keys that are not editable in Homepage admin.");
        }
    }


    public function test_saved_section_item_limit_takes_precedence_over_legacy_data_source_limit(): void
    {
        $homepage = Homepage::create([
            'name' => 'Home 9 — Fixed homepage',
            'slug' => 'home-9',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $section = $homepage->sections()->create([
            'section_key' => 'services.home-9',
            'section_type' => 'service-list',
            'sort_order' => 1,
            'is_enabled' => true,
            'config' => ['items_limit' => 1, 'columns' => '4'],
            'data_source' => ['limit' => 8, 'sort_by' => 'published_at', 'sort_direction' => 'desc'],
        ]);

        foreach ([1, 2] as $number) {
            Service::create([
                'title' => 'Published service '.$number,
                'slug' => 'published-service-'.$number,
                'status' => 'published',
                'published_at' => now()->subMinutes($number),
            ]);
        }

        $resolved = app(SectionDataResolver::class)->resolve($section);

        $this->assertCount(1, $resolved['services']);
    }

}
