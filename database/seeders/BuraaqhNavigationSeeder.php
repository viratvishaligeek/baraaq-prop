<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class BuraaqhNavigationSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedMenu('primary', 'Primary navigation', [
            ['Home', '/', null],
            ['Properties', '/properties', null],
            ['Services', '/services', null],
            ['Property Gallery', '/gallery', null],
            ['Neighborhood Guides', '/neighborhood-guide', null],
            ['FAQs', '/faq', null],
            ['About Us', '/about', null],
            ['Journal', '/blog', null],
            ['Contact', '/contact', null],
        ]);

        $this->seedMenu('footer', 'Footer navigation', [
            ['Properties', '/properties', null],
            ['Services', '/services', null],
            ['About Us', '/about', null],
            ['Journal', '/blog', null],
            ['Neighborhood Guides', '/neighborhood-guide', null],
            ['FAQs', '/faq', null],
            ['Privacy Policy', '/privacy-policy', null],
            ['Contact', '/contact', null],
        ]);

        $this->seedMenu('footer-quick-links', 'Footer quick links', [
            ['Property Buying Guide', '/blog/complete-guide-buying-residential-property-bareilly', null],
            ['Property Documents', '/blog/documents-check-before-buying-property-uttar-pradesh', null],
            ['Residential vs Commercial', '/blog/residential-vs-commercial-property-buyer-guide', null],
            ['Contact', '/contact', null],
        ]);

        $this->seedMenu('footer-support', 'Footer support', [
            ['Frequently Asked Questions', '/faq', null],
            ['Neighborhood Guides', '/neighborhood-guide', null],
            ['Privacy Policy', '/privacy-policy', null],
            ['Contact Us', '/contact', null],
        ]);
    }

    private function seedMenu(string $location, string $name, array $items): void
    {
        $menu = Menu::query()->firstOrCreate(['location' => $location], ['name' => $name]);

        foreach ($items as $index => [$label, $url, $parentLabel]) {
            MenuItem::query()->firstOrCreate(
                ['menu_id' => $menu->id, 'label' => $label],
                [
                    'parent_id' => null,
                    'url' => $url,
                    'link_type' => 'custom',
                    'link_id' => null,
                    'target' => '_self',
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );
        }

        Menu::forgetLocationCache($location);
    }
}
