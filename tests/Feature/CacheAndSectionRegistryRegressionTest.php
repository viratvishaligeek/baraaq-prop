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

    public function test_homepage_controller_caches_only_the_homepage_id(): void
    {
        $homepage = Homepage::create([
            'name' => 'Published homepage',
            'slug' => 'published-homepage-cache-test',
            'design_key' => 'index',
            'status' => 'published',
            'is_default' => true,
            'published_at' => now(),
        ]);

        Cache::put('homepage:published-default', new \stdClass());

        app(HomeController::class)->__invoke();

        $this->assertSame($homepage->id, Cache::get('homepage:published-default:id'));
        $this->assertNull(Cache::get('homepage:published-default'));
    }

    public function test_setting_read_ignores_legacy_model_cache_and_caches_scalar_data(): void
    {
        Setting::create([
            'group' => 'general',
            'key' => 'site_title',
            'value' => 'Pillar Real Estate',
            'type' => 'string',
        ]);

        // Simulate the legacy key that may contain a serialized Eloquent model.
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

    public function test_dotted_homepage_section_keys_are_registered_as_literal_array_keys(): void
    {
        $sections = config('cms-sections.sections', []);

        $this->assertArrayHasKey('hero.index', $sections);
        $this->assertArrayHasKey('properties.index', $sections);
        $this->assertSame('hero.index', $sections['hero.index']['key']);
        $this->assertSame('properties.index', $sections['properties.index']['key']);

        foreach ($sections as $key => $definition) {
            $this->assertSame($key, $definition['key'] ?? null, "Section registry key [{$key}] must match its definition key.");
        }
    }
}
