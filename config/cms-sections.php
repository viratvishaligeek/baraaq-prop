<?php

$heroDesigns = [
    'index' => ['label' => 'Hero — Home 1', 'view' => 'frontend.sections.hero.index'],
    'home-2' => ['label' => 'Hero — Home 2', 'view' => 'frontend.sections.hero.home-2'],
    'home-3' => ['label' => 'Hero — Home 3', 'view' => 'frontend.sections.hero.home-3'],
    'home-4' => ['label' => 'Hero — Home 4', 'view' => 'frontend.sections.hero.home-4'],
    'home-5' => ['label' => 'Hero — Home 5', 'view' => 'frontend.sections.hero.home-5'],
    'home-6' => ['label' => 'Hero — Home 6', 'view' => 'frontend.sections.hero.home-6'],
    'home-7' => ['label' => 'Hero — Home 7', 'view' => 'frontend.sections.hero.home-7'],
    'home-8' => ['label' => 'Hero — Home 8', 'view' => 'frontend.sections.hero.home-8'],
    'home-9' => ['label' => 'Hero — Home 9', 'view' => 'frontend.sections.hero.home-9'],
    'home-10' => ['label' => 'Hero — Home 10', 'view' => 'frontend.sections.hero.home-10'],
];

$heroFields = [
    'eyebrow' => ['label' => 'Eyebrow / small heading', 'type' => 'text', 'max' => 120],
    'heading' => ['label' => 'Main heading', 'type' => 'textarea', 'max' => 500],
    'description' => ['label' => 'Description', 'type' => 'textarea', 'max' => 1500],
    'primary_button_label' => ['label' => 'Primary button label', 'type' => 'text', 'max' => 80],
    'primary_button_url' => ['label' => 'Primary button URL', 'type' => 'url', 'max' => 2048],
    'secondary_button_label' => ['label' => 'Secondary button label', 'type' => 'text', 'max' => 80],
    'secondary_button_url' => ['label' => 'Secondary button URL', 'type' => 'url', 'max' => 2048],
    'background_image' => ['label' => 'Background image path or URL', 'type' => 'image', 'max' => 2048],
    'overlay_color' => ['label' => 'Overlay color', 'type' => 'color'],
    'overlay_opacity' => ['label' => 'Overlay opacity (%)', 'type' => 'number', 'min' => 0, 'max' => 100],
];

$sections = [];

foreach ($heroDesigns as $design => $definition) {
    $key = 'hero.'.$design;

    $sections[$key] = [
        'key' => $key,
        'label' => $definition['label'],
        'type' => 'hero',
        'source_design' => $design,
        'view' => $definition['view'],
        'defaults' => [],
        'fields' => $heroFields,
    ];
}

$sections['properties.index'] = [
    'key' => 'properties.index',
    'label' => 'Featured properties — Home 1',
    'type' => 'property-list',
    'source_design' => 'index',
    'view' => 'frontend.sections.properties.index',
    'defaults' => [],
    'fields' => [
        'eyebrow' => ['label' => 'Section eyebrow', 'type' => 'text', 'max' => 120],
        'heading' => ['label' => 'Section heading', 'type' => 'text', 'max' => 180],
        'description' => ['label' => 'Introductory text', 'type' => 'textarea', 'max' => 1000],
        'items_limit' => ['label' => 'Number of properties', 'type' => 'number', 'min' => 1, 'max' => 6],
        'listing_filter' => ['label' => 'Listing filter', 'type' => 'select', 'options' => ['all' => 'All listings', 'sale' => 'For sale', 'rent' => 'For rent', 'featured' => 'Featured only']],
        'button_label' => ['label' => 'View-all button label', 'type' => 'text', 'max' => 80],
        'button_url' => ['label' => 'View-all button URL', 'type' => 'url', 'max' => 2048],
    ],
];

$sections['team.index'] = [
    'key' => 'team.index',
    'label' => 'Team members — Home 1',
    'type' => 'team',
    'source_design' => 'index',
    'view' => 'frontend.sections.team.index',
    'defaults' => [],
    'fields' => [
        'eyebrow' => ['label' => 'Section eyebrow', 'type' => 'text', 'max' => 120],
        'heading' => ['label' => 'Section heading', 'type' => 'text', 'max' => 180],
        'description' => ['label' => 'Introductory text', 'type' => 'textarea', 'max' => 1000],
        'items_limit' => ['label' => 'Number of team members', 'type' => 'number', 'min' => 1, 'max' => 4],
        'show_email' => ['label' => 'Show email address', 'type' => 'checkbox'],
    ],
];

$sections['blog.index'] = [
    'key' => 'blog.index',
    'label' => 'Latest articles — Home 1',
    'type' => 'blog',
    'source_design' => 'index',
    'view' => 'frontend.sections.blog.index',
    'defaults' => [],
    'fields' => [
        'eyebrow' => ['label' => 'Section eyebrow', 'type' => 'text', 'max' => 120],
        'heading' => ['label' => 'Section heading', 'type' => 'text', 'max' => 180],
        'description' => ['label' => 'Introductory text', 'type' => 'textarea', 'max' => 1000],
        'items_limit' => ['label' => 'Number of articles', 'type' => 'number', 'min' => 1, 'max' => 3],
        'button_label' => ['label' => 'View-all button label', 'type' => 'text', 'max' => 80],
        'button_url' => ['label' => 'View-all button URL', 'type' => 'url', 'max' => 2048],
        'show_author' => ['label' => 'Show author', 'type' => 'checkbox'],
        'show_date' => ['label' => 'Show publish date', 'type' => 'checkbox'],
    ],
];

$categoryVariants = [
    'categories.home-2' => ['design' => 'home-2', 'label' => 'Property categories — Home 2', 'view' => 'frontend.sections.categories.slider'],
    'categories.home-3' => ['design' => 'home-3', 'label' => 'Property categories — Home 3', 'view' => 'frontend.sections.categories.slider'],
    'categories.home-7' => ['design' => 'home-7', 'label' => 'Property categories — Home 7', 'view' => 'frontend.sections.categories.slider'],
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
            'background_color' => $variant['design'] === 'home-3' ? '#ffffff' : '#f5f5f5',
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

return [
    'sections' => $sections,
];
