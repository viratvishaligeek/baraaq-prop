<?php

namespace App\Services\Widgets;

class CmsWidgetRegistry
{
    private const GROUPS = [
        'hero' => 'Hero sections',
        'about-composition' => 'About sections',
        'service-list' => 'Services',
        'category-slider' => 'Property categories',
        'property-list' => 'Property listings',
        'property-slider-grid' => 'Property listings',
        'team-slider' => 'Team members',
        'testimonial-slider' => 'Testimonials',
        'blog-slider' => 'Blog posts',
        'featured-gallery-slider' => 'Image galleries',
        'neighborhood-feature-gallery' => 'Image galleries',
        'neighborhood-gallery' => 'Image galleries',
        'property-values-gallery' => 'Image galleries',
        'room-gallery' => 'Image galleries',
        'project-gallery' => 'Image galleries',
        'city-explore-gallery' => 'Image galleries',
        'process-steps' => 'Process steps',
        'faq' => 'Frequently asked questions',
        'faq-accordion' => 'Frequently asked questions',
        'statistics' => 'Statistics',
        'contact-panel' => 'Contact',
        'brand-slider' => 'Client logos',
        'cta' => 'Call to action',
    ];

    private const INSTANCE_STYLE_FIELDS = [
        'widget_background_color' => ['label' => 'Widget background color', 'type' => 'color'],
        'widget_background_image' => ['label' => 'Widget background image', 'type' => 'image', 'max' => 2048],
        'widget_heading_color' => ['label' => 'Widget heading color', 'type' => 'color'],
        'widget_text_color' => ['label' => 'Widget text color', 'type' => 'color'],
        'widget_heading_font_size' => ['label' => 'Heading font size (px)', 'type' => 'number', 'min' => 16, 'max' => 80],
        'widget_body_font_size' => ['label' => 'Body font size (px)', 'type' => 'number', 'min' => 12, 'max' => 32],
        'widget_padding_top' => ['label' => 'Padding top (px)', 'type' => 'number', 'min' => 0, 'max' => 240],
        'widget_padding_bottom' => ['label' => 'Padding bottom (px)', 'type' => 'number', 'min' => 0, 'max' => 240],
    ];

    public function all(): array
    {
        $definitions = config('cms-sections.sections', []);

        foreach ($definitions as $key => $definition) {
            $definition['fields'] = array_merge($definition['fields'] ?? [], self::INSTANCE_STYLE_FIELDS);
            $definitions[$key] = $definition;
        }

        return $definitions;
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
        $propertyFilters = [
            'listing_type' => ['label' => 'Listing type', 'type' => 'select', 'options' => ['all' => 'All', 'sale' => 'For sale', 'rent' => 'For rent']],
            'category_id' => ['label' => 'Property category ID', 'type' => 'integer', 'min' => 1],
            'city' => ['label' => 'City contains', 'type' => 'text', 'max' => 100],
            'property_type' => ['label' => 'Property type', 'type' => 'text', 'max' => 100],
            'min_price' => ['label' => 'Minimum price', 'type' => 'number', 'min' => 0],
            'max_price' => ['label' => 'Maximum price', 'type' => 'number', 'min' => 0],
        ];

        return [
            'service-list' => ['label' => 'Services', 'table' => 'services', 'sort' => ['published_at', 'created_at', 'title'], 'slider' => false, 'filters' => []],
            'city-explore-gallery' => ['label' => 'Featured property explorer', 'table' => 'properties', 'sort' => ['published_at', 'created_at', 'price'], 'filters' => $propertyFilters],
            'property-slider-grid' => ['label' => 'Properties', 'table' => 'properties', 'sort' => ['published_at', 'created_at', 'price'], 'filters' => $propertyFilters],
            'property-list' => ['label' => 'Properties', 'table' => 'properties', 'sort' => ['published_at', 'created_at', 'price'], 'slider' => false, 'filters' => $propertyFilters],
            'testimonial-slider' => ['label' => 'Testimonials', 'table' => 'testimonials', 'sort' => ['sort_order', 'created_at', 'name'], 'filters' => [
                'min_rating' => ['label' => 'Minimum rating', 'type' => 'integer', 'min' => 1, 'max' => 5],
            ]],
            'team-slider' => ['label' => 'Team members', 'table' => 'team_members', 'sort' => ['sort_order', 'created_at', 'name'], 'filters' => [
                'designation' => ['label' => 'Designation contains', 'type' => 'text', 'max' => 120],
            ]],
            'blog-slider' => ['label' => 'Blog posts', 'table' => 'posts', 'sort' => ['published_at', 'created_at', 'title'], 'filters' => [
                'category_id' => ['label' => 'Blog category ID', 'type' => 'integer', 'min' => 1],
            ]],
            'featured-gallery-slider' => ['label' => 'Featured properties', 'table' => 'properties', 'sort' => ['published_at', 'created_at', 'price'], 'filters' => $propertyFilters],
            'category-slider' => ['label' => 'Categories', 'table' => 'categories', 'sort' => ['sort_order', 'created_at', 'name'], 'filters' => []],
        ];
    }
}}
