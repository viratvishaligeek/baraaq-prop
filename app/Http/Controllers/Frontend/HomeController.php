<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Homepage;
use App\Models\Post;
use App\Models\Property;
use App\Models\TeamMember;

class HomeController extends Controller
{
    public function __invoke()
    {
        $homepage = cache()->remember(
            'homepage:published-default',
            300,
            fn () => Homepage::published()->default()->with('sections')->first()
        );

        $hasCustomSections = $homepage
            && $homepage->sections->contains(fn ($section) => $section->is_enabled);

        $view = $hasCustomSections
            ? 'frontend.home-builder'
            : match ($homepage?->design_key) {
                'home-2', 'home-3', 'home-4', 'home-5', 'home-6',
                'home-7', 'home-8', 'home-9', 'home-10' =>
                    'frontend.theme-pages.'.$homepage->design_key,
                default => 'frontend.home',
            };

        return view($view, [
            'homepage' => $homepage,
            'properties' => Property::published()
                ->with('category:id,name')
                ->latest('published_at')
                ->limit(6)
                ->get(),
            'featuredProperties' => Property::published()
                ->where('is_featured', true)
                ->with('category:id,name')
                ->latest('published_at')
                ->limit(4)
                ->get(),
            'banners' => Banner::visible()
                ->where('placement', 'home')
                ->orderBy('sort_order')
                ->get(),
            'posts' => Post::published()
                ->with('author:id,name')
                ->latest('published_at')
                ->limit(3)
                ->get(),
            'teamMembers' => TeamMember::where('is_active', true)
                ->orderBy('sort_order')
                ->limit(4)
                ->get(),
        ]);
    }
}
