<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Role;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminContentCrudMatrixTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        return $user;
    }

    public function test_all_generic_admin_content_modules_create_update_delete_and_keep_uploaded_images(): void
    {
        Storage::fake('public');
        $admin = $this->administrator();

        $modules = [
            'posts' => [
                'model' => Post::class,
                'table' => 'posts',
                'lookup' => 'title',
                'payload' => [
                    'title' => 'CRUD matrix post',
                    'slug' => 'crud-matrix-post',
                    'excerpt' => 'A complete post record.',
                    'content' => '<p>Post content.</p>',
                    'status' => 'draft',
                    'featured_image' => UploadedFile::fake()->image('post-featured.png'),
                    'og_image' => UploadedFile::fake()->image('post-social.png'),
                ],
                'updated' => ['title' => 'CRUD matrix post updated', 'slug' => 'crud-matrix-post'],
                'images' => ['featured_image', 'og_image'],
            ],
            'pages' => [
                'model' => Page::class,
                'table' => 'pages',
                'lookup' => 'title',
                'payload' => [
                    'title' => 'CRUD matrix page',
                    'slug' => 'crud-matrix-page',
                    'content' => '<p>Page content.</p>',
                    'status' => 'draft',
                    'og_image' => UploadedFile::fake()->image('page-social.png'),
                ],
                'updated' => ['title' => 'CRUD matrix page updated', 'slug' => 'crud-matrix-page'],
                'images' => ['og_image'],
            ],
            'categories' => [
                'model' => Category::class,
                'table' => 'categories',
                'lookup' => 'name',
                'payload' => [
                    'name' => 'CRUD matrix category',
                    'slug' => 'crud-matrix-category',
                    'type' => 'property',
                    'description' => 'Category description.',
                    'is_active' => '1',
                    'icon_path' => UploadedFile::fake()->image('category-icon.png'),
                ],
                'updated' => ['name' => 'CRUD matrix category updated', 'slug' => 'crud-matrix-category', 'type' => 'property'],
                'images' => ['icon_path'],
            ],
            'banners' => [
                'model' => Banner::class,
                'table' => 'banners',
                'lookup' => 'title',
                'payload' => [
                    'title' => 'CRUD matrix banner',
                    'placement' => 'homepage',
                    'description' => 'Banner description.',
                    'button_text' => 'Contact us',
                    'button_url' => '/contact',
                    'is_active' => '1',
                    'image' => UploadedFile::fake()->image('banner-image.png'),
                ],
                'updated' => ['title' => 'CRUD matrix banner updated', 'placement' => 'homepage'],
                'images' => ['image'],
            ],
            'team' => [
                'model' => TeamMember::class,
                'table' => 'team_members',
                'lookup' => 'name',
                'payload' => [
                    'name' => 'CRUD matrix team member',
                    'designation' => 'Property advisor',
                    'status' => 'draft',
                    'social_links' => '{}',
                    'is_active' => '1',
                    'photo' => UploadedFile::fake()->image('team-photo.png'),
                ],
                'updated' => ['name' => 'CRUD matrix team member updated', 'status' => 'draft'],
                'images' => ['photo'],
            ],
            'services' => [
                'model' => Service::class,
                'table' => 'services',
                'lookup' => 'title',
                'payload' => [
                    'title' => 'CRUD matrix service',
                    'slug' => 'crud-matrix-service',
                    'excerpt' => 'Service summary.',
                    'content' => '<p>Service content.</p>',
                    'status' => 'draft',
                    'featured_image' => UploadedFile::fake()->image('service-featured.png'),
                    'og_image' => UploadedFile::fake()->image('service-social.png'),
                ],
                'updated' => ['title' => 'CRUD matrix service updated', 'slug' => 'crud-matrix-service'],
                'images' => ['featured_image', 'og_image'],
            ],
            'testimonials' => [
                'model' => Testimonial::class,
                'table' => 'testimonials',
                'lookup' => 'name',
                'payload' => [
                    'name' => 'CRUD matrix client',
                    'designation' => 'Home buyer',
                    'company' => 'Local client',
                    'quote' => 'The service was clear and professional.',
                    'rating' => '5',
                    'status' => 'draft',
                    'is_active' => '1',
                    'photo' => UploadedFile::fake()->image('testimonial-photo.png'),
                ],
                'updated' => ['name' => 'CRUD matrix client updated', 'quote' => 'Updated testimonial quote.', 'status' => 'draft'],
                'images' => ['photo'],
            ],
        ];

        foreach ($modules as $type => $module) {
            $this->actingAs($admin)
                ->post(route('admin.content.store', $type), $module['payload'])
                ->assertRedirect(route('admin.content.index', $type));

            $modelClass = $module['model'];
            $item = $modelClass::query()
                ->where($module['lookup'], $module['payload'][$module['lookup']])
                ->firstOrFail();

            foreach ($module['images'] as $field) {
                $path = $item->{$field};
                $this->assertNotEmpty($path, "{$type}.{$field} was not persisted.");
                Storage::disk('public')->assertExists($path);
                $this->assertDatabaseHas('media', ['path' => $path]);
            }

            $this->actingAs($admin)
                ->put(route('admin.content.update', [$type, $item->id]), array_merge($module['payload'], $module['updated']))
                ->assertRedirect(route('admin.content.index', $type));

            $item->refresh();
            $this->assertSame(
                $module['updated'][$module['lookup']],
                $item->{$module['lookup']},
                "{$type} did not persist its update."
            );

            foreach ($module['images'] as $field) {
                $this->assertNotEmpty($item->{$field}, "{$type}.{$field} was cleared by an unrelated update.");
                Storage::disk('public')->assertExists($item->{$field});
            }

            $this->actingAs($admin)
                ->delete(route('admin.content.destroy', [$type, $item->id]))
                ->assertRedirect();

            if (Schema::hasColumn($module['table'], 'deleted_at')) {
                $this->assertNotNull(DB::table($module['table'])->where('id', $item->id)->value('deleted_at'));
            } else {
                $this->assertDatabaseMissing($module['table'], ['id' => $item->id]);
            }
        }
    }
}
