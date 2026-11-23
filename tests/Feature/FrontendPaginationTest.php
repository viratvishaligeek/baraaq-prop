<?php

namespace Tests\Feature;

use App\Models\GalleryItem;
use App\Models\NeighborhoodGuide;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
            ->assertSee('Bareilly locality guide 9');
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
            ->assertSee('Bareilly property article 10');
    }
    public function test_gallery_category_filter_is_applied_server_side_and_preserved_by_pagination(): void
    {
        foreach (range(1, 19) as $number) {
            GalleryItem::query()->create([
                'title' => 'Apartment gallery image '.$number,
                'slug' => 'apartment-gallery-image-'.$number,
                'description' => 'Approved apartment gallery image.',
                'category' => 'apartment',
                'status' => 'published',
                'published_at' => now()->subMinutes($number),
                'sort_order' => $number,
            ]);
        }

        GalleryItem::query()->create([
            'title' => 'Commercial gallery image',
            'slug' => 'commercial-gallery-image',
            'category' => 'commercial',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->get(route('gallery.index', ['category' => 'apartment']))
            ->assertOk()
            ->assertSee('cat1', false)
            ->assertSee(e(route('gallery.index', ['category' => 'apartment', 'page' => 2])), false)
            ->assertDontSee('Commercial gallery image');

        $this->get(route('gallery.index', ['category' => 'apartment', 'page' => 2]))
            ->assertOk()
            ->assertSee('Apartment gallery image 19')
            ->assertDontSee('Commercial gallery image');
    }

}
