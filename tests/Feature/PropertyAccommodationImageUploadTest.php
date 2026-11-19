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

        $this->assertSame('master-bedroom.jpg', $media->original_name);
        $this->assertNotNull($media->small_path);
        $this->assertNotNull($media->medium_path);
        $this->assertNotNull($media->large_path);
        Storage::disk('public')->assertExists($media->path);
        Storage::disk('public')->assertExists($media->small_path);
        Storage::disk('public')->assertExists($media->medium_path);
        Storage::disk('public')->assertExists($media->large_path);
    }
}
