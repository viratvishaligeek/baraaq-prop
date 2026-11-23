<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyMediaRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_removing_gallery_relationship_does_not_clear_featured_image_or_delete_shared_media(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true]);
        $admin->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());
        Storage::fake('public');
        Storage::disk('public')->put('properties/shared-featured.jpg', 'shared property image');

        $media = Media::query()->create([
            'uploaded_by' => $admin->id,
            'disk' => 'public',
            'path' => 'properties/shared-featured.jpg',
            'original_name' => 'shared-featured.jpg',
            'mime_type' => 'image/jpeg',
            'size' => 20,
            'alt_text' => 'Property exterior',
        ]);

        $property = Property::query()->create([
            'title' => 'Bareilly independent home',
            'slug' => 'bareilly-independent-home',
            'listing_type' => 'sale',
            'status' => 'draft',
            'featured_image' => $media->path,
            'created_by' => $admin->id,
        ]);
        $image = $property->images()->create(['path' => $media->path, 'sort_order' => 1]);

        $this->actingAs($admin)
            ->delete(route('admin.properties.images.destroy', [$property, $image]))
            ->assertRedirect();

        $this->assertSame($media->path, $property->fresh()->featured_image);
        $this->assertDatabaseMissing('property_images', ['id' => $image->id]);
        $this->assertDatabaseHas('media', ['id' => $media->id, 'path' => $media->path]);
        Storage::disk('public')->assertExists($media->path);
    }
}
