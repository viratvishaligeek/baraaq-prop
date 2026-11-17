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

$sections = array_merge($sections, require __DIR__.'/cms-sections/categories.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/about.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/property-values.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/neighborhoods.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/team.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/why.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/properties.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/faq.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/download.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/marquee.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/testimonials.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/featured-properties.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/blog.php');

return [
    'sections' => $sections,
];
