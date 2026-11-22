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
}
