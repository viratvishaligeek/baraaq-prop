<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\HomepageController;
use App\Http\Controllers\Admin\PageHomepageController;
use App\Http\Controllers\Frontend\HomeController;
use App\Models\Homepage;
use App\Models\Page;
use App\Models\Role;
use App\Models\User;
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
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::create(['name' => 'Administrator', 'slug' => 'administrator']));
        $this->actingAs($admin);

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

        app(HomepageController::class)->setDefault($homeFive);

        $this->assertFalse($homeFour->fresh()->is_default);
        $this->assertTrue($homeFive->fresh()->is_default);
        $this->assertNull(Cache::get('homepage:published-default:id'));
        $this->get('/')->assertOk()->assertSee('Discover Luxury Home Every Rooms');
    }

    public function test_selecting_a_page_homepage_clears_the_cached_default_homepage(): void
    {
        $admin = User::factory()->create();
        $admin->roles()->attach(Role::create(['name' => 'Administrator', 'slug' => 'administrator']));
        $this->actingAs($admin);

        $homepage = Homepage::create([
            'name' => 'Old default',
            'slug' => 'old-default-homepage-cache-test',
            'design_key' => 'home-4',
            'status' => 'published',
            'is_default' => true,
            'is_composition_complete' => true,
            'published_at' => now(),
        ]);
        $page = Page::create([
            'title' => 'New page homepage',
            'slug' => 'new-page-homepage-cache-test',
            'content' => 'New page homepage content.',
            'status' => 'published',
        ]);

        Cache::put('homepage:published-default:id', $homepage->id, 300);
        Cache::put('homepage:published-default', $homepage, 300);

        app(PageHomepageController::class)->store($page);

        $this->assertNull(Cache::get('homepage:published-default:id'));
        $this->assertNull(Cache::get('homepage:published-default'));
        $this->assertSame('primary', $page->fresh()->homepage_key);
        $this->assertFalse($homepage->fresh()->is_default);
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
