<?php

namespace Tests\Feature;

use App\Models\HomepageSection;
use App\Models\Service;
use App\Services\Homepage\SectionDataResolver;
use App\Services\Widgets\CmsWidgetRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PillerWidgetLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_and_services_piller_designs_are_registered_with_distinct_views(): void
    {
        $registry = app(CmsWidgetRegistry::class);

        $aboutKeys = ['about.home-4', 'about.home-7', 'about.home-8', 'about.home-9', 'about.home-10'];
        $serviceKeys = ['services.home-4', 'services.home-6', 'services.home-9', 'services.home-10'];
        $testimonialKeys = ['testimonials.index', 'testimonials.home-4', 'testimonials.home-10'];
        $categoryDefinition = $registry->find('categories.home-10');
        $additionalKeys = [
            'faq.home-10', 'statistics.home-4', 'statistics.home-6', 'statistics.home-10',
            'contact.home-1', 'contact.home-2', 'contact.home-3',
            'brands.grid', 'brands.marquee', 'gallery.home-5-rooms', 'gallery.home-6-projects', 'gallery.home-10-projects', 'gallery.home-1-city-explore', 'process.home-8', 'cta.download-home-2', 'cta.download-home-3',
            'cta.enquiry-home-3', 'cta.home-10',
        ];

        $this->assertNotNull($categoryDefinition);
        $this->assertTrue(view()->exists($categoryDefinition['view']));
        foreach ($additionalKeys as $key) {
            $definition = $registry->find($key);
            $this->assertNotNull($definition, $key.' should be registered');
            $this->assertTrue(view()->exists($definition['view']), $key.' should resolve to an actual Blade view');
        }

        foreach (array_merge($aboutKeys, $serviceKeys, $testimonialKeys) as $key) {
            $definition = $registry->find($key);

            $this->assertNotNull($definition, $key.' should be registered');
            $this->assertTrue(view()->exists($definition['view']), $key.' should resolve to an actual Blade view');
        }

        $aboutViews = collect($aboutKeys)->map(fn ($key) => $registry->find($key)['view'])->unique();
        $serviceViews = collect($serviceKeys)->map(fn ($key) => $registry->find($key)['view'])->unique();

        $this->assertCount(5, $aboutViews);
        $this->assertArrayHasKey('widget_background_image', $registry->find('about.home-4')['fields']);
        $this->assertArrayHasKey('widget_heading_color', $registry->find('services.home-9')['fields']);
        $this->assertArrayHasKey('widget_padding_top', $registry->find('testimonials.home-4')['fields']);
        $this->assertCount(4, $serviceViews);
        $testimonialViews = collect($testimonialKeys)->map(fn ($key) => $registry->find($key)['view'])->unique();
        $this->assertCount(3, $testimonialViews);

        $designGroups = [
            'About' => ['about.home-2', 'about.home-4', 'about.home-7', 'about.home-8', 'about.home-9', 'about.home-10'],
            'Services' => ['services.home-4', 'services.home-6', 'services.home-9', 'services.home-10'],
            'Why choose us' => ['why.home-1'],
            'Testimonials' => ['testimonials.home-2', 'testimonials.home-3', 'testimonials.index', 'testimonials.home-4', 'testimonials.home-5', 'testimonials.home-10'],
            'Property listings' => ['properties.index', 'properties.featured-index', 'properties.home-2', 'properties.home-3', 'featured-properties.home-2'],
            'Property categories' => ['categories.home-2', 'categories.home-3', 'categories.home-7', 'categories.home-10'],
            'Team members' => ['team.index', 'team.home-2', 'team.home-3', 'team.home-10'],
            'Blog posts' => ['blog.index', 'blog.home-2', 'blog.home-3', 'blog.home-6'],
            'Image galleries' => ['gallery.home-3', 'featured-gallery.home-3', 'neighborhoods.home-2', 'property-values.home-1', 'property-values.home-2', 'gallery.home-5-rooms', 'gallery.home-10-projects'],
            'FAQ' => ['faq.home-2', 'faq.home-3', 'faq.home-10'],
            'Call to action' => ['cta.download-home-2', 'cta.download-home-3', 'cta.enquiry-home-3', 'cta.home-10'],
            'Statistics' => ['statistics.home-4', 'statistics.home-6', 'statistics.home-10'],
            'Contact' => ['contact.home-1', 'contact.home-2', 'contact.home-3'],
            'Client logos' => ['brands.home-3', 'brands.grid', 'brands.marquee'],
        ];

        foreach ($designGroups as $family => $keys) {
            foreach ($keys as $key) {
                $definition = $registry->find($key);
                $this->assertNotNull($definition, $family.' design '.$key.' should be registered');
                $this->assertTrue(view()->exists($definition['view']), $family.' design '.$key.' should have a real Blade view');
            }
        }

        $this->assertSame(57, array_sum(array_map('count', $designGroups)));
    }

    public function test_service_widget_query_honours_selected_ids_limit_sorting_and_published_scope(): void
    {
        $zulu = Service::create([
            'title' => 'Zulu service',
            'slug' => 'zulu-service-widget-test',
            'excerpt' => 'Zulu excerpt',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
        $alpha = Service::create([
            'title' => 'Alpha service',
            'slug' => 'alpha-service-widget-test',
            'excerpt' => 'Alpha excerpt',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
        Service::create([
            'title' => 'Draft service',
            'slug' => 'draft-service-widget-test',
            'status' => 'draft',
            'published_at' => now(),
        ]);

        $section = new HomepageSection([
            'section_key' => 'services.home-9',
            'section_type' => 'service-list',
            'data_source' => [
                'selected_ids' => [$zulu->id, $alpha->id],
                'limit' => 1,
                'sort_by' => 'title',
                'sort_direction' => 'asc',
            ],
        ]);

        $resolved = app(SectionDataResolver::class)->resolve($section);

        $this->assertCount(1, $resolved['services']);
        $this->assertSame($alpha->id, $resolved['services']->first()->id);
    }
}
