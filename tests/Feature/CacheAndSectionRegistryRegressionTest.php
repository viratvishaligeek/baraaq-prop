<?php

namespace Tests\Feature;

use App\Http\Controllers\Frontend\HomeController;
use App\Models\Homepage;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CacheAndSectionRegistryRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_controller_always_resolves_home_nine_even_if_an_alternate_record_exists(): void
    {
        Homepage::create([
            'name' => 'Alternate homepage',
            'slug' => 'home-2',
            'status' => 'published',
            'published_at' => now(),
        ]);

        Homepage::create([
            'name' => 'Fixed homepage',
            'slug' => 'home-9',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $response = app(HomeController::class)->__invoke();

        $this->assertSame('home-9', $response->getData()['homepage']->slug);
        $this->assertSame('frontend.homepage', $response->name());
    }

    public function test_setting_read_ignores_legacy_model_cache_and_caches_scalar_data(): void
    {
        Setting::create([
            'group' => 'general',
            'key' => 'site_title',
            'value' => 'Pillar Real Estate',
            'type' => 'string',
        ]);

        Cache::put('setting:site_title', new \stdClass());

        $this->assertSame('Pillar Real Estate', Setting::read('site_title'));
        $this->assertIsArray(Cache::get('setting:data:site_title'));
        $this->assertSame('Pillar Real Estate', Cache::get('setting:data:site_title')['value']);
    }

    public function test_setting_write_invalidates_the_new_scalar_cache_key(): void
    {
        Setting::create([
            'group' => 'general',
            'key' => 'site_title',
            'value' => 'Old title',
            'type' => 'string',
        ]);

        $this->assertSame('Old title', Setting::read('site_title'));
        Setting::write('site_title', 'New title');
        $this->assertSame('New title', Setting::read('site_title'));
    }

    public function test_dotted_section_registry_contains_only_the_fixed_home_nine_sections(): void
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
        $sections = config('cms-sections.sections', []);

        $this->assertSame($expected, array_keys($sections));
        $this->assertArrayNotHasKey('hero.index', $sections);

        foreach ($sections as $key => $definition) {
            $this->assertSame($key, $definition['key'] ?? null, "Section registry key [{$key}] must match its definition key.");
        }
    }
}
