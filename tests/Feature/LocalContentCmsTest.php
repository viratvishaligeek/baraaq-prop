<?php

namespace Tests\Feature;

use App\Models\FaqItem;
use App\Models\GalleryItem;
use App\Models\NeighborhoodGuide;
use App\Models\Role;
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
    }
}
