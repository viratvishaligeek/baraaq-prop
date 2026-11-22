<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\Property;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PropertyAccommodationImageUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_accommodation_image_upload_creates_media_and_responsive_variants(): void
    {
        Storage::fake('public');
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        $response = $this->actingAs($user)->post(route('admin.properties.store'), [
            'title' => 'Accommodation images property',
            'slug' => 'accommodation-images-property',
            'listing_type' => 'sale',
            'area_unit' => 'sqft',
            'status' => 'draft',
            'accommodations' => [
                0 => [
                    'name' => 'Master bedroom',
                    'image' => UploadedFile::fake()->image('master-bedroom.jpg', 1600, 1200),
                    'is_included' => '1',
                ],
                1 => ['name' => 'Kitchen', 'is_included' => '1'],
            ],
            'faqs' => [
                0 => ['question' => 'Is parking included?', 'answer' => 'Yes.'],
                1 => ['question' => 'Are pets allowed?', 'answer' => 'Ask our team.'],
            ],
            'accommodation_variants' => [
                0 => [
                    'small' => UploadedFile::fake()->image('master-small.webp', 320, 240),
                    'medium' => UploadedFile::fake()->image('master-medium.webp', 900, 675),
                    'large' => UploadedFile::fake()->image('master-large.webp', 1600, 1200),
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.properties.index'));
        $property = Property::where('slug', 'accommodation-images-property')->firstOrFail();
        $space = $property->accommodations()->firstOrFail();
        $media = Media::findOrFail($space->image_media_id);
        $spaces = $property->accommodations()->orderBy('sort_order')->get();
        $faqs = $property->faqs()->orderBy('sort_order')->get();
        $this->assertSame(['Master bedroom', 'Kitchen'], $spaces->pluck('name')->all());
        $this->assertSame([0, 1], $spaces->pluck('sort_order')->all());
        $this->assertSame([0, 1], $faqs->pluck('sort_order')->all());

        $this->assertSame('master-bedroom.jpg', $media->original_name);
        $this->assertNotNull($media->small_path);
        $this->assertNotNull($media->medium_path);
        $this->assertNotNull($media->large_path);
        Storage::disk('public')->assertExists($media->path);
        Storage::disk('public')->assertExists($media->small_path);
        Storage::disk('public')->assertExists($media->medium_path);
        Storage::disk('public')->assertExists($media->large_path);
    }

    public function test_existing_media_library_image_can_be_selected_for_an_accommodation_and_survives_property_update(): void
    {
        Storage::fake('public');
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        $media = Media::create([
            'uploaded_by' => $user->id,
            'disk' => 'public',
            'path' => 'media/existing-kitchen.png',
            'original_name' => 'existing-kitchen.png',
            'mime_type' => 'image/png',
            'size' => 12,
            'alt_text' => 'Kitchen',
        ]);
        Storage::disk('public')->put($media->path, 'image-data');

        $this->actingAs($user)->post(route('admin.properties.store'), [
            'title' => 'Media library accommodation property',
            'slug' => 'media-library-accommodation-property',
            'listing_type' => 'sale',
            'area_unit' => 'sqft',
            'status' => 'draft',
            'accommodations' => [[
                'name' => 'Kitchen',
                'image_media_id' => (string) $media->id,
                'is_included' => '1',
            ]],
        ])->assertRedirect(route('admin.properties.index'));

        $property = Property::query()->where('slug', 'media-library-accommodation-property')->firstOrFail();
        $space = $property->accommodations()->firstOrFail();
        $this->assertSame($media->id, $space->image_media_id);
        $this->assertSame($media->url, $space->imageMedia->url);

        $this->actingAs($user)->put(route('admin.properties.update', $property), [
            'title' => 'Media library accommodation property updated',
            'slug' => 'media-library-accommodation-property',
            'listing_type' => 'sale',
            'area_unit' => 'sqft',
            'status' => 'draft',
            'accommodations' => [[
                'name' => 'Kitchen',
                'image_media_id' => (string) $media->id,
                'is_included' => '1',
            ]],
        ])->assertRedirect(route('admin.properties.index'));

        $updatedSpace = $property->fresh()->accommodations()->firstOrFail();
        $this->assertSame($media->id, $updatedSpace->image_media_id);
        $this->assertDatabaseHas('media', ['id' => $media->id, 'path' => 'media/existing-kitchen.png']);
        Storage::disk('public')->assertExists($media->path);
    }

}
