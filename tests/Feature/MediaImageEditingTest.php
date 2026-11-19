<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaImageEditingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function test_media_upload_stores_original_and_generated_responsive_variants(): void
    {
        $user = $this->administrator();
        $original = UploadedFile::fake()->image('property.jpg', 1600, 1200);
        $small = UploadedFile::fake()->image('property-small.webp', 320, 240);
        $medium = UploadedFile::fake()->image('property-medium.webp', 900, 675);
        $large = UploadedFile::fake()->image('property-large.webp', 1600, 1200);

        $response = $this->actingAs($user)->post(route('admin.media.store'), [
            'files' => [$original],
            'variants' => [0 => ['small' => $small, 'medium' => $medium, 'large' => $large]],
        ]);

        $response->assertRedirect();
        $media = Media::query()->where('original_name', 'property.jpg')->firstOrFail();

        $this->assertNotNull($media->small_path);
        $this->assertNotNull($media->medium_path);
        $this->assertNotNull($media->large_path);
        Storage::disk('public')->assertExists($media->path);
        Storage::disk('public')->assertExists($media->small_path);
        Storage::disk('public')->assertExists($media->medium_path);
        Storage::disk('public')->assertExists($media->large_path);
        $this->assertSame($media->small_path, $media->fresh()->small_path);
    }

    public function test_admin_can_save_a_cropped_or_resized_image_as_a_new_media_copy(): void
    {
        $user = $this->administrator();
        $original = Media::create([
            'uploaded_by' => $user->id,
            'disk' => 'public',
            'path' => 'media/original.webp',
            'original_name' => 'original.webp',
            'mime_type' => 'image/webp',
            'size' => 2048,
            'alt_text' => 'Original image',
        ]);
        Storage::disk('public')->put($original->path, 'original-image-bytes');

        $response = $this->actingAs($user)->postJson(route('admin.media.edited-copy', $original), [
            'image' => UploadedFile::fake()->image('edited.webp', 800, 600),
            'alt_text' => 'Edited property image',
        ]);

        $response->assertCreated()->assertJsonPath('saved', true);
        $copy = Media::query()->where('original_name', 'original-edited.webp')->firstOrFail();

        $this->assertNotSame($original->path, $copy->path);
        $this->assertSame('Edited property image', $copy->alt_text);
        Storage::disk('public')->assertExists($original->path);
        Storage::disk('public')->assertExists($copy->path);
    }

    public function test_non_administrators_without_media_permission_cannot_edit_media(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $media = Media::create([
            'disk' => 'public',
            'path' => 'media/private.webp',
            'original_name' => 'private.webp',
            'mime_type' => 'image/webp',
            'size' => 100,
        ]);

        $this->actingAs($user)->postJson(route('admin.media.edited-copy', $media), [
            'image' => UploadedFile::fake()->image('edited.webp'),
        ])->assertForbidden();
    }

    private function administrator(): User
    {
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Administrator', 'slug' => 'administrator']);
        $permission = Permission::create(['name' => 'Manage media', 'slug' => 'media.manage', 'group' => 'media']);
        $dashboardPermission = Permission::create(['name' => 'View dashboard', 'slug' => 'dashboard.view', 'group' => 'dashboard']);
        $role->permissions()->attach([$permission->id, $dashboardPermission->id]);
        $user->roles()->attach($role);

        return $user;
    }
}
