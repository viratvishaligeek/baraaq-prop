<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class BuraaqhNavigationSeeder extends Seeder
{
    public function run(): void
    {
        $primary = Menu::query()->firstOrCreate(
            ['location' => 'primary'],
            ['name' => 'Primary navigation', 'is_active' => true]
        );

        $property = $this->addItem($primary, 'Properties', '/properties');
        $items = [
            ['Home', '/', null, 'custom', null],
            ['Properties', '/properties', null, 'custom', null],
            ['Apartments', $this->categoryUrl('apartment'), $property->id, 'property_category', $this->categoryId('apartment')],
            ['Independent Houses', $this->categoryUrl('independent-house'), $property->id, 'property_category', $this->categoryId('independent-house')],
            ['Villas', $this->categoryUrl('villa'), $property->id, 'property_category', $this->categoryId('villa')],
            ['Residential Plots', $this->categoryUrl('residential-plot'), $property->id, 'property_category', $this->categoryId('residential-plot')],
            ['Commercial', $this->categoryUrl('commercial'), $property->id, 'property_category', $this->categoryId('commercial')],
            ['Services', '/services', null, 'custom', null],
            ['Gallery', '/gallery', null, 'custom', null],
            ['Neighborhood Guide', '/neighborhood-guide', null, 'custom', null],
            ['FAQs', '/faq', null, 'custom', null],
            ['About Us', '/about', null, 'custom', null],
            ['Blog', '/blog', null, 'custom', null],
            ['Contact', '/contact', null, 'custom', null],
        ];

        $this->insertItems($primary, $items);

        $footer = Menu::query()->firstOrCreate(
            ['location' => 'footer'],
            ['name' => 'Footer navigation', 'is_active' => true]
        );
        $this->insertItems($footer, [
            ['Properties', '/properties', null, 'custom', null],
            ['Services', '/services', null, 'custom', null],
            ['About Buraaqh Properties', '/about', null, 'custom', null],
            ['Blog', '/blog', null, 'custom', null],
            ['Neighborhood Guide', '/neighborhood-guide', null, 'custom', null],
            ['Contact', '/contact', null, 'custom', null],
        ]);

        $quickLinks = Menu::query()->firstOrCreate(
            ['location' => 'footer-quick-links'],
            ['name' => 'Footer quick links', 'is_active' => true]
        );
        $this->insertItems($quickLinks, [
            ['Residential Property', '/properties?type=residential', null, 'custom', null],
            ['Commercial Property', '/properties?type=commercial', null, 'custom', null],
            ['Buying Guide', '/pages/buy-with-buraaqh', null, 'custom', null],
            ['Selling Guide', '/pages/sell-with-buraaqh', null, 'custom', null],
            ['Property FAQs', '/faq', null, 'custom', null],
            ['Privacy Policy', '/pages/privacy-policy', null, 'custom', null],
        ]);

        $support = Menu::query()->firstOrCreate(
            ['location' => 'footer-support'],
            ['name' => 'Footer support', 'is_active' => true]
        );
        $this->insertItems($support, [
            ['Contact and Enquiries', '/contact', null, 'custom', null],
            ['Document Checklist', '/blog?category=buying-guides', null, 'custom', null],
            ['Site Visit Questions', '/blog?category=property-tips', null, 'custom', null],
            ['Neighborhood Guides', '/neighborhood-guide', null, 'custom', null],
        ]);

        foreach ([$primary, $footer, $quickLinks, $support] as $menu) {
            Menu::forgetLocationCache($menu->location);
        }
    }

    private function addItem(Menu $menu, string $label, string $url): MenuItem
    {
        return MenuItem::query()->firstOrCreate(
            ['menu_id' => $menu->id, 'label' => $label],
            [
                'url' => $url,
                'link_type' => 'custom',
                'target' => '_self',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );
    }

    private function insertItems(Menu $menu, array $items): void
    {
        $parents = [];

        foreach ($items as $index => [$label, $url, $parentId, $linkType, $linkId]) {
            $item = MenuItem::query()->firstOrCreate(
                ['menu_id' => $menu->id, 'label' => $label],
                [
                    'parent_id' => $parentId,
                    'url' => $url,
                    'link_type' => $linkType,
                    'link_id' => $linkId,
                    'target' => '_self',
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ]
            );

            $parents[$label] = $item->id;
        }
    }

    private function categoryId(string $slug): ?int
    {
        return Category::query()->where('slug', $slug)->where('type', 'property')->value('id');
    }

    private function categoryUrl(string $slug): string
    {
        return $this->categoryId($slug)
            ? '/properties?category='.$slug
            : '/properties';
    }
}
