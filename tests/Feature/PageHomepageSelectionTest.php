<?php

namespace Tests\Feature;

use App\Http\Controllers\Frontend\HomeController;
use App\Models\Homepage;
use App\Models\Page;
use Illuminate\Support\Facades\Cache;
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

    public function test_root_url_renders_the_selected_page_through_the_real_route(): void
    {
        Page::create([
            'title' => 'Root route landing page',
            'slug' => 'root-route-landing-page',
            'content' => 'Root route content that must be visible.',
            'status' => 'published',
            'homepage_key' => 'primary',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertViewIs('frontend.pages.show')
            ->assertViewHas('isHomepage', true)
            ->assertSee('Root route content that must be visible.');
    }

    public function test_switching_the_published_default_homepage_changes_the_root_route_output(): void
    {
        $homeFour = Homepage::create([
            'name' => 'Home 4',
            'slug' => 'root-test-home-four',
            'design_key' => 'home-4',
            'status' => 'published',
            'is_default' => true,
            'is_composition_complete' => true,
            'published_at' => now(),
        ]);
        $homeFive = Homepage::create([
            'name' => 'Home 5',
            'slug' => 'root-test-home-five',
            'design_key' => 'home-5',
            'status' => 'published',
            'is_default' => false,
            'is_composition_complete' => true,
            'published_at' => now(),
        ]);

        Cache::forget('homepage:published-default:id');
        $this->get('/')->assertOk()->assertSee('Digining Your Luxury Dream Space With Style');

        $homeFour->update(['is_default' => false]);
        $homeFive->update(['is_default' => true]);
        Cache::forget('homepage:published-default:id');

        $this->get('/')->assertOk()->assertSee('Discover Luxury Home Every Rooms');
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
