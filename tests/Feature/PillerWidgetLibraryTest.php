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

        foreach (array_merge($aboutKeys, $serviceKeys) as $key) {
            $definition = $registry->find($key);

            $this->assertNotNull($definition, $key.' should be registered');
            $this->assertTrue(view()->exists($definition['view']), $key.' should resolve to an actual Blade view');
        }

        $aboutViews = collect($aboutKeys)->map(fn ($key) => $registry->find($key)['view'])->unique();
        $serviceViews = collect($serviceKeys)->map(fn ($key) => $registry->find($key)['view'])->unique();

        $this->assertCount(5, $aboutViews);
        $this->assertCount(4, $serviceViews);
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
