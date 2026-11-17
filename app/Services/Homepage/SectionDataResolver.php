<?php

namespace App\Services\Homepage;

use App\Models\Category;
use App\Models\HomepageSection;
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
            default => [],
        };
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
