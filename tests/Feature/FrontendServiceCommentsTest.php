<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\ServiceComment;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendServiceCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_comment_is_saved_pending_moderation_and_not_public_until_approved(): void
    {
        $service = Service::query()->create([
            'title' => 'Property consultation',
            'slug' => 'property-consultation',
            'excerpt' => 'Consultation service.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->post(route('services.comments.store', $service), [
            'name' => 'A Visitor',
            'email' => 'visitor@example.test',
            'body' => 'Please share more details about this service.',
        ])->assertRedirect();

        $comment = ServiceComment::query()->firstOrFail();
        $this->assertSame('pending', $comment->status);

        $this->get(route('services.show', $service))
            ->assertOk()
            ->assertDontSee('Please share more details about this service.');

        $comment->update(['status' => 'approved']);

        $this->get(route('services.show', $service))
            ->assertOk()
            ->assertSee('Please share more details about this service.');
    }

    public function test_admin_can_moderate_service_comments_from_existing_comment_admin(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create(['is_active' => true]);
        $admin->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());
        $service = Service::query()->create([
            'title' => 'Property consultation',
            'slug' => 'property-consultation',
            'status' => 'published',
            'published_at' => now(),
        ]);
        $comment = ServiceComment::query()->create([
            'service_id' => $service->id,
            'name' => 'A Visitor',
            'email' => 'visitor@example.test',
            'body' => 'A moderated service question.',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.service-comments.update', $comment), ['status' => 'approved'])
            ->assertRedirect();

        $this->assertDatabaseHas('service_comments', ['id' => $comment->id, 'status' => 'approved']);
        $this->actingAs($admin)->get(route('admin.comments.index'))
            ->assertOk()
            ->assertSee('Service comments')
            ->assertSee('A moderated service question.');
    }
}
