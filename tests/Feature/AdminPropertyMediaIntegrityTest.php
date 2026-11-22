<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Property;
use App\Models\PropertyAccommodation;
use App\Models\PropertyImage;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminPropertyMediaIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        return $user;
    }

    public function test_property_featured_og_gallery_and_accommodation_images_are_registered_atomically(): void
    {
        Storage::fake('public');
        $admin = $this->administrator();

        $this->actingAs($admin)->post(route('admin.properties.store'), [
            'title' => 'Media-integrated residence',
            'property_type' => 'Apartment',
            'listing_type' => 'sale',
            'area_unit' => 'sqft',
            'status' => 'draft',
            'featured_image' => UploadedFile::fake()->image('featured.png'),
            'og_image' => UploadedFile::fake()->image('social.png'),
            'gallery' => [UploadedFile::fake()->image('gallery.png')],
            'accommodations' => [[
                'name' => 'Kitchen',
                'image' => UploadedFile::fake()->image('kitchen.png'),
                'is_included' => '1',
            ]],
            'accommodation_variants' => [
                0 => ['small' => UploadedFile::fake()->image('kitchen-small.png')],
            ],
        ])->assertRedirect(route('admin.properties.index'));

        $property = Property::query()->where('title', 'Media-integrated residence')->firstOrFail();
        $this->assertNotEmpty($property->featured_image);
        $this->assertNotEmpty($property->og_image);
        $this->assertDatabaseHas('media', ['path' => $property->featured_image, 'original_name' => 'featured.png']);
        $this->assertDatabaseHas('media', ['path' => $property->og_image, 'original_name' => 'social.png']);

        $galleryImage = $property->images()->firstOrFail();
        $this->assertDatabaseHas('media', ['path' => $galleryImage->path, 'original_name' => 'gallery.png']);

        $accommodation = PropertyAccommodation::query()->where('property_id', $property->id)->firstOrFail();
        $accommodationMedia = Media::query()->findOrFail($accommodation->image_media_id);
        $this->assertNotEmpty($accommodationMedia->small_path);

        foreach ([$property->featured_image, $property->og_image, $galleryImage->path, $accommodationMedia->path, $accommodationMedia->small_path] as $path) {
            Storage::disk('public')->assertExists($path);
        }

        $this->assertCount(4, Media::query()->get());
    }

    public function test_replacing_a_legacy_property_image_deletes_only_the_unreferenced_old_file(): void
    {
        Storage::fake('public');
        $admin = $this->administrator();
        Storage::disk('public')->put('properties/legacy-featured.jpg', 'old-image');

        $property = Property::create([
            'title' => 'Legacy residence',
            'slug' => 'legacy-residence',
            'property_type' => 'Apartment',
            'listing_type' => 'sale',
            'area_unit' => 'sqft',
            'status' => 'draft',
            'featured_image' => 'properties/legacy-featured.jpg',
            'created_by' => $admin->id,
        ]);

        $this->actingAs($admin)->put(route('admin.properties.update', $property), [
            'title' => 'Legacy residence updated',
            'property_type' => 'Apartment',
            'listing_type' => 'sale',
            'area_unit' => 'sqft',
            'status' => 'draft',
            'featured_image' => UploadedFile::fake()->image('replacement.png'),
        ])->assertRedirect(route('admin.properties.index'));

        $property->refresh();
        $this->assertNotSame('properties/legacy-featured.jpg', $property->featured_image);
        Storage::disk('public')->assertMissing('properties/legacy-featured.jpg');
        Storage::disk('public')->assertExists($property->featured_image);
        $this->assertDatabaseHas('media', ['path' => $property->featured_image, 'original_name' => 'replacement.png']);
    }

    public function test_deleting_a_property_gallery_entry_does_not_delete_its_media_library_file(): void
    {
        Storage::fake('public');
        $admin = $this->administrator();
        Storage::disk('public')->put('media/property-gallery.png', 'image-data');

        $media = Media::create([
            'uploaded_by' => $admin->id,
            'disk' => 'public',
            'path' => 'media/property-gallery.png',
            'original_name' => 'property-gallery.png',
            'mime_type' => 'image/png',
            'size' => 10,
        ]);

        $property = Property::create([
            'title' => 'Gallery residence',
            'slug' => 'gallery-residence',
            'property_type' => 'Apartment',
            'listing_type' => 'sale',
            'area_unit' => 'sqft',
            'status' => 'draft',
            'created_by' => $admin->id,
        ]);
        $image = PropertyImage::create([
            'property_id' => $property->id,
            'path' => $media->path,
            'sort_order' => 0,
        ]);

        $this->actingAs($admin)->from(route('admin.properties.edit', $property))
            ->delete(route('admin.properties.images.destroy', [$property, $image]))
            ->assertRedirect();

        $this->assertDatabaseMissing('property_images', ['id' => $image->id]);
        $this->assertDatabaseHas('media', ['id' => $media->id]);
        Storage::disk('public')->assertExists($media->path);
    }
}
