<?php

namespace Tests\Feature;

use App\Models\Homepage;
use App\Models\HomepageSection;
use App\Models\Media;
use App\Models\Page;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminMediaIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        return $user;
    }

    public function test_media_used_by_a_page_open_graph_image_cannot_be_deleted(): void
    {
        Storage::fake('public');
        $admin = $this->administrator();
        $media = Media::create([
            'uploaded_by' => $admin->id,
            'disk' => 'public',
            'path' => 'media/page-og.png',
            'original_name' => 'page-og.png',
            'mime_type' => 'image/png',
            'size' => 10,
        ]);
        Storage::disk('public')->put($media->path, 'image-data');

        Page::create([
            'title' => 'Page with social image',
            'slug' => 'page-with-social-image',
            'status' => 'published',
            'og_image' => $media->path,
        ]);

        $this->actingAs($admin)->from(route('admin.media.index'))
            ->delete(route('admin.media.destroy', $media))
            ->assertSessionHasErrors('media');

        $this->assertDatabaseHas('media', ['id' => $media->id]);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_media_embedded_in_rich_text_content_cannot_be_deleted(): void
    {
        Storage::fake('public');
        $admin = $this->administrator();
        $media = Media::create([
            'uploaded_by' => $admin->id,
            'disk' => 'public',
            'path' => 'media/inline-content-image.png',
            'original_name' => 'inline-content-image.png',
            'mime_type' => 'image/png',
            'size' => 10,
        ]);
        Storage::disk('public')->put($media->path, 'image-data');
        Page::create([
            'title' => 'Page with inline media',
            'slug' => 'page-with-inline-media',
            'content' => '<p><img src="https://example.test/storage/media/inline-content-image.png" alt="Inline"></p>',
            'status' => 'published',
        ]);

        $this->actingAs($admin)->from(route('admin.media.index'))
            ->delete(route('admin.media.destroy', $media))
            ->assertSessionHasErrors('media');

        $this->assertDatabaseHas('media', ['id' => $media->id]);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_media_id_in_legacy_homepage_section_config_cannot_be_deleted(): void
    {
        Storage::fake('public');
        $admin = $this->administrator();
        $media = Media::create([
            'uploaded_by' => $admin->id,
            'disk' => 'public',
            'path' => 'media/legacy-homepage-hero.png',
            'original_name' => 'legacy-homepage-hero.png',
            'mime_type' => 'image/png',
            'size' => 10,
        ]);
        Storage::disk('public')->put($media->path, 'image-data');

        $homepage = Homepage::create([
            'name' => 'Home 9',
            'slug' => 'home-9',
            'status' => 'published',
            'published_at' => now(),
        ]);
        HomepageSection::create([
            'homepage_id' => $homepage->id,
            'section_key' => 'hero.home-9',
            'section_type' => 'hero',
            'sort_order' => 1,
            'is_enabled' => true,
            'config' => ['hero_image' => $media->id],
        ]);

        $this->actingAs($admin)->from(route('admin.media.index'))
            ->delete(route('admin.media.destroy', $media))
            ->assertSessionHasErrors('media');

        $this->assertDatabaseHas('media', ['id' => $media->id]);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_media_path_stored_in_the_footer_gallery_cannot_be_deleted(): void
    {
        Storage::fake('public');
        $admin = $this->administrator();
        $media = Media::create([
            'uploaded_by' => $admin->id,
            'disk' => 'public',
            'path' => 'media/footer-gallery-path.png',
            'original_name' => 'footer-gallery-path.png',
            'mime_type' => 'image/png',
            'size' => 10,
        ]);
        Storage::disk('public')->put($media->path, 'image-data');
        Setting::write('footer_gallery_images', [$media->path], 'footer', 'json');

        $this->actingAs($admin)->from(route('admin.media.index'))
            ->delete(route('admin.media.destroy', $media))
            ->assertSessionHasErrors('media');

        $this->assertDatabaseHas('media', ['id' => $media->id]);
        Storage::disk('public')->assertExists($media->path);
    }

    public function test_media_used_by_the_footer_gallery_setting_cannot_be_deleted(): void
    {
        Storage::fake('public');
        $admin = $this->administrator();
        $media = Media::create([
            'uploaded_by' => $admin->id,
            'disk' => 'public',
            'path' => 'media/footer-gallery.png',
            'original_name' => 'footer-gallery.png',
            'mime_type' => 'image/png',
            'size' => 10,
        ]);
        Storage::disk('public')->put($media->path, 'image-data');
        Setting::write('footer_gallery_images', [$media->id], 'footer', 'json');

        $this->actingAs($admin)->from(route('admin.media.index'))
            ->delete(route('admin.media.destroy', $media))
            ->assertSessionHasErrors('media');

        $this->assertDatabaseHas('media', ['id' => $media->id]);
        Storage::disk('public')->assertExists($media->path);
    }
}
