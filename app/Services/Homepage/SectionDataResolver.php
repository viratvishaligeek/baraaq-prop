<?php

namespace App\Services\Homepage;

use App\Models\HomepageSection;
use App\Models\Post;
use App\Models\Property;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Support\Collection;

class SectionDataResolver
{
    public function resolve(HomepageSection $section, array $config = []): array
    {
        $source = $section->data_source ?? [];
        // The fixed Homepage CMS exposes these values as editable section fields.
        // Keep legacy data-source values as fallbacks, not overrides of saved admin content.
        $config = array_merge($config, $source, [
            'items_limit' => $config['items_limit'] ?? ($source['limit'] ?? 6),
            'columns' => $config['columns'] ?? ($source['columns'] ?? 3),
            'slider' => $config['slider'] ?? ($source['slider'] ?? true),
        ]);

        $ids = collect($source['selected_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->values()
            ->all();
        $direction = ($source['sort_direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        return match ($section->section_type) {
            'service-list' => ['services' => $this->services($config, $ids, $direction)],
            'property-list' => ['properties' => $this->properties($config, $ids, $direction)],
            'featured-gallery-slider' => ['featuredProperties' => $this->featuredProperties($config, $ids, $direction)],
            'team-slider' => ['teamMembers' => $this->teamMembers($config, $ids, $direction)],
            'testimonial-slider' => $this->testimonials($config, $ids, $direction),
            'blog-slider' => ['posts' => $this->posts($config, $ids, $direction)],
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

    private function properties(array $config, array $ids, string $direction): Collection
    {
        $query = Property::published();
        if ($ids !== []) {
            $query->whereIn('id', $ids);
        }

        $this->applyPropertyFilters($query, $config['filters'] ?? []);
        $listingFilter = $config['listing_filter'] ?? 'all';
        if (in_array($listingFilter, ['sale', 'rent'], true)) {
            $query->where('listing_type', $listingFilter);
        } elseif ($listingFilter === 'featured') {
            $query->where('is_featured', true);
        }

        $sort = in_array($config['sort_by'] ?? 'published_at', ['published_at', 'created_at', 'price'], true)
            ? ($config['sort_by'] ?? 'published_at')
            : 'published_at';

        return $query->orderBy($sort, $direction)
            ->limit(max(1, min(24, (int) ($config['items_limit'] ?? 8))))
            ->get();
    }

    private function featuredProperties(array $config, array $ids, string $direction): Collection
    {
        $query = Property::published()
            ->where('is_featured', true)
            ->with('creator:id,name');

        if ($ids !== []) {
            $query->whereIn('id', $ids);
        }

        $this->applyPropertyFilters($query, $config['filters'] ?? []);
        $sort = in_array($config['sort_by'] ?? 'published_at', ['published_at', 'created_at', 'price'], true)
            ? ($config['sort_by'] ?? 'published_at')
            : 'published_at';

        return $query->orderBy($sort, $direction)
            ->limit(max(1, min(24, (int) ($config['items_limit'] ?? 3))))
            ->get();
    }

    private function testimonials(array $config, array $ids, string $direction): array
    {
        $query = Testimonial::query()
            ->where('status', 'published')
            ->where('is_active', true);

        if ($ids !== []) {
            $query->whereIn('id', $ids);
        }

        $filters = $config['filters'] ?? [];
        if (isset($filters['min_rating']) && $filters['min_rating'] !== '') {
            $query->where('rating', '>=', (int) $filters['min_rating']);
        }

        $sort = in_array($config['sort_by'] ?? 'sort_order', ['sort_order', 'created_at', 'name'], true)
            ? ($config['sort_by'] ?? 'sort_order')
            : 'sort_order';
        $items = (clone $query)
            ->orderBy($sort, $direction)
            ->limit(max(1, min(24, (int) ($config['items_limit'] ?? 4))))
            ->get();

        return [
            'testimonials' => $items,
            'averageRating' => round((float) ((clone $query)->whereNotNull('rating')->avg('rating') ?? 0), 1),
            'reviewCount' => (clone $query)->count(),
        ];
    }

    private function teamMembers(array $config, array $ids, string $direction): Collection
    {
        $query = TeamMember::query()
            ->where('status', 'published')
            ->where('is_active', true);

        if ($ids !== []) {
            $query->whereIn('id', $ids);
        }

        $designation = trim((string) ($config['filters']['designation'] ?? ''));
        if ($designation !== '') {
            $query->where('designation', 'like', '%' . $designation . '%');
        }

        $sort = in_array($config['sort_by'] ?? 'sort_order', ['sort_order', 'created_at', 'name'], true)
            ? ($config['sort_by'] ?? 'sort_order')
            : 'sort_order';

        return $query->orderBy($sort, $direction)
            ->limit(max(1, min(24, (int) ($config['items_limit'] ?? 6))))
            ->get();
    }

    private function posts(array $config, array $ids, string $direction): Collection
    {
        $query = Post::published()->with('author');
        if ($ids !== []) {
            $query->whereIn('id', $ids);
        }

        $categoryId = $config['filters']['category_id'] ?? null;
        if ($categoryId) {
            $query->whereHas('categories', fn ($categories) => $categories->whereKey((int) $categoryId));
        }

        $sort = in_array($config['sort_by'] ?? 'published_at', ['published_at', 'created_at', 'title'], true)
            ? ($config['sort_by'] ?? 'published_at')
            : 'published_at';

        return $query->orderBy($sort, $direction)
            ->limit(max(1, min(24, (int) ($config['items_limit'] ?? 6))))
            ->get();
    }

    private function applyPropertyFilters($query, array $filters): void
    {
        $listingType = $filters['listing_type'] ?? null;
        if (in_array($listingType, ['sale', 'rent', 'lease'], true)) {
            $query->where('listing_type', $listingType);
        }

        if (! empty($filters['category_id'])) {
            $query->where('category_id', (int) $filters['category_id']);
        }

        foreach (['city', 'property_type'] as $field) {
            $value = trim((string) ($filters[$field] ?? ''));
            if ($value !== '') {
                $query->where($field, 'like', '%' . $value . '%');
            }
        }

        if (isset($filters['min_price']) && $filters['min_price'] !== '') {
            $query->where('price', '>=', (float) $filters['min_price']);
        }

        if (isset($filters['max_price']) && $filters['max_price'] !== '') {
            $query->where('price', '<=', (float) $filters['max_price']);
        }
    }
}
