<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Setting;
use Database\Seeders\DefaultThemeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MenuCacheAndDefaultThemeSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_cache_stores_an_identifier_and_returns_a_fresh_eloquent_model(): void
    {
        $menu = Menu::create(['name' => 'Primary navigation', 'location' => 'primary']);
        MenuItem::create([
            'menu_id' => $menu->id,
            'label' => 'Home',
            'url' => '/',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        // A legacy object cache entry must not be read by the new implementation.
        Cache::put('menu:primary', Menu::make(['name' => 'Stale menu']));

        $result = Menu::cached('primary');

        $this->assertInstanceOf(Menu::class, $result);
        $this->assertSame($menu->id, (int) Cache::get('menu:id:primary'));
        $this->assertSame('Home', $result->items->first()->label);
        $this->assertInstanceOf(Menu::class, Menu::cached('primary'));
    }

    public function test_header_navigation_renders_the_menu_selected_by_the_site_setting(): void
    {
        $menu = Menu::create(['name' => 'Custom header', 'location' => 'custom-header']);
        MenuItem::create([
            'menu_id' => $menu->id,
            'label' => 'Custom landing',
            'url' => '/custom-landing',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $html = view('frontend.partials.navigation', [
            'primaryMenu' => Menu::cached('custom-header'),
        ])->render();

        $this->assertStringContainsString('Custom landing', $html);
        $this->assertStringContainsString('/custom-landing', $html);
        $this->assertStringNotContainsString('Properties', $html);
    }

    public function test_menu_item_changes_invalidate_the_cached_menu_identifier(): void
    {
        $menu = Menu::create(['name' => 'Primary navigation', 'location' => 'primary']);
        Menu::cached('primary');

        $this->assertSame($menu->id, (int) Cache::get('menu:id:primary'));

        $item = MenuItem::create([
            'menu_id' => $menu->id,
            'label' => 'Home',
            'url' => '/',
            'sort_order' => 0,
            'is_active' => true,
        ]);

        $this->assertNull(Cache::get('menu:id:primary'));

        Menu::cached('primary');
        $item->update(['label' => 'Homepage']);

        $this->assertNull(Cache::get('menu:id:primary'));
        $this->assertSame('Homepage', Menu::cached('primary')->items->first()->label);
    }

    public function test_default_theme_seeder_is_idempotent_and_preserves_site_configuration(): void
    {
        $this->seed(DefaultThemeSeeder::class);
        $this->seed(DefaultThemeSeeder::class);

        $this->assertDatabaseHas('settings', [
            'key' => 'header_menu_location',
            'value' => 'primary',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'footer_menu_location',
            'value' => 'footer',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'header_cta_label',
            'value' => 'Browse Properties',
        ]);
        $this->assertDatabaseHas('settings', [
            'key' => 'header_cta_url',
            'value' => '/properties',
        ]);
        $primaryMenu = Menu::where('location', 'primary')->firstOrFail();
        $this->assertSame(8, $primaryMenu->allItems()->count());
        $this->assertSame('/about', MenuItem::where('menu_id', $primaryMenu->id)->where('label', 'About Us')->value('url'));
        $this->assertSame('/contact', MenuItem::where('menu_id', $primaryMenu->id)->where('label', 'Contact')->value('url'));
        $this->assertSame(6, Menu::where('location', 'footer')->first()->allItems()->count());
        $this->assertSame(6, Menu::where('location', 'footer-quick-links')->first()->allItems()->count());
        $this->assertSame(5, Menu::where('location', 'footer-support')->first()->allItems()->count());
        $this->assertDatabaseHas('categories', ['slug' => 'apartment', 'type' => 'property']);
        $this->assertDatabaseHas('categories', ['slug' => 'buying-guides', 'type' => 'post']);

        Setting::write('site_title', 'My Custom Brand');
        MenuItem::where('menu_id', Menu::where('location', 'primary')->value('id'))
            ->where('label', 'Home')
            ->update(['url' => '/custom-home']);
        $this->seed(DefaultThemeSeeder::class);

        $this->assertSame('My Custom Brand', Setting::read('site_title'));
        $this->assertSame(
            '/custom-home',
            MenuItem::where('menu_id', Menu::where('location', 'primary')->value('id'))
                ->where('label', 'Home')
                ->value('url')
        );
    }
}
