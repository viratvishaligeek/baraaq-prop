<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Post;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_preview_draft_posts_pages_and_services_without_publishing_them(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        $post = Post::create(['title' => 'Draft preview post', 'slug' => 'draft-preview-post', 'content' => '<p>Post preview body.</p>', 'status' => 'draft']);
        $page = Page::create(['title' => 'Draft preview page', 'slug' => 'draft-preview-page', 'content' => '<p>Page preview body.</p>', 'status' => 'draft']);
        $service = Service::create(['title' => 'Draft preview service', 'slug' => 'draft-preview-service', 'content' => '<p>Service preview body.</p>', 'status' => 'draft']);

        $this->actingAs($user)->get(route('admin.content.preview', ['type' => 'posts', 'id' => $post->id]))->assertOk()->assertSee('Draft preview post');
        $this->actingAs($user)->get(route('admin.content.preview', ['type' => 'pages', 'id' => $page->id]))->assertOk()->assertSee('Draft preview page')->assertSee('Preview — not publicly published');
        $this->actingAs($user)->get(route('admin.content.preview', ['type' => 'services', 'id' => $service->id]))->assertOk()->assertSee('Draft preview service')->assertSee('Preview — not publicly published');

        $this->assertSame('draft', $post->fresh()->status);
        $this->assertSame('draft', $page->fresh()->status);
        $this->assertSame('draft', $service->fresh()->status);
    }
}
