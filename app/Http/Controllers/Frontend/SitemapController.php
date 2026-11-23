<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Post;
use App\Models\Property;
use App\Models\Service;
use App\Models\GalleryItem;
use App\Models\NeighborhoodGuide;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $urls = collect([
            ['loc' => route('home'), 'lastmod' => now()],
            ['loc' => route('properties.index'), 'lastmod' => now()],
            ['loc' => route('blog.index'), 'lastmod' => now()],
            ['loc' => route('services.index'), 'lastmod' => now()],
            ['loc' => route('team.index'), 'lastmod' => now()],
            ['loc' => route('testimonials.index'), 'lastmod' => now()],
            ['loc' => route('gallery.index'), 'lastmod' => now()],
            ['loc' => route('faq.index'), 'lastmod' => now()],
            ['loc' => route('neighborhood-guides.index'), 'lastmod' => now()],
        ]);

        foreach (Property::published()->get(['slug', 'updated_at']) as $property) {
            $urls->push(['loc' => route('properties.show', $property->slug), 'lastmod' => $property->updated_at]);
        }

        foreach (Post::published()->get(['slug', 'updated_at']) as $post) {
            $urls->push(['loc' => route('blog.show', $post->slug), 'lastmod' => $post->updated_at]);
        }

        foreach (Page::where('status', 'published')->get(['slug', 'updated_at']) as $page) {
            $urls->push(['loc' => route('pages.show', $page->slug), 'lastmod' => $page->updated_at]);
        }

        foreach (NeighborhoodGuide::published()->get(['slug', 'updated_at']) as $guide) {
            $urls->push(['loc' => route('neighborhood-guides.show', $guide->slug), 'lastmod' => $guide->updated_at]);
        }

        foreach (Service::published()->get(['slug', 'updated_at']) as $service) {
            $urls->push(['loc' => route('services.show', $service->slug), 'lastmod' => $service->updated_at]);
        }

        return response()->view('frontend.sitemap', compact('urls'), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
