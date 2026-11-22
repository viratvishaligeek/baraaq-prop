<?php

namespace Tests\Feature;

use App\Models\Homepage;
use App\Models\HomepageSection;
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
}
