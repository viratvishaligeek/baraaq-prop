<?php

namespace App\Services\Homepage;

use App\Models\Category;
use App\Models\HomepageSection;
use App\Models\Post;
use App\Models\Service;
use App\Models\Property;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Support\Collection;

class SectionDataResolver
{
    public function resolve(HomepageSection $section, array $config = []): array
    {
        $source = $section->data_source ?? [];
        $config = array_merge($config, $source, [
            'items_limit' => $source['limit'] ?? ($config['items_limit'] ?? 6),
            'columns' => $source['columns'] ?? ($config['columns'] ?? 3),
            'slider' => $source['slider'] ?? ($config['slider'] ?? true),
        ]);
        $ids = array_values(array_filter(array_map('intval', $source['selected_ids'] ?? [])));
        $direction = ($source['sort_direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        if ($section->section_key === 'hero.home-3') {
            $mapProperties = Property::published()->whereNotNull('latitude')->whereNotNull('longitude')
                ->with(['images', 'category'])->orderByDesc('is_featured')->latest('published_at')
                ->limit(max(1, min(4, (int) ($config['map_card_limit'] ?? 4))))->get();
            $latitudes = $mapProperties->map(fn (Property $property) => (float) $property->latitude);
            $longitudes = $mapProperties->map(fn (Property $property) => (float) $property->longitude);

            return [
                'mapProperties' => $mapProperties,
                'mapBounds' => ['min_lat' => $latitudes->min() ?? 0, 'max_lat' => $latitudes->max() ?? 0, 'min_lng' => $longitudes->min() ?? 0, 'max_lng' => $longitudes->max() ?? 0],
                'cities' => Property::published()->whereNotNull('city')->where('city', '!=', '')->distinct()->orderBy('city')->pluck('city'),
                'propertyTypes' => Property::published()->whereNotNull('property_type')->where('property_type', '!=', '')->distinct()->orderBy('property_type')->pluck('property_type'),
                'listingTypes' => Property::published()->whereNotNull('listing_type')->where('listing_type', '!=', '')->distinct()->orderBy('listing_type')->pluck('listing_type'),
                'bedroomOptions' => Property::published()->whereNotNull('bedrooms')->distinct()->orderBy('bedrooms')->pluck('bedrooms'),
                'propertyCategories' => Category::query()->where('type', 'property')->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(['id', 'name']),
            ];
        }

        return match ($section->section_type) {
            'service-list' => ['services' => $this->services($config, $ids, $direction)],
            'category-slider' => ['categories' => $this->categories($config, $ids, $direction)],
            'team-slider' => ['teamMembers' => $this->teamMembers($config, $ids, $direction)],
            'testimonial-slider' => $this->testimonials($config, $ids, $direction),
            'property-slider-grid', 'property-list' => $this->properties($config, $ids, $direction),
            'featured-gallery-slider' => ['featuredProperties' => $this->featuredProperties($config, $ids, $direction)],
            'blog-slider' => ['posts' => $this->posts($config, $ids, $direction)],
            'advanced-search' => [
                'cities' => Property::published()->whereNotNull('city')->where('city', '!=', '')->distinct()->orderBy('city')->pluck('city'),
                'propertyTypes' => Property::published()->whereNotNull('property_type')->where('property_type', '!=', '')->distinct()->orderBy('property_type')->pluck('property_type'),
            ],
            default => [],
        };
    }


    private function services(array $config, array $ids, string $direction): Collection
    {
        $query = Service::published();
        if ($ids !== []) {
            $query->whereIn('id', $ids);
        }

        $sort = in_array($config['sort_by'] ?? 'published_at', ['published_at', 'created_at', 'title'], true)
            ? ($config['sort_by'] ?? 'published_at')
            : 'published_at';

        return $query->orderBy($sort, $direction)
            ->limit(max(1, min(24, (int) ($config['items_limit'] ?? 8))))
            ->get();
    }

    private function testimonials(array $config, array $ids, string $direction): array
    {
        $query = Testimonial::query()->where('status', 'published')->where('is_active', true);
        if ($ids !== []) $query->whereIn('id', $ids);
        $sort = in_array($config['sort_by'] ?? 'sort_order', ['sort_order', 'created_at', 'name'], true) ? ($config['sort_by'] ?? 'sort_order') : 'sort_order';
        $items = (clone $query)->orderBy($sort, $direction)->limit(max(1, min(24, (int) ($config['items_limit'] ?? 4))))->get();

        return ['testimonials' => $items, 'averageRating' => round((float) ((clone $query)->whereNotNull('rating')->avg('rating') ?? 0), 1), 'reviewCount' => (clone $query)->count()];
    }

    private function properties(array $config, array $ids, string $direction): array
    {
        $query = Property::published()->with(['images', 'category', 'creator:id,name']);
        if ($ids !== []) $query->whereIn('id', $ids);
        $filter = $config['listing_filter'] ?? 'all';
        if (in_array($filter, ['sale', 'rent'], true)) $query->where('listing_type', $filter);
        elseif ($filter === 'featured') $query->where('is_featured', true);
        $sort = in_array($config['sort_by'] ?? 'published_at', ['published_at', 'created_at', 'price'], true) ? ($config['sort_by'] ?? 'published_at') : 'published_at';
        $properties = (clone $query)->orderBy($sort, $direction)->limit(max(1, min(24, (int) ($config['items_limit'] ?? 8))))->get();
        $categories = Category::query()->where('type', 'property')->where('is_active', true)
            ->withCount(['properties' => fn ($q) => $q->published()])->having('properties_count', '>', 0)
            ->orderBy('sort_order')->orderBy('name')->get();

        return ['properties' => $properties, 'propertyCategories' => $categories];
    }

    private function featuredProperties(array $config, array $ids, string $direction): Collection
    {
        $query = Property::published()->where('is_featured', true)->with(['images', 'creator:id,name']);
        if ($ids !== []) $query->whereIn('id', $ids);
        $sort = in_array($config['sort_by'] ?? 'published_at', ['published_at', 'created_at', 'price'], true) ? ($config['sort_by'] ?? 'published_at') : 'published_at';

        return $query->orderBy($sort, $direction)->limit(max(1, min(24, (int) ($config['items_limit'] ?? 3))))->get();
    }

    private function posts(array $config, array $ids, string $direction): Collection
    {
        $query = Post::published()->with('author');
        if ($ids !== []) $query->whereIn('id', $ids);
        $sort = in_array($config['sort_by'] ?? 'published_at', ['published_at', 'created_at', 'title'], true) ? ($config['sort_by'] ?? 'published_at') : 'published_at';

        return $query->orderBy($sort, $direction)->limit(max(1, min(24, (int) ($config['items_limit'] ?? 6))))->get();
    }

    private function teamMembers(array $config, array $ids, string $direction): Collection
    {
        $query = TeamMember::query()->where('status', 'published')->where('is_active', true);
        if ($ids !== []) $query->whereIn('id', $ids);
        $sort = in_array($config['sort_by'] ?? 'sort_order', ['sort_order', 'created_at', 'name'], true) ? ($config['sort_by'] ?? 'sort_order') : 'sort_order';

        return $query->orderBy($sort, $direction)->limit(max(1, min(24, (int) ($config['items_limit'] ?? 6))))->get();
    }

    private function categories(array $config, array $ids, string $direction): Collection
    {
        $query = Category::query()->where('type', 'property')->where('is_active', true)
            ->withCount(['properties' => fn ($q) => $q->published()])
            ->with(['properties' => fn ($q) => $q->published()->with('images')->latest('published_at')->limit(3)]);
        if ($ids !== []) $query->whereIn('id', $ids);
        $sort = in_array($config['sort_by'] ?? 'sort_order', ['sort_order', 'created_at', 'name'], true) ? ($config['sort_by'] ?? 'sort_order') : 'sort_order';

        return $query->orderBy($sort, $direction)->limit(max(1, min(24, (int) ($config['items_limit'] ?? 7))))->get();
    }
}
