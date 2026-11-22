<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminContentIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        return $user;
    }

    public function test_page_image_upload_is_registered_in_media_and_saved_with_the_page(): void
    {
        Storage::fake('public');
        $admin = $this->administrator();

        $this->actingAs($admin)->post(route('admin.content.store', 'pages'), [
            'title' => 'Company information',
            'slug' => 'company-information',
            'content' => '<p>About our company</p>',
            'status' => 'published',
            'og_image' => UploadedFile::fake()->image('company-og.png'),
        ])->assertRedirect(route('admin.content.index', 'pages'));

        $page = \App\Models\Page::query()->where('slug', 'company-information')->firstOrFail();
        $this->assertNotEmpty($page->og_image);
        $this->assertDatabaseHas('media', ['path' => $page->og_image, 'original_name' => 'company-og.png']);
        Storage::disk('public')->assertExists($page->og_image);
    }

    public function test_banner_button_rejects_unsafe_javascript_links(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)->post(route('admin.content.store', 'banners'), [
            'title' => 'Unsafe banner',
            'placement' => 'homepage',
            'button_text' => 'Open',
            'button_url' => 'javascript:alert(1)',
            'is_active' => '1',
        ])->assertSessionHasErrors('button_url');
    }

    public function test_team_social_links_reject_unsafe_or_non_http_schemes(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)->post(route('admin.content.store', 'team'), [
            'name' => 'Team member',
            'status' => 'draft',
            'social_links' => json_encode(['facebook' => 'javascript:alert(1)']),
            'is_active' => '1',
        ])->assertSessionHasErrors('social_links');
    }

    public function test_service_featured_and_social_images_persist_on_create_and_update(): void
    {
        Storage::fake('public');
        $admin = $this->administrator();

        $this->actingAs($admin)->post(route('admin.content.store', 'services'), [
            'title' => 'Image-backed service',
            'slug' => 'image-backed-service',
            'excerpt' => 'A service with managed images.',
            'content' => '<p>Service content.</p>',
            'status' => 'draft',
            'featured_image' => UploadedFile::fake()->image('service-featured.png'),
            'og_image' => UploadedFile::fake()->image('service-social.png'),
        ])->assertRedirect(route('admin.content.index', 'services'));

        $service = \\App\\Models\\Service::query()->where('slug', 'image-backed-service')->firstOrFail();
        $this->assertDatabaseHas('media', [
            'path' => $service->featured_image,
            'original_name' => 'service-featured.png',
        ]);
        $this->assertDatabaseHas('media', [
            'path' => $service->og_image,
            'original_name' => 'service-social.png',
        ]);
        Storage::disk('public')->assertExists($service->featured_image);
        Storage::disk('public')->assertExists($service->og_image);

        $this->actingAs($admin)->put(route('admin.content.update', ['services', $service->id]), [
            'title' => 'Image-backed service updated',
            'slug' => 'image-backed-service',
            'excerpt' => 'Updated service summary.',
            'content' => '<p>Updated service content.</p>',
            'status' => 'draft',
            'featured_image' => UploadedFile::fake()->image('service-featured-updated.png'),
        ])->assertRedirect(route('admin.content.index', 'services'));

        $service->refresh();
        $this->assertDatabaseHas('media', [
            'path' => $service->featured_image,
            'original_name' => 'service-featured-updated.png',
        ]);
        Storage::disk('public')->assertExists($service->featured_image);
        $this->assertNotEmpty($service->og_image);
        Storage::disk('public')->assertExists($service->og_image);
    }

}
