<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Homepage;
use App\Models\Post;
use App\Models\Property;
use App\Models\TeamMember;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function __invoke()
    {
        // Cache only the scalar ID; never serialize an Eloquent model or its relationships.
        $homepageId = Cache::remember(
            'homepage:published-default:id',
            300,
            fn () => Homepage::published()->default()->value('id')
        );

        // Clear the legacy key written by older releases that cached the model itself.
        Cache::forget('homepage:published-default');

        $homepage = $homepageId
            ? Homepage::query()
                ->with(['sections.reusableSection.mediaAttachments.media', 'sections.mediaAttachments.media'])
                ->find($homepageId)
            : null;

        $hasCustomSections = $homepage
            && $homepage->is_composition_complete
            && $homepage->sections->contains(fn($section) => $section->is_enabled);

        $view = $hasCustomSections
            ? 'frontend.home-builder'
            : match ($homepage?->design_key) {
                'home-2', 'home-3', 'home-4', 'home-5', 'home-6',
                'home-7', 'home-8', 'home-9', 'home-10' =>
                'frontend.theme-pages.' . $homepage->design_key,
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
