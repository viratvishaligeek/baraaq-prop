<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\MenuItem;
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

    public function test_default_theme_seeder_is_idempotent_and_does_not_overwrite_site_settings(): void
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
        $this->assertSame(8, Menu::where('location', 'primary')->first()->allItems()->count());
        $this->assertSame(6, Menu::where('location', 'footer')->first()->allItems()->count());

        \App\Models\Setting::write('site_title', 'My Custom Brand');
        $this->seed(DefaultThemeSeeder::class);

        $this->assertSame('My Custom Brand', \App\Models\Setting::read('site_title'));
    }
}
