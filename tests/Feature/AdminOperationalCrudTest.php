<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Enquiry;
use App\Models\Homepage;
use App\Models\Media;
use App\Models\Menu;
use App\Models\NewsletterSubscriber;
use App\Models\Post;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminOperationalCrudTest extends TestCase
{
    use RefreshDatabase;

    private function administrator(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        return $user;
    }

    public function test_enquiries_comments_and_newsletter_subscribers_support_their_admin_actions(): void
    {
        $admin = $this->administrator();

        $enquiry = Enquiry::create([
            'name' => 'Operational test enquiry',
            'email' => 'visitor@example.test',
            'phone' => null,
            'message' => 'Please contact me.',
            'source' => 'contact',
            'status' => 'new',
        ]);

        $this->actingAs($admin)->patch(route('admin.enquiries.update', $enquiry), [
            'status' => 'contacted',
            'admin_notes' => 'Called and left a message.',
        ])->assertRedirect();

        $this->assertDatabaseHas('enquiries', [
            'id' => $enquiry->id,
            'status' => 'contacted',
            'admin_notes' => 'Called and left a message.',
        ]);
        $this->assertNotNull($enquiry->fresh()->contacted_at);

        $this->actingAs($admin)->delete(route('admin.enquiries.destroy', $enquiry))->assertRedirect();
        $this->assertDatabaseMissing('enquiries', ['id' => $enquiry->id]);

        $post = Post::create([
            'title' => 'Comment moderation article',
            'slug' => 'comment-moderation-article',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $comment = Comment::create([
            'post_id' => $post->id,
            'name' => 'Comment author',
            'email' => 'commenter@example.test',
            'body' => 'A useful comment awaiting moderation.',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->patch(route('admin.comments.update', $comment), [
            'status' => 'approved',
        ])->assertRedirect();

        $this->assertDatabaseHas('comments', ['id' => $comment->id, 'status' => 'approved']);

        $this->actingAs($admin)->patch(route('admin.comments.update', $comment), [
            'name' => 'Updated comment author',
            'body' => 'An edited and approved comment.',
        ])->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'name' => 'Updated comment author',
            'body' => 'An edited and approved comment.',
        ]);

        $this->actingAs($admin)->delete(route('admin.comments.destroy', $comment))->assertRedirect();
        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);

        $subscriber = NewsletterSubscriber::create([
            'email' => 'subscriber@example.test',
            'subscribed_at' => now(),
        ]);

        $this->actingAs($admin)->get(route('admin.newsletters.index'))
            ->assertOk()
            ->assertSee('subscriber@example.test');

        $this->actingAs($admin)->delete(route('admin.newsletters.destroy', $subscriber))->assertRedirect();
        $this->assertDatabaseMissing('newsletter_subscribers', ['id' => $subscriber->id]);
    }

    public function test_menus_support_create_update_nested_items_and_delete(): void
    {
        $admin = $this->administrator();

        $this->actingAs($admin)->post(route('admin.menus.store'), [
            'name' => 'Operational menu',
            'location' => 'operational-menu',
            'items' => [[
                'label' => 'Properties',
                'link_type' => 'custom',
                'url' => '/properties',
                'target' => '_self',
                'is_active' => '1',
            ]],
        ])->assertRedirect();

        $menu = Menu::query()->where('location', 'operational-menu')->firstOrFail();
        $this->assertDatabaseHas('menu_items', ['menu_id' => $menu->id, 'label' => 'Properties']);

        $this->actingAs($admin)->put(route('admin.menus.update', $menu), [
            'name' => 'Updated operational menu',
            'location' => 'updated-operational-menu',
            'items' => [
                [
                    'label' => 'Properties',
                    'link_type' => 'custom',
                    'url' => '/properties',
                    'target' => '_self',
                    'is_active' => '1',
                ],
                [
                    'label' => 'Featured properties',
                    'link_type' => 'custom',
                    'url' => '/properties?featured=1',
                    'target' => '_self',
                    'parent_index' => '0',
                    'is_active' => '1',
                ],
            ],
        ])->assertRedirect();

        $menu->refresh();
        $this->assertSame('Updated operational menu', $menu->name);
        $this->assertSame('updated-operational-menu', $menu->location);
        $this->assertCount(2, $menu->allItems()->get());
        $this->assertNotNull($menu->allItems()->where('label', 'Featured properties')->first()->parent_id);

        $this->actingAs($admin)->delete(route('admin.menus.destroy', $menu))->assertRedirect();
        $this->assertDatabaseMissing('menus', ['id' => $menu->id]);
        $this->assertDatabaseMissing('menu_items', ['menu_id' => $menu->id]);
    }

    public function test_media_metadata_and_bulk_deletion_respect_homepage_references(): void
    {
        Storage::fake('public');
        $admin = $this->administrator();

        $unused = Media::create([
            'uploaded_by' => $admin->id,
            'disk' => 'public',
            'path' => 'media/unused-admin-image.png',
            'original_name' => 'unused-admin-image.png',
            'mime_type' => 'image/png',
            'size' => 12,
        ]);
        $referenced = Media::create([
            'uploaded_by' => $admin->id,
            'disk' => 'public',
            'path' => 'media/referenced-admin-image.png',
            'original_name' => 'referenced-admin-image.png',
            'mime_type' => 'image/png',
            'size' => 12,
        ]);
        Storage::disk('public')->put($unused->path, 'unused-image');
        Storage::disk('public')->put($referenced->path, 'referenced-image');

        $homepage = Homepage::create([
            'name' => 'Home 9',
            'slug' => 'home-9',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $section = $homepage->sections()->create([
            'section_key' => 'hero.home-9',
            'section_type' => 'hero',
            'sort_order' => 1,
            'is_enabled' => true,
            'config' => [],
            'data_source' => [],
        ]);
        $section->mediaAttachments()->create([
            'field_key' => 'foreground_image',
            'media_id' => $referenced->id,
        ]);

        $this->actingAs($admin)->patch(route('admin.media.update', $unused), [
            'alt_text' => 'Updated image description',
        ])->assertRedirect();

        $this->assertSame('Updated image description', $unused->fresh()->alt_text);

        $this->actingAs($admin)->from(route('admin.media.index'))
            ->delete(route('admin.media.bulk-destroy'), [
                'media_ids' => [$unused->id, $referenced->id],
            ])->assertRedirect()->assertSessionHasErrors('media');

        $this->assertDatabaseMissing('media', ['id' => $unused->id]);
        $this->assertDatabaseHas('media', ['id' => $referenced->id]);
        Storage::disk('public')->assertMissing($unused->path);
        Storage::disk('public')->assertExists($referenced->path);
    }
}
