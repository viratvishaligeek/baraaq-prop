<?php

$sections = [];

$categoryVariants = [
    'categories.home-2' => ['design' => 'home-2', 'label' => 'Property categories — Home 2', 'view' => 'frontend.sections.categories.slider'],
    'categories.home-3' => ['design' => 'home-3', 'label' => 'Property categories — Home 3', 'view' => 'frontend.sections.categories.slider'],
    'categories.home-7' => ['design' => 'home-7', 'label' => 'Property categories — Home 7', 'view' => 'frontend.sections.categories.slider'],
    'categories.home-10' => ['design' => 'home-10', 'label' => 'Property categories — Tabbed property types (Home 10)', 'view' => 'frontend.sections.categories.home-10'],
];

foreach ($categoryVariants as $key => $variant) {
    $sections[$key] = [
        'key' => $key,
        'label' => $variant['label'],
        'type' => 'category-slider',
        'source_design' => $variant['design'],
        'view' => $variant['view'],
        'defaults' => [
            'eyebrow' => 'CATEGORIES',
            'heading' => 'Choose Your Property Type',
            'alignment' => 'center',
            'columns' => 6,
            'items_limit' => 7,
            'content_source' => 'categories',
            'show_property_count' => true,
            'vertical_spacing' => 'space',
            'show_mobile' => true,
            'show_tablet' => true,
            'show_desktop' => true,
        ],
        'fields' => [
            'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
            'heading' => ['label' => 'Heading', 'type' => 'text', 'max' => 180],
            'description' => ['label' => 'Description', 'type' => 'textarea', 'max' => 1000],
            'alignment' => ['label' => 'Heading alignment', 'type' => 'select', 'options' => ['left' => 'Left', 'center' => 'Center', 'right' => 'Right']],
            'columns' => ['label' => 'Desktop slider columns', 'type' => 'number', 'min' => 1, 'max' => 6],
            'items_limit' => ['label' => 'Maximum categories', 'type' => 'number', 'min' => 1, 'max' => 20],
            'content_source' => ['label' => 'Content source', 'type' => 'select', 'options' => ['categories' => 'Property categories']],
            'show_property_count' => ['label' => 'Show property count', 'type' => 'checkbox'],
            'background_color' => ['label' => 'Background color', 'type' => 'color'],
            'background_image' => ['label' => 'Background image', 'type' => 'image', 'max' => 2048],
            'vertical_spacing' => ['label' => 'Vertical spacing', 'type' => 'select', 'options' => ['space' => 'Theme spacing', 'space-top' => 'Top only', 'space-bottom' => 'Bottom only', 'space-extra-bottom' => 'Extra bottom', 'py-0' => 'No vertical spacing']],
            'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
            'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
            'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
        ],
    ];
}


return $sections;
