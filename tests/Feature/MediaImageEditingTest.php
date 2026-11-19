<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
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
            'variants' => [
                'small' => UploadedFile::fake()->image('edited-small.webp', 320, 240),
                'medium' => UploadedFile::fake()->image('edited-medium.webp', 800, 600),
                'large' => UploadedFile::fake()->image('edited-large.webp', 1200, 900),
            ],
            'alt_text' => 'Edited property image',
        ]);

        $response->assertCreated()->assertJsonPath('saved', true);
        $copy = Media::query()->where('original_name', 'original-edited.webp')->firstOrFail();

        $this->assertNotSame($original->path, $copy->path);
        $this->assertSame('Edited property image', $copy->alt_text);
        $this->assertNotNull($copy->small_path);
        $this->assertNotNull($copy->medium_path);
        $this->assertNotNull($copy->large_path);
        Storage::disk('public')->assertExists($copy->small_path);
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


    public function test_media_referenced_by_a_property_accommodation_cannot_be_deleted(): void
    {
        $user = $this->administrator();
        $property = \App\Models\Property::create([
            'title' => 'Media protected property',
            'slug' => 'media-protected-property',
            'status' => 'draft',
        ]);
        $media = Media::create([
            'uploaded_by' => $user->id,
            'disk' => 'public',
            'path' => 'media/accommodation-protected.webp',
            'original_name' => 'accommodation-protected.webp',
            'mime_type' => 'image/webp',
            'size' => 100,
        ]);
        Storage::disk('public')->put($media->path, 'image');
        $property->accommodations()->create([
            'image_media_id' => $media->id,
            'name' => 'Kitchen',
            'is_included' => true,
        ]);

        $this->actingAs($user)
            ->delete(route('admin.media.destroy', $media))
            ->assertRedirect()
            ->assertSessionHasErrors('media');

        $this->assertDatabaseHas('media', ['id' => $media->id]);
        Storage::disk('public')->assertExists($media->path);
    }

    private function administrator(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        return $user;
    }
}
