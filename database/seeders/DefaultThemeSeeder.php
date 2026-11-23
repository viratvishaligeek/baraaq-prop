<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultThemeSeeder extends Seeder
{
    public function run(): void
    {
        $siteName = trim((string) env('SITE_NAME', 'Buraaqh Properties Real Estate')) ?: 'Buraaqh Properties Real Estate';

        $settings = [
            'site_title' => [$siteName, 'general', 'string'],
            'site_tagline' => ['', 'general', 'string'],
            'business_name' => [$siteName, 'business', 'string'],
            'site_email' => ['', 'contact', 'string'],
            'site_phone' => ['', 'contact', 'string'],
            'whatsapp_number' => ['', 'contact', 'string'],
            'site_address' => ['', 'contact', 'string'],
            'logo' => ['', 'branding', 'string'],
            'favicon' => ['', 'branding', 'string'],
            'header_menu_location' => ['primary', 'navigation', 'string'],
            'header_cta_label' => ['Browse Properties', 'navigation', 'string'],
            'header_cta_url' => ['/properties', 'navigation', 'string'],
            'footer_menu_location' => ['footer', 'navigation', 'string'],
            'footer_quick_links_menu_location' => ['footer-quick-links', 'navigation', 'string'],
            'footer_support_menu_location' => ['footer-support', 'navigation', 'string'],
            'footer_about_heading' => ['About', 'footer', 'string'],
            'footer_services_heading' => ['Services', 'footer', 'string'],
            'footer_quick_links_heading' => ['Quick Links', 'footer', 'string'],
            'footer_support_heading' => ['Support', 'footer', 'string'],
            'footer_location_heading' => ['Buraaqh Properties Location', 'footer', 'string'],
            'footer_social_heading' => ['Social Media:', 'footer', 'string'],
            'sticky_header' => ['1', 'appearance', 'boolean'],
            'primary_color' => ['#068FFF', 'appearance', 'string'],
            'secondary_color' => ['#044DBC', 'appearance', 'string'],
            'footer_description' => ['', 'footer', 'string'],
            'footer_copyright' => [$siteName, 'footer', 'string'],
            'footer_rights_text' => ['All Rights Reserved.', 'footer', 'string'],
            'meta_title' => [$siteName, 'seo', 'string'],
            'meta_description' => ['', 'seo', 'string'],
            'meta_keywords' => ['', 'seo', 'string'],
            'meta_robots' => ['index,follow', 'seo', 'string'],
            'default_og_image' => ['', 'seo', 'string'],
            'head_scripts' => ['', 'integrations', 'string'],
            'body_scripts' => ['', 'integrations', 'string'],
        ];

        foreach ($settings as $key => [$value, $group, $type]) {
            // Seed missing defaults only. Never overwrite values configured by the site owner.
            if (! Setting::query()->where('key', $key)->exists()) {
                Setting::write($key, $value, $group, $type);
            }
        }

        $this->seedCategories();

        $this->seedMenu('primary', 'Primary navigation', [
            ['Home', '/', null, 0],
            ['Properties', '/properties', null, 1],
            ['For Sale', '/properties?listing_type=sale', 'Properties', 2],
            ['For Rent', '/properties?listing_type=rent', 'Properties', 3],
            ['Services', '/services', null, 4],
            ['About Us', '/about', null, 5],
            ['Blog', '/blog', null, 6],
            ['Contact', '/contact', null, 7],
        ]);

        $this->seedMenu('footer-quick-links', 'Footer quick links', [
            ['Properties', '/properties', null, 0],
            ['Blog', '/blog', null, 1],
            ['Services', '/services', null, 2],
            ['Our team', '/team', null, 3],
            ['Testimonials', '/testimonials', null, 4],
            ['XML sitemap', '/sitemap.xml', null, 5],
        ]);

        $this->seedMenu('footer-support', 'Footer support', [
            ['Help Center', '/contact', null, 0],
            ['FAQs', '/services', null, 1],
            ['Contact Us', '/contact', null, 2],
            ['Ticket Support', '/contact', null, 3],
            ['Live Chat', '/contact', null, 4],
        ]);

        $this->seedMenu('footer', 'Footer navigation', [
            ['Properties', '/properties', null, 0],
            ['Services', '/services', null, 1],
            ['About Us', '/about', null, 2],
            ['Blog', '/blog', null, 3],
            ['Testimonials', '/testimonials', null, 4],
            ['Contact', '/contact', null, 5],
        ]);
    }

    private function seedCategories(): void
    {
        $categories = [
            ['name' => 'Apartment', 'slug' => 'apartment', 'type' => 'property', 'sort_order' => 1],
            ['name' => 'House', 'slug' => 'house', 'type' => 'property', 'sort_order' => 2],
            ['name' => 'Villa', 'slug' => 'villa', 'type' => 'property', 'sort_order' => 3],
            ['name' => 'Office', 'slug' => 'office', 'type' => 'property', 'sort_order' => 4],
            ['name' => 'Land', 'slug' => 'land', 'type' => 'property', 'sort_order' => 5],
            ['name' => 'Commercial Property', 'slug' => 'commercial-property', 'type' => 'property', 'sort_order' => 6],
            ['name' => 'Buying Guides', 'slug' => 'buying-guides', 'type' => 'post', 'sort_order' => 1],
            ['name' => 'Selling Guides', 'slug' => 'selling-guides', 'type' => 'post', 'sort_order' => 2],
            ['name' => 'Market Insights', 'slug' => 'market-insights', 'type' => 'post', 'sort_order' => 3],
            ['name' => 'Property Tips', 'slug' => 'property-tips', 'type' => 'post', 'sort_order' => 4],
        ];

        foreach ($categories as $category) {
            Category::query()->firstOrCreate(
                ['slug' => $category['slug']],
                $category + ['is_active' => true]
            );
        }
    }

    private function seedMenu(string $location, string $name, array $items): void
    {
        $menu = Menu::query()->firstOrCreate(
            ['location' => $location],
            ['name' => $name]
        );

        $parents = [];

        DB::transaction(function () use ($menu, $items, &$parents): void {
            foreach ($items as [$label, $url, $parentLabel, $sortOrder]) {
                $parentId = $parentLabel ? ($parents[$parentLabel] ?? null) : null;

                $item = MenuItem::query()->firstOrCreate(
                    ['menu_id' => $menu->id, 'label' => $label],
                    [
                        'parent_id' => $parentId,
                        'url' => $url,
                        'link_type' => 'custom',
                        'link_id' => null,
                        'target' => '_self',
                        'sort_order' => $sortOrder,
                        'is_active' => true,
                    ]
                );

                $legacyUrl = match ($url) {
                    '/about' => '/about.html',
                    '/contact' => '/contact.html',
                    default => null,
                };
                if ($legacyUrl && $item->url === $legacyUrl) {
                    $item->update(['url' => $url]);
                }

                $parents[$label] = $item->id;
            }
        });

        Menu::forgetLocationCache($location);
    }
}
