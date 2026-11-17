<?php

namespace App\Services\Homepage;

use App\Models\Category;
use App\Models\HomepageSection;
use App\Models\TeamMember;
use App\Models\Property;
use App\Models\Post;
use Illuminate\Support\Collection;

class SectionDataResolver
{
    /**
     * Resolve only the data required by a registered section.
     */
    public function resolve(HomepageSection $section, array $config = []): array
    {
        if ($section->section_key === 'hero.home-3') {
            $mapProperties = Property::published()
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->with(['images', 'category'])
                ->orderByDesc('is_featured')
                ->latest('published_at')
                ->limit(max(1, min(4, (int) ($config['map_card_limit'] ?? 4))))
                ->get();

            $latitudes = $mapProperties->map(fn (Property $property) => (float) $property->latitude);
            $longitudes = $mapProperties->map(fn (Property $property) => (float) $property->longitude);

            return [
                'mapProperties' => $mapProperties,
                'mapBounds' => [
                    'min_lat' => $latitudes->min() ?? 0,
                    'max_lat' => $latitudes->max() ?? 0,
                    'min_lng' => $longitudes->min() ?? 0,
                    'max_lng' => $longitudes->max() ?? 0,
                ],
                'cities' => Property::published()
                    ->whereNotNull('city')
                    ->where('city', '!=', '')
                    ->distinct()
                    ->orderBy('city')
                    ->pluck('city'),
                'propertyTypes' => Property::published()
                    ->whereNotNull('property_type')
                    ->where('property_type', '!=', '')
                    ->distinct()
                    ->orderBy('property_type')
                    ->pluck('property_type'),
                'listingTypes' => Property::published()
                    ->whereNotNull('listing_type')
                    ->where('listing_type', '!=', '')
                    ->distinct()
                    ->orderBy('listing_type')
                    ->pluck('listing_type'),
                'bedroomOptions' => Property::published()
                    ->whereNotNull('bedrooms')
                    ->distinct()
                    ->orderBy('bedrooms')
                    ->pluck('bedrooms'),
                'propertyCategories' => Category::query()
                    ->where('type', 'property')
                    ->where('is_active', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get(['id', 'name']),
            ];
        }

        return match ($section->section_type) {
            'category-slider' => ['categories' => $this->categories($config)],
            'team-slider' => ['teamMembers' => $this->teamMembers($config)],
            'property-slider-grid' => $this->properties($config),
            'featured-gallery-slider' => ['featuredProperties' => Property::published()->where('is_featured', true)->with(['images', 'creator:id,name'])->latest('published_at')->limit(max(1, min(8, (int) ($config['items_limit'] ?? 3))))->get()],
            'blog-slider' => ['posts' => Post::published()->with('author')->latest('published_at')->limit(max(1, min(12, (int) ($config['items_limit'] ?? 6))))->get()],
            'advanced-search' => [
                'cities' => Property::published()->whereNotNull('city')->where('city', '!=', '')->distinct()->orderBy('city')->pluck('city'),
                'propertyTypes' => Property::published()->whereNotNull('property_type')->where('property_type', '!=', '')->distinct()->orderBy('property_type')->pluck('property_type'),
            ],
            default => [],
        };
    }

    private function properties(array $config): array
    {
        $properties = Property::published()
            ->with(['images', 'category', 'creator:id,name'])
            ->latest('published_at')
            ->limit(max(1, min(16, (int) ($config['items_limit'] ?? 8))))
            ->get();

        $categories = Category::query()
            ->where('type', 'property')
            ->where('is_active', true)
            ->withCount(['properties' => fn ($query) => $query->published()])
            ->having('properties_count', '>', 0)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return ['properties' => $properties, 'propertyCategories' => $categories];
    }

    private function teamMembers(array $config): Collection
    {
        return TeamMember::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(max(1, min(12, (int) ($config['items_limit'] ?? 6))))
            ->get();
    }

    private function categories(array $config): Collection
    {
        return Category::query()
            ->where('type', 'property')
            ->where('is_active', true)
            ->withCount(['properties' => fn ($query) => $query->published()])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(max(1, min(20, (int) ($config['items_limit'] ?? 7))))
            ->get();
    }
}
