<?php

namespace App\Services\Widgets;

class CmsWidgetRegistry
{
    private const GROUPS = [
        'hero' => 'Hero sections',
        'about-composition' => 'About sections',
        'category-slider' => 'Property categories',
        'property-list' => 'Property listings',
        'property-slider-grid' => 'Property listings',
        'team-slider' => 'Team members',
        'testimonial-slider' => 'Testimonials',
        'blog-slider' => 'Blog posts',
        'featured-gallery-slider' => 'Image galleries',
        'faq' => 'Frequently asked questions',
        'cta' => 'Call to action',
    ];

    public function all(): array
    {
        return config('cms-sections.sections', []);
    }

    public function find(string $key): ?array
    {
        $definition = $this->all()[$key] ?? null;

        return is_array($definition) && isset($definition['key'], $definition['type'], $definition['view'])
            ? $definition
            : null;
    }

    public function grouped(): array
    {
        $groups = [];

        foreach ($this->all() as $key => $definition) {
            if (! is_array($definition) || empty($definition['view']) || ! view()->exists($definition['view'])) {
                continue;
            }

            $type = $definition['type'] ?? 'content';
            $groups[self::GROUPS[$type] ?? ucfirst(str_replace('-', ' ', $type))][] = [
                'key' => $key,
                'label' => $definition['label'] ?? ucfirst(str_replace('-', ' ', $type)),
                'type' => $type,
                'design' => $definition['source_design'] ?? 'default',
            ];
        }

        ksort($groups);

        return $groups;
    }

    public function queryableTypes(): array
    {
        return [
            'property-slider-grid' => ['label' => 'Properties', 'table' => 'properties', 'sort' => ['published_at', 'created_at', 'price']],
            'property-list' => ['label' => 'Properties', 'table' => 'properties', 'sort' => ['published_at', 'created_at', 'price']],
            'testimonial-slider' => ['label' => 'Testimonials', 'table' => 'testimonials', 'sort' => ['sort_order', 'created_at', 'name']],
            'team-slider' => ['label' => 'Team members', 'table' => 'team_members', 'sort' => ['sort_order', 'created_at', 'name']],
            'blog-slider' => ['label' => 'Blog posts', 'table' => 'posts', 'sort' => ['published_at', 'created_at', 'title']],
            'featured-gallery-slider' => ['label' => 'Properties', 'table' => 'properties', 'sort' => ['published_at', 'created_at', 'price']],
            'category-slider' => ['label' => 'Categories', 'table' => 'categories', 'sort' => ['sort_order', 'created_at', 'name']],
        ];
    }
}
