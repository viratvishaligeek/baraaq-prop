<?php

namespace Tests\\Feature;

use App\\Models\\NeighborhoodGuide;
use App\\Models\\Post;
use App\\Models\\Service;
use Illuminate\\Foundation\\Testing\\RefreshDatabase;
use Tests\\TestCase;

class FrontendPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_pagination_uses_services_route_and_keeps_search_query(): void
    {
        foreach (range(1, 13) as $number) {
            Service::query()->create([
                'title' => 'Bareilly property advisory '.$number,
                'slug' => 'bareilly-property-advisory-'.$number,
                'excerpt' => 'Bareilly property guidance.',
                'status' => 'published',
                'published_at' => now()->subMinutes($number),
            ]);
        }

        $this->get(route('services.index', ['q' => 'Bareilly']))
            ->assertOk()
            ->assertSee('th-pagination', false)
            ->assertSee(e(route('services.index', ['q' => 'Bareilly', 'page' => 2])), false)
            ->assertDontSee(e(route('blog.index', ['q' => 'Bareilly', 'page' => 2])), false);

        $this->get(route('services.index', ['q' => 'Bareilly', 'page' => 2]))
            ->assertOk()
            ->assertSee('Bareilly property advisory 13');
    }

    public function test_neighborhood_pagination_uses_neighborhood_route_and_keeps_search_query(): void
    {
        foreach (range(1, 10) as $number) {
            NeighborhoodGuide::query()->create([
                'title' => 'Bareilly locality guide '.$number,
                'slug' => 'bareilly-locality-guide-'.$number,
                'locality' => 'Bareilly locality '.$number,
                'city' => 'Bareilly',
                'state' => 'Uttar Pradesh',
                'status' => 'published',
                'published_at' => now()->subMinutes($number),
            ]);
        }

        $this->get(route('neighborhood-guides.index', ['q' => 'Bareilly']))
            ->assertOk()
            ->assertSee('th-pagination', false)
            ->assertSee(e(route('neighborhood-guides.index', ['q' => 'Bareilly', 'page' => 2])), false)
            ->assertDontSee(e(route('properties.index', ['q' => 'Bareilly', 'page' => 2])), false);

        $this->get(route('neighborhood-guides.index', ['q' => 'Bareilly', 'page' => 2]))
            ->assertOk()
            ->assertSee('Bareilly locality guide 1');
    }

    public function test_blog_pagination_uses_blog_route_and_keeps_search_query(): void
    {
        foreach (range(1, 10) as $number) {
            Post::query()->create([
                'title' => 'Bareilly property article '.$number,
                'slug' => 'bareilly-property-article-'.$number,
                'excerpt' => 'Bareilly property information.',
                'status' => 'published',
                'published_at' => now()->subMinutes($number),
            ]);
        }

        $this->get(route('blog.index', ['q' => 'Bareilly']))
            ->assertOk()
            ->assertSee('th-pagination', false)
            ->assertSee(e(route('blog.index', ['q' => 'Bareilly', 'page' => 2])), false);

        $this->get(route('blog.index', ['q' => 'Bareilly', 'page' => 2]))
            ->assertOk()
            ->assertSee('Bareilly property article 1');
    }
}
