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
use App\Services\Homepage\SectionDataResolver;
use App\Services\Homepage\SectionMediaResolver;
use App\Rules\SafeLink;

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

        $definitions = config('cms-sections.sections', []);
        $sectionConfigs = [];
        $sectionData = [];
        $sectionEnabled = [];

        foreach ($homepage->sections as $section) {
            $sectionEnabled[$section->section_key] = (bool) $section->is_enabled;
            if (! $section->is_enabled) {
                continue;
            }

            $definition = $definitions[$section->section_key] ?? null;
            if (! $definition) {
                continue;
            }

            $config = array_merge($definition['defaults'] ?? [], $section->data_source ?? [], $section->config ?? []);
            foreach ($config as $key => $value) {
                if (str_ends_with($key, '_url')) {
                    $config[$key] = SafeLink::sanitize($value);
                }
            }

            $config = app(SectionMediaResolver::class)->forRendering($section, $config);
            $sectionConfigs[$section->section_key] = $config;
            $sectionData[$section->section_key] = app(SectionDataResolver::class)->resolve($section, $config);
        }

        $propertySection = $sectionData['properties.index']['properties'] ?? null;
        $featuredPropertySection = $sectionData['featured-gallery.home-9']['featuredProperties'] ?? null;
        $serviceSection = $sectionData['services.home-9']['services'] ?? null;
        $teamSection = $sectionData['team.home-2']['teamMembers'] ?? null;
        $testimonialSection = $sectionData['testimonials.home-3']['testimonials'] ?? null;
        $blogSection = $sectionData['blog.home-2']['posts'] ?? null;

        $featuredProperties = collect($propertySection ?? $featuredPropertySection ?? Property::published()->orderByDesc('is_featured')->latest('published_at')->limit(8)->get());
        $featuredProperties->loadMissing('images');

        return view('frontend.homepage', [
            'homepage' => $homepage,
            'homepageSectionConfigs' => $sectionConfigs,
            'homepageSectionData' => $sectionData,
            'homepageSectionEnabled' => $sectionEnabled,
            'featuredProperties' => $featuredProperties,
            'featuredServices' => $serviceSection ?? Service::published()->latest('published_at')->limit(8)->get(),
            'galleryItems' => GalleryItem::published()->orderBy('sort_order')->latest()->limit(8)->get(),
            'members' => $teamSection ?? TeamMember::query()->where('status', 'published')->where('is_active', true)->orderBy('sort_order')->limit(6)->get(),
            'testimonials' => $testimonialSection ?? Testimonial::query()->where('status', 'published')->where('is_active', true)->orderBy('sort_order')->limit(6)->get(),
            'posts' => $blogSection ?? Post::published()->with('author')->latest('published_at')->limit(6)->get(),
            'averageRating' => $sectionData['testimonials.home-3']['averageRating'] ?? null,
            'reviewCount' => $sectionData['testimonials.home-3']['reviewCount'] ?? null,
        ]);
    }
}
