<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Homepage;
use App\Models\Property;
use App\Models\Service;
use App\Models\GalleryItem;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\Post;

class HomeController extends Controller
{
    public function __invoke()
    {
        $homepage = Homepage::published()
            ->where('slug', 'home-9')
            ->with([
                'sections.mediaAttachments.media',
            ])
            ->first();

        abort_unless($homepage, 503, 'The fixed homepage is not published yet.');

        return view('frontend.homepage', [
            'homepage' => $homepage,
            'featuredProperties' => Property::published()->with('images')->orderByDesc('is_featured')->latest('published_at')->limit(8)->get(),
            'featuredServices' => Service::published()->latest('published_at')->limit(8)->get(),
            'galleryItems' => GalleryItem::published()->orderBy('sort_order')->latest()->limit(8)->get(),
            'members' => TeamMember::query()->where('status', 'published')->where('is_active', true)->orderBy('sort_order')->limit(6)->get(),
            'testimonials' => Testimonial::query()->where('status', 'published')->where('is_active', true)->orderBy('sort_order')->limit(6)->get(),
            'posts' => Post::published()->with('author')->latest('published_at')->limit(6)->get(),
        ]);
    }
}
