<?php

namespace Tests\Feature;

use App\Http\Controllers\Frontend\HomeController;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageHomepageSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_selected_published_page_is_rendered_at_the_root_url(): void
    {
        $page = Page::create([
            'title' => 'Custom landing page',
            'slug' => 'custom-landing-page',
            'content' => 'Welcome to the custom homepage.',
            'status' => 'published',
            'homepage_key' => 'primary',
        ]);

        $response = app(HomeController::class)->__invoke();

        $this->assertSame('frontend.pages.show', $response->name());
        $this->assertTrue($response->getData()['isHomepage']);
        $this->assertSame($page->id, $response->getData()['page']->id);
    }

    public function test_a_page_cannot_remain_the_homepage_when_it_is_unpublished(): void
    {
        $page = Page::create([
            'title' => 'Draft landing page',
            'slug' => 'draft-landing-page',
            'content' => 'Draft content.',
            'status' => 'published',
            'homepage_key' => 'primary',
        ]);

        $page->update(['status' => 'draft']);

        $this->assertNull($page->fresh()->homepage_key);
    }
}
