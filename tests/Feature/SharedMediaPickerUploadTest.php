<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\MediaPickerController;
use App\Models\Media;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SharedMediaPickerUploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_shared_picker_uploads_images_into_the_existing_media_library(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->roles()->attach(Role::create(['name' => 'Administrator', 'slug' => 'administrator']));
        $file = UploadedFile::fake()->image('piller-widget-image.png', 640, 480);

        $request = Request::create('/admin/media/picker', 'POST');
        $request->files->set('files', [$file]);
        $request->files->set('variants', [0 => [
            'small' => UploadedFile::fake()->image('piller-small.webp', 320, 240),
            'medium' => UploadedFile::fake()->image('piller-medium.webp', 900, 675),
            'large' => UploadedFile::fake()->image('piller-large.webp', 1600, 1200),
        ]]);
        $request->setUserResolver(fn () => $user);

        $response = app(MediaPickerController::class)->upload($request);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertDatabaseHas('media', [
            'uploaded_by' => $user->id,
            'original_name' => 'piller-widget-image.png',
            'mime_type' => 'image/png',
            'disk' => 'public',
        ]);

        $media = Media::query()->where('original_name', 'piller-widget-image.png')->firstOrFail();
        Storage::disk('public')->assertExists($media->path);
        $this->assertNotNull($media->small_path);
        $this->assertNotNull($media->medium_path);
        $this->assertNotNull($media->large_path);
        Storage::disk('public')->assertExists($media->small_path);
        $this->assertSame($media->url, $response->getData(true)['data'][0]['url']);
    }

    public function test_shared_picker_rejects_non_image_uploads(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $user->roles()->attach(Role::create(['name' => 'Administrator', 'slug' => 'administrator']));
        $file = UploadedFile::fake()->create('not-an-image.pdf', 20, 'application/pdf');

        $request = Request::create('/admin/media/picker', 'POST');
        $request->files->set('files', [$file]);
        $request->setUserResolver(fn () => $user);

        try {
            app(MediaPickerController::class)->upload($request);
            $this->fail('A non-image file should not be accepted by the image picker.');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertArrayHasKey('files.0', $exception->errors());
        }

        $this->assertDatabaseCount('media', 0);
    }
}
