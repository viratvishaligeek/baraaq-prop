<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTagManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_edit_and_delete_tags(): void
    {
        $user = $this->administrator();

        $this->actingAs($user)
            ->post(route('admin.tags.store'), ['name' => 'Market insights'])
            ->assertRedirect(route('admin.tags.index'));

        $tag = Tag::where('slug', 'market-insights')->firstOrFail();
        $this->actingAs($user)
            ->put(route('admin.tags.update', $tag), ['name' => 'Property insights', 'slug' => 'property-insights'])
            ->assertRedirect(route('admin.tags.index'));

        $this->assertSame('Property insights', $tag->fresh()->name);
        $this->actingAs($user)->delete(route('admin.tags.destroy', $tag))->assertRedirect();
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    public function test_blog_post_can_assign_and_persist_multiple_tags(): void
    {
        $user = $this->administrator();
        $first = Tag::create(['name' => 'Buying', 'slug' => 'buying']);
        $second = Tag::create(['name' => 'Investment', 'slug' => 'investment']);

        $this->actingAs($user)
            ->post(route('admin.content.store', 'posts'), [
                'title' => 'Buying a property',
                'slug' => 'buying-a-property',
                'excerpt' => 'A practical guide.',
                'content' => '<p>Guide content.</p>',
                'status' => 'draft',
                'tag_ids' => [$first->id, $second->id],
            ])
            ->assertRedirect(route('admin.content.index', 'posts'));

        $post = Post::where('slug', 'buying-a-property')->firstOrFail();
        $this->assertEqualsCanonicalizing([$first->id, $second->id], $post->tags()->pluck('tags.id')->all());

        $this->actingAs($user)
            ->get(route('admin.content.edit', ['type' => 'posts', 'id' => $post->id]))
            ->assertOk()
            ->assertSee('Buying')
            ->assertSee('Investment');
    }

    private function administrator(): User
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        return $user;
    }
}
