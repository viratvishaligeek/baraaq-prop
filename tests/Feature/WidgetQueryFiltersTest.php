<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\HomepageSectionController;
use App\Models\Homepage;
use App\Models\HomepageSection;
use App\Models\Testimonial;
use App\Services\Homepage\SectionDataResolver;
use App\Services\Widgets\CmsWidgetRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class WidgetQueryFiltersTest extends TestCase
{
    use RefreshDatabase;
    public function test_testimonial_query_applies_minimum_rating_filter_before_limit(): void
    {
        Testimonial::create([
            'name' => 'Three star reviewer',
            'quote' => 'Good.',
            'rating' => 3,
            'sort_order' => 1,
            'is_active' => true,
            'status' => 'published',
        ]);
        $fiveStar = Testimonial::create([
            'name' => 'Five star reviewer',
            'quote' => 'Excellent.',
            'rating' => 5,
            'sort_order' => 2,
            'is_active' => true,
            'status' => 'published',
        ]);

        $section = new HomepageSection([
            'section_key' => 'testimonials.home-4',
            'section_type' => 'testimonial-slider',
            'data_source' => [
                'limit' => 1,
                'sort_by' => 'sort_order',
                'sort_direction' => 'asc',
                'filters' => ['min_rating' => 4],
            ],
        ]);

        $resolved = app(SectionDataResolver::class)->resolve($section);

        $this->assertCount(1, $resolved['testimonials']);
        $this->assertSame($fiveStar->id, $resolved['testimonials']->first()->id);
        $this->assertSame(5.0, (float) $resolved['averageRating']);
        $this->assertSame(1, $resolved['reviewCount']);
    }

    public function test_homepage_widget_admin_save_persists_database_query_filters(): void
    {
        $homepage = Homepage::create([
            'name' => 'Query builder homepage',
            'slug' => 'query-builder-homepage',
            'design_key' => 'home-5',
            'status' => 'draft',
            'is_default' => false,
            'is_composition_complete' => false,
        ]);
        $section = HomepageSection::create([
            'homepage_id' => $homepage->id,
            'instance_key' => (string) \Illuminate\Support\Str::uuid(),
            'section_key' => 'testimonials.home-5',
            'section_type' => 'testimonial-slider',
            'source_design' => 'home-5',
            'sort_order' => 1,
            'is_enabled' => true,
            'config' => [],
            'data_source' => [],
        ]);

        $request = Request::create('/admin/homepages/'.$homepage->id.'/sections/'.$section->id, 'PATCH', [
            'config' => ['heading' => 'Client stories'],
            'data_source' => [
                'limit' => '8',
                'sort_by' => 'sort_order',
                'sort_direction' => 'asc',
                'selected_ids' => '2,5',
                'filters' => ['min_rating' => '4'],
            ],
        ]);

        app(HomepageSectionController::class)->update($request, $homepage, $section);

        $saved = $section->fresh();
        $this->assertSame('Client stories', $saved->config['heading']);
        $this->assertSame(8, $saved->data_source['limit']);
        $this->assertSame([2, 5], $saved->data_source['selected_ids']);
        $this->assertSame(4, $saved->data_source['filters']['min_rating']);
    }

    public function test_queryable_content_families_expose_only_registered_filter_definitions(): void
    {
        $queryable = app(CmsWidgetRegistry::class)->queryableTypes();

        $this->assertArrayHasKey('listing_type', $queryable['property-list']['filters']);
        $this->assertArrayHasKey('category_id', $queryable['blog-slider']['filters']);
        $this->assertArrayHasKey('designation', $queryable['team-slider']['filters']);
        $this->assertArrayHasKey('min_rating', $queryable['testimonial-slider']['filters']);
        $this->assertSame(['published_at', 'created_at', 'title'], $queryable['blog-slider']['sort']);
    }
}
