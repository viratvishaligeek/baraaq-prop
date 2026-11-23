<?php

namespace Tests\Feature;

use App\Models\FaqItem;
use App\Models\Banner;
use App\Models\Media;
use App\Models\GalleryItem;
use App\Models\Homepage;
use App\Models\NeighborhoodGuide;
use App\Models\Page;
use App\Models\Post;
use App\Models\Property;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\Setting;
use Database\Seeders\BuraaqhContentSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BuraaqhContentSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_buraaqh_seeder_is_repeatable_and_does_not_publish_fabricated_property_inventory(): void
    {
        Storage::fake('public');

        $this->seed();

        $this->assertSame(1, Homepage::query()->count());
        $this->assertSame(0, Property::published()->count());
        $this->assertSame(0, Service::published()->count());
        $this->assertSame(0, GalleryItem::query()->where('status', 'published')->count());
        $this->assertSame([], Setting::read('footer_gallery_images', []));
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


    public function test_legacy_demo_records_are_retired_without_deleting_or_rewriting_owner_data(): void
    {
        Storage::fake('public');

        $property = Property::query()->create([
            'title' => 'Legacy sample property',
            'slug' => 'the-oakwood-residences',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $service = Service::query()->create([
            'title' => 'Owner-reviewed valuation service',
            'slug' => 'property-valuation',
            'content' => '<p>Owner-reviewed service wording.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $serviceImage = ServiceImage::query()->create([
            'service_id' => $service->id,
            'path' => 'services/inside/seed-property-valuation.jpg',
            'alt_text' => 'Template photo',
            'sort_order' => 1,
        ]);
        $media = Media::query()->create([
            'disk' => 'public',
            'path' => $serviceImage->path,
            'original_name' => 'template-photo.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 0,
            'alt_text' => 'Template photo',
        ]);
        $gallery = GalleryItem::query()->create([
            'title' => 'Legacy template gallery image',
            'slug' => 'bareilly-property-gallery-1',
            'image' => 'gallery/buraaqh-reference-1.jpg',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $banner = Banner::query()->create([
            'title' => 'Discover a place that feels like home',
            'placement' => 'home',
            'is_active' => true,
        ]);
        $legacyPage = Page::query()->create([
            'title' => 'Legacy buy page',
            'slug' => 'buy-with-pillar',
            'content' => '<p>Owner-edited legacy page copy.</p>',
            'status' => 'published',
        ]);
        $legacyPost = Post::query()->create([
            'title' => 'Legacy moving article',
            'slug' => 'planning-a-delhi-ncr-move',
            'content' => '<p>Owner-edited legacy article copy.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->seed(BuraaqhContentSeeder::class);

        $this->assertSame('draft', $property->fresh()->status);
        $this->assertSame('draft', $service->fresh()->status);
        $this->assertSame('Owner-reviewed valuation service', $service->fresh()->title);
        $this->assertSame('<p>Owner-reviewed service wording.</p>', $service->fresh()->content);
        $this->assertSame(0, ServiceImage::query()->whereKey($serviceImage->id)->count());
        $this->assertDatabaseHas('media', ['id' => $media->id, 'path' => $media->path]);
        $this->assertSame('draft', $gallery->fresh()->status);
        $this->assertFalse((bool) $banner->fresh()->is_active);
        $this->assertSame('draft', $legacyPage->fresh()->status);
        $this->assertSame('<p>Owner-edited legacy page copy.</p>', $legacyPage->fresh()->content);
        $this->assertSame('draft', $legacyPost->fresh()->status);
        $this->assertSame('<p>Owner-edited legacy article copy.</p>', $legacyPost->fresh()->content);
        $this->assertDatabaseHas('pages', ['slug' => 'buy-with-buraaqh', 'status' => 'published']);
        $this->assertDatabaseHas('posts', ['slug' => 'planning-a-bareilly-move', 'status' => 'published']);
    }

    public function test_seeder_preserves_owner_managed_content_when_rerun(): void
    {
        Storage::fake('public');
        $this->seed();

        $faq = FaqItem::query()->firstOrFail();
        $faq->update(['answer' => 'Owner-approved FAQ wording.']);
        $guide = NeighborhoodGuide::query()->firstOrFail();
        $guide->update(['content' => '<p>Owner-approved neighborhood guide copy.</p>']);

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
        $this->assertSame('Owner-approved FAQ wording.', $faq->fresh()->answer);
        $this->assertSame('<p>Owner-approved neighborhood guide copy.</p>', $guide->fresh()->content);
    }
}
