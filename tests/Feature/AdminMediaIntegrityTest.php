<?php

namespace Tests\Feature;

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
