<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultThemeSeeder extends Seeder
{
    public function run(): void
    {
        $siteName = trim((string) env('SITE_NAME', 'Pillar Real Estate')) ?: 'Pillar Real Estate';

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
            'footer_menu_location' => ['footer', 'navigation', 'string'],
            'sticky_header' => ['1', 'appearance', 'boolean'],
            'primary_color' => ['#068FFF', 'appearance', 'string'],
            'secondary_color' => ['#044DBC', 'appearance', 'string'],
            'footer_description' => ['', 'footer', 'string'],
            'footer_copyright' => [$siteName, 'footer', 'string'],
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

        $this->seedMenu('primary', 'Primary navigation', [
            ['Home', '/', null, 0],
            ['Properties', '/properties', null, 1],
            ['For Sale', '/properties?listing_type=sale', 'Properties', 2],
            ['For Rent', '/properties?listing_type=rent', 'Properties', 3],
            ['Services', '/services', null, 4],
            ['About Us', '/about.html', null, 5],
            ['Blog', '/blog', null, 6],
            ['Contact', '/contact.html', null, 7],
        ]);

        $this->seedMenu('footer', 'Footer navigation', [
            ['Properties', '/properties', null, 0],
            ['Services', '/services', null, 1],
            ['About Us', '/about.html', null, 2],
            ['Blog', '/blog', null, 3],
            ['Testimonials', '/testimonials', null, 4],
            ['Contact', '/contact.html', null, 5],
        ]);
    }

    private function seedMenu(string $location, string $name, array $items): void
    {
        $menu = Menu::query()->firstOrCreate(
            ['location' => $location],
            ['name' => $name]
        );

        $menu->updateQuietly(['name' => $name]);

        $parents = [];

        DB::transaction(function () use ($menu, $items, &$parents): void {
            foreach ($items as [$label, $url, $parentLabel, $sortOrder]) {
                $parentId = $parentLabel ? ($parents[$parentLabel] ?? null) : null;

                $item = MenuItem::query()->updateOrCreate(
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

                $parents[$label] = $item->id;
            }
        });

        Menu::forgetLocationCache($location);
    }
}
