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
        return match ($section->section_type) {
            'category-slider' => ['categories' => $this->categories($config)],
            'team-slider' => ['teamMembers' => $this->teamMembers($config)],
            'property-slider-grid' => $this->properties($config),
            'blog-slider' => ['posts' => Post::published()->with('author')->latest('published_at')->limit(max(1, min(12, (int) ($config['items_limit'] ?? 6))))->get()],
            'advanced-search' => ['cities' => Property::published()->whereNotNull('city')->where('city', '!=', '')->distinct()->orderBy('city')->pluck('city')],
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
