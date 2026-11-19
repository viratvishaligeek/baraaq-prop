<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Post;
use App\Models\Property;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoIndexingTest extends TestCase
{
    use RefreshDatabase;


    public function test_admin_can_open_sitemap_and_indexing_settings_overview(): void
    {
        $this->seed(RolePermissionSeeder::class);
        $user = User::factory()->create(['is_active' => true]);
        $user->roles()->attach(Role::query()->where('slug', 'administrator')->firstOrFail());

        $this->actingAs($user)
            ->get(route('admin.seo.indexing'))
            ->assertOk()
            ->assertSee('Sitemap and indexing')
            ->assertSee(route('sitemap'), false)
            ->assertSee('robots.txt');
    }

    public function test_robots_txt_uses_configured_safe_directives_and_appends_sitemap(): void
    {
        Setting::write('robots_rules', "User-agent: *\nAllow: /\nDisallow: /private\nCrawl-delay: 5\nHost: attacker.example", 'seo');

        $this->get(route('robots'))
            ->assertOk()
            ->assertSee('User-agent: *')
            ->assertSee('Disallow: /private')
            ->assertSee('Crawl-delay: 5')
            ->assertDontSee('Host: attacker.example')
            ->assertSee('Sitemap: ' . route('sitemap'));
    }

    public function test_sitemap_contains_only_public_content_routes(): void
    {
        $property = Property::create(['title' => 'Sitemap property', 'slug' => 'sitemap-property', 'status' => 'published', 'published_at' => now()]);
        $post = Post::create(['title' => 'Sitemap post', 'slug' => 'sitemap-post', 'status' => 'published', 'published_at' => now()]);
        $service = Service::create(['title' => 'Sitemap service', 'slug' => 'sitemap-service', 'status' => 'published', 'published_at' => now()]);
        $page = Page::create(['title' => 'Sitemap page', 'slug' => 'sitemap-page', 'status' => 'published']);

        $this->get(route('sitemap'))
            ->assertOk()
            ->assertSee(route('properties.show', $property->slug), false)
            ->assertSee(route('blog.show', $post->slug), false)
            ->assertSee(route('services.show', $service->slug), false)
            ->assertSee(route('pages.show', $page->slug), false);
    }

    public function test_public_property_canonical_url_is_generated_from_its_route(): void
    {
        $property = Property::create([
            'title' => 'Canonical property',
            'slug' => 'canonical-property',
            'status' => 'published',
            'published_at' => now(),
            'canonical_url' => 'https://legacy.example/wrong-canonical',
        ]);

        $this->get(route('properties.show', $property))
            ->assertOk()
            ->assertSee('<link rel="canonical" href="' . route('properties.show', $property) . '"', false)
            ->assertDontSee('https://legacy.example/wrong-canonical');
    }
}
