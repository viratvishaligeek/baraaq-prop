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

        foreach (Property::published()->get(['slug', 'updated_at', 'canonical_url']) as $property) {
            $urls->push(['loc' => ($property->canonical_url ?: route('properties.show', $property->slug)), 'lastmod' => $property->updated_at]);
        }

        foreach (Post::published()->get(['slug', 'updated_at', 'canonical_url']) as $post) {
            $urls->push(['loc' => ($post->canonical_url ?: route('blog.show', $post->slug)), 'lastmod' => $post->updated_at]);
        }

        foreach (Page::where('status', 'published')->whereNotIn('slug', ['about', 'contact'])->get(['slug', 'updated_at', 'canonical_url']) as $page) {
            $urls->push(['loc' => $page->canonical_url ?: route('pages.show', $page->slug), 'lastmod' => $page->updated_at]);
        }

        foreach (NeighborhoodGuide::published()->get(['slug', 'updated_at']) as $guide) {
            $urls->push(['loc' => route('neighborhood-guides.show', $guide->slug), 'lastmod' => $guide->updated_at]);
        }

        foreach (Service::published()->get(['slug', 'updated_at', 'canonical_url']) as $service) {
            $urls->push(['loc' => ($service->canonical_url ?: route('services.show', $service->slug)), 'lastmod' => $service->updated_at]);
        }

        return response()->view('frontend.sitemap', compact('urls'), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
