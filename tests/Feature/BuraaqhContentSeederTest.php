<?php

namespace Tests\\Feature;

use App\\Models\\FaqItem;
use App\\Models\\GalleryItem;
use App\\Models\\Homepage;
use App\\Models\\NeighborhoodGuide;
use App\\Models\\Page;
use App\\Models\\Post;
use App\\Models\\Property;
use App\\Models\\Service;
use App\\Models\\Setting;
use Database\\Seeders\\BuraaqhContentSeeder;
use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Illuminate\\Support\\Facades\\Storage;
use Tests\\TestCase;

class BuraaqhContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_buraaqh_seeder_is_repeatable_and_does_not_publish_fabricated_property_inventory(): void
    {
        Storage::fake('public');

        $this->seed();

        $this->assertSame(1, Homepage::query()->count());
        $this->assertSame(0, Property::published()->count());
        $this->assertSame('Buraaqh Properties', Setting::read('site_title'));
        $this->assertSame('Bareilly', Setting::read('service_area_city'));
        $this->assertSame('', Setting::read('site_phone'));
        $this->assertSame('', Setting::read('site_email'));
        $this->assertSame('', Setting::read('site_address'));

        $counts = [
            'homepages' => Homepage::query()->count(),
            'posts' => Post::query()->count(),
            'services' => Service::query()->count(),
            'pages' => Page::query()->count(),
            'faqs' => FaqItem::query()->count(),
            'gallery' => GalleryItem::query()->count(),
            'neighborhood_guides' => NeighborhoodGuide::query()->count(),
        ];

        $this->seed(BuraaqhContentSeeder::class);

        $this->assertSame($counts, [
            'homepages' => Homepage::query()->count(),
            'posts' => Post::query()->count(),
            'services' => Service::query()->count(),
            'pages' => Page::query()->count(),
            'faqs' => FaqItem::query()->count(),
            'gallery' => GalleryItem::query()->count(),
            'neighborhood_guides' => NeighborhoodGuide::query()->count(),
        ]);
        $this->assertSame(0, Property::published()->count());

        $this->get('/')
            ->assertOk()
            ->assertSee('Buraaqh Properties')
            ->assertDontSee('offices worldwide')
            ->assertDontSee('5,600 sq ft');
    }

    public function test_seeder_preserves_owner_managed_content_when_rerun(): void
    {
        Storage::fake('public');
        $this->seed();

        $page = Page::query()->create([
            'title' => 'Owner-managed page',
            'slug' => 'owner-managed-page',
            'content' => '<p>Pillar is a proper noun in this owner-managed content and must not be rewritten.</p>',
            'template' => 'default',
            'status' => 'draft',
        ]);

        $this->seed(BuraaqhContentSeeder::class);

        $this->assertSame(
            '<p>Pillar is a proper noun in this owner-managed content and must not be rewritten.</p>',
            $page->fresh()->content
        );
        $this->assertSame('draft', $page->fresh()->status);
    }
}
