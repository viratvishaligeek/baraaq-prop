<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentPublishingAndCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_published_services_are_visible_on_the_frontend(): void
    {
        $published = Service::create([
            'title' => 'Property valuation',
            'slug' => 'property-valuation',
            'excerpt' => 'Professional property valuation.',
            'content' => 'A published service description.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $draft = Service::create([
            'title' => 'Draft consultation',
            'slug' => 'draft-consultation',
            'content' => 'This must remain private.',
            'status' => 'draft',
        ]);

        $this->get(route('services.show', $published))->assertOk()->assertSee('Property valuation');
        $this->get(route('services.show', $draft))->assertNotFound();
    }

    public function test_blog_comments_are_saved_as_pending_until_moderated(): void
    {
        $post = Post::create([
            'title' => 'Buying your first home',
            'slug' => 'buying-your-first-home',
            'content' => 'Article content.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->post(route('blog.comments.store', $post), [
            'name' => 'A Reader',
            'email' => 'reader@example.test',
            'body' => 'This article was helpful.',
        ])->assertRedirect();

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'email' => 'reader@example.test',
            'status' => 'pending',
        ]);

        $this->get(route('blog.show', $post))->assertOk()->assertDontSee('This article was helpful.');
    }
}
