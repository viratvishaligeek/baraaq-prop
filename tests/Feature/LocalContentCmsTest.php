<?php

namespace Tests\Feature;

use App\Models\FaqItem;
use App\Models\GalleryItem;
use App\Models\NeighborhoodGuide;
use App\Models\Post;
use App\Models\Property;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LocalContentCmsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_gallery_shows_only_published_items_and_supports_search(): void
    {
        GalleryItem::create([
            'title' => 'Bareilly home photography',
            'slug' => 'bareilly-home-photography',
            'image' => 'gallery/home.jpg',
            'status' => 'published',
            'published_at' => now(),
        ]);
        GalleryItem::create([
            'title' => 'Unpublished gallery item',
            'slug' => 'unpublished-gallery-item',
            'image' => 'gallery/draft.jpg',
            'status' => 'draft',
        ]);

        $this->get(route('gallery.index', ['q' => 'Bareilly']))
            ->assertOk()
            ->assertSee('Bareilly home photography')
            ->assertDontSee('Unpublished gallery item');
    }

    public function test_public_faq_page_renders_database_answers_and_faq_structured_data(): void
    {
        FaqItem::create([
            'question' => 'How should I verify property documents?',
            'answer' => 'Ask a qualified local professional to review the original documents.',
            'category' => 'Buying',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->get(route('faq.index', ['category' => 'Buying']))
            ->assertOk()
            ->assertSee('How should I verify property documents?')
            ->assertSee('FAQPage', false)
            ->assertSee('qualified local professional');
    }

    public function test_public_neighborhood_guide_requires_published_content(): void
    {
        $guide = NeighborhoodGuide::create([
            'title' => 'Civil Lines property considerations',
            'slug' => 'civil-lines-property-considerations',
            'locality' => 'Civil Lines',
            'city' => 'Bareilly',
            'state' => 'Uttar Pradesh',
            'content' => '<p>Verify the specific property and its documents independently.</p>',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->get(route('neighborhood-guides.show', $guide))
            ->assertOk()
            ->assertSee('Civil Lines property considerations')
            ->assertSee('Bareilly');

        $draft = NeighborhoodGuide::create([
            'title' => 'Draft guide',
            'slug' => 'draft-guide',
            'locality' => 'Izatnagar',
            'status' => 'draft',
        ]);

        $this->get(route('neighborhood-guides.show', $draft))->assertNotFound();
    }


    public function test_legacy_detail_template_urls_resolve_to_database_slugs_and_error_page_returns_404(): void
    {
        $property = Property::query()->create([
            'title' => 'Verified Bareilly property',
            'slug' => 'verified-bareilly-property',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $post = Post::query()->create([
            'title' => 'Bareilly property article',
            'slug' => 'bareilly-property-article',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $service = \App\Models\Service::query()->create([
            'title' => 'Property consultation',
            'slug' => 'property-consultation',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->get('/property-details.html?slug='.$property->slug)
            ->assertRedirect(route('properties.show', $property));
        $this->get('/blog-details.html?slug='.$post->slug)
            ->assertRedirect(route('blog.show', $post));
        $this->get('/service-details.html?slug='.$service->slug)
            ->assertRedirect(route('services.show', $service));

        $this->get('/error.html')
            ->assertNotFound()
            ->assertSee('We couldn’t find that page.');
    }

    public function test_admin_can_manage_service_features_and_remove_one_inside_image(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true]);
        $admin->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());
        Storage::fake('public');

        $this->actingAs($admin)->post(route('admin.content.store', 'services'), [
            'title' => 'Property advisory',
            'excerpt' => 'Guidance for informed decisions.',
            'content' => '<p>Service overview.</p>',
            'status' => 'draft',
            'features_json' => json_encode([
                ['title' => 'Document checklist', 'description' => 'Review required records.'],
                ['title' => 'Location comparison', 'description' => 'Compare suitable areas.'],
            ]),
            'inside_images' => [UploadedFile::fake()->image('inside-one.jpg'), UploadedFile::fake()->image('inside-two.jpg')],
        ])->assertRedirect(route('admin.content.index', 'services'));

        $service = \App\Models\Service::query()->where('slug', 'property-advisory')->firstOrFail();
        $this->assertDatabaseCount('service_features', 2);
        $this->assertDatabaseCount('service_images', 2);
        $imageId = $service->images()->firstOrFail()->id;

        $this->actingAs($admin)->put(route('admin.content.update', ['type' => 'services', 'id' => $service->id]), [
            'title' => 'Property advisory',
            'excerpt' => 'Updated guidance.',
            'content' => '<p>Updated service overview.</p>',
            'status' => 'published',
            'features_json' => json_encode([['title' => 'Updated document checklist']]),
            'remove_inside_images' => [$imageId],
        ])->assertRedirect(route('admin.content.index', 'services'));

        $this->assertDatabaseCount('service_features', 1);
        $this->assertDatabaseCount('service_images', 1);
        $this->get(route('services.show', $service->fresh()))
            ->assertOk()
            ->assertSee('Updated document checklist');
    }

    public function test_admin_can_create_faq_and_gallery_content_with_media_records(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true]);
        $admin->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());
        Storage::fake('public');

        $this->actingAs($admin)->post(route('admin.site-content.store', 'faqs'), [
            'question' => 'What should buyers verify?',
            'answer' => 'Review the documents with an independent professional.',
            'category' => 'Buying',
            'sort_order' => 1,
            'is_active' => '1',
        ])->assertRedirect(route('admin.site-content.index', 'faqs'));

        $this->assertDatabaseHas('faq_items', ['question' => 'What should buyers verify?']);
        $faq = FaqItem::query()->where('question', 'What should buyers verify?')->firstOrFail();

        $this->actingAs($admin)->put(route('admin.site-content.update', ['type' => 'faqs', 'id' => $faq->id]), [
            'question' => $faq->question,
            'answer' => 'Updated owner-approved answer.',
            'category' => 'Buying',
            'sort_order' => 1,
            'is_active' => '0',
        ])->assertRedirect(route('admin.site-content.index', 'faqs'));
        $this->assertDatabaseHas('faq_items', ['id' => $faq->id, 'answer' => 'Updated owner-approved answer.', 'is_active' => false]);

        $this->actingAs($admin)->post(route('admin.site-content.store', 'gallery'), [
            'title' => 'Bareilly property gallery image',
            'description' => 'A published gallery entry.',
            'image' => UploadedFile::fake()->image('property.jpg', 1200, 800),
            'alt_text' => 'Residential property exterior',
            'status' => 'published',
            'sort_order' => 1,
        ])->assertRedirect(route('admin.site-content.index', 'gallery'));

        $this->assertDatabaseHas('gallery_items', ['title' => 'Bareilly property gallery image', 'status' => 'published']);
        $this->assertDatabaseHas('media', ['original_name' => 'property.jpg']);
        $gallery = GalleryItem::query()->where('title', 'Bareilly property gallery image')->firstOrFail();
        $storedPath = $gallery->image;

        $this->actingAs($admin)->put(route('admin.site-content.update', ['type' => 'gallery', 'id' => $gallery->id]), [
            'title' => $gallery->title,
            'description' => $gallery->description,
            'alt_text' => $gallery->alt_text,
            'status' => 'draft',
            'remove_image' => '1',
        ])->assertRedirect(route('admin.site-content.index', 'gallery'));

        $this->assertDatabaseHas('gallery_items', ['id' => $gallery->id, 'image' => null]);
        Storage::disk('public')->assertExists($storedPath);

        $guideUpload = UploadedFile::fake()->image('neighborhood.jpg', 1000, 700);
        $this->actingAs($admin)->post(route('admin.site-content.store', 'neighborhood-guides'), [
            'title' => 'Bareilly neighborhood guide',
            'slug' => 'bareilly-neighborhood-guide',
            'locality' => 'Civil Lines',
            'city' => 'Bareilly',
            'state' => 'Uttar Pradesh',
            'excerpt' => 'A practical locality checklist.',
            'content' => '<p>Review current local conditions.</p>',
            'image' => $guideUpload,
            'alt_text' => 'Bareilly neighborhood street',
            'status' => 'published',
            'sort_order' => 1,
        ])->assertRedirect(route('admin.site-content.index', 'neighborhood-guides'));

        $guide = NeighborhoodGuide::query()->where('slug', 'bareilly-neighborhood-guide')->firstOrFail();
        $guidePath = $guide->hero_image;
        $this->assertDatabaseHas('media', ['original_name' => 'neighborhood.jpg']);

        $this->actingAs($admin)->put(route('admin.site-content.update', ['type' => 'neighborhood-guides', 'id' => $guide->id]), [
            'title' => $guide->title,
            'slug' => $guide->slug,
            'locality' => $guide->locality,
            'city' => $guide->city,
            'state' => $guide->state,
            'excerpt' => 'Updated locality checklist.',
            'content' => '<p>Updated guide copy.</p>',
            'alt_text' => $guide->alt_text,
            'status' => 'draft',
            'remove_image' => '1',
            'sort_order' => 1,
        ])->assertRedirect(route('admin.site-content.index', 'neighborhood-guides'));

        $this->assertDatabaseHas('neighborhood_guides', ['id' => $guide->id, 'hero_image' => null, 'status' => 'draft']);
        Storage::disk('public')->assertExists($guidePath);

        $this->actingAs($admin)->delete(route('admin.site-content.destroy', ['type' => 'neighborhood-guides', 'id' => $guide->id]))
            ->assertRedirect(route('admin.site-content.index', 'neighborhood-guides'));
        $this->assertSoftDeleted('neighborhood_guides', ['id' => $guide->id]);

        $this->actingAs($admin)->delete(route('admin.site-content.destroy', ['type' => 'gallery', 'id' => $gallery->id]))
            ->assertRedirect(route('admin.site-content.index', 'gallery'));
        $this->assertSoftDeleted('gallery_items', ['id' => $gallery->id]);

        $galleryMedia = \\App\\Models\\Media::query()->where('path', $storedPath)->firstOrFail();
        $this->actingAs($admin)->delete(route('admin.media.destroy', $galleryMedia))
            ->assertRedirect();
        $this->assertDatabaseMissing('media', ['id' => $galleryMedia->id]);
        Storage::disk('public')->assertMissing($storedPath);

        $this->actingAs($admin)->delete(route('admin.site-content.destroy', ['type' => 'faqs', 'id' => $faq->id]))
            ->assertRedirect(route('admin.site-content.index', 'faqs'));
        $this->assertSoftDeleted('faq_items', ['id' => $faq->id]);
    }
}
