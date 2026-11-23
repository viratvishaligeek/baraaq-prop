<?php

namespace Tests\\Feature;

use App\\Models\\Homepage;
use App\\Models\\Menu;
use App\\Models\\MenuItem;
use App\\Models\\Property;
use App\\Models\\Setting;
use App\\Services\\Homepage\\SectionDataResolver;
use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Tests\\TestCase;

class HomepagePersistenceRegressionTest extends TestCase
{
    use RefreshDatabase;

    private function homepage(): Homepage
    {
        return Homepage::create([
            'name' => 'Home 9',
            'slug' => 'home-9',
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function test_homepage_header_uses_the_admin_selected_menu_location(): void
    {
        $this->homepage();

        $menu = Menu::create([
            'name' => 'Homepage navigation',
            'location' => 'homepage-primary',
        ]);

        MenuItem::create([
            'menu_id' => $menu->id,
            'label' => 'Admin-managed navigation item',
            'url' => '/contact',
            'link_type' => 'custom',
            'target' => '_self',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        Setting::write('header_menu_location', 'homepage-primary', 'navigation', 'string');

        $this->get('/')
            ->assertOk()
            ->assertSee('Admin-managed navigation item');
    }

    public function test_saved_property_section_filters_override_legacy_data_source_values(): void
    {
        $homepage = $this->homepage();

        Property::create([
            'title' => 'Admin-selected sale property',
            'slug' => 'admin-selected-sale-property',
            'listing_type' => 'sale',
            'area_unit' => 'sqft',
            'status' => 'published',
            'published_at' => now(),
        ]);

        Property::create([
            'title' => 'Legacy rent property',
            'slug' => 'legacy-rent-property',
            'listing_type' => 'rent',
            'area_unit' => 'sqft',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $definition = config('cms-sections.sections.properties.index');
        $section = $homepage->sections()->create([
            'section_key' => 'properties.index',
            'section_type' => $definition['type'],
            'sort_order' => 1,
            'is_enabled' => true,
            'config' => ['listing_filter' => 'sale', 'items_limit' => 2],
            'data_source' => ['listing_filter' => 'rent', 'limit' => 1],
        ]);

        $renderConfig = array_merge(
            $definition['defaults'] ?? [],
            $section->data_source ?? [],
            $section->config ?? []
        );

        $resolved = app(SectionDataResolver::class)->resolve($section, $renderConfig);

        $this->assertSame(
            ['admin-selected-sale-property'],
            $resolved['properties']->pluck('slug')->all()
        );
    }
}
