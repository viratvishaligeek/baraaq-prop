<?php

$sections = [
    'hero.home-9' => [
        'key' => 'hero.home-9',
        'label' => 'Hero — Home 9',
        'type' => 'hero',
        'view' => 'frontend.sections.hero.home-9',
        'defaults' => [
            'eyebrow' => 'Don’t Missed Out',
            'heading_prefix' => 'Discover',
            'heading_highlight' => 'Most Suitable',
            'heading_suffix' => 'Property',
            'description' => 'Explore top listings and let our expert agents guide you every step of the way to your new home.',
            'primary_button_label' => 'Explore Properties',
            'primary_button_url' => '/properties',
            'secondary_button_label' => 'Request A Visit',
            'secondary_button_url' => '/contact',
            'background_image' => '/theme-assets/img/hero/hero-9-bg.png',
            'foreground_image' => '/theme-assets/img/hero/hero-9-right.png',
        ],
        'fields' => [
            'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
            'heading_prefix' => ['label' => 'Heading prefix', 'type' => 'text', 'max' => 120],
            'heading_highlight' => ['label' => 'Highlighted heading', 'type' => 'text', 'max' => 120],
            'heading_suffix' => ['label' => 'Heading suffix', 'type' => 'text', 'max' => 120],
            'description' => ['label' => 'Description', 'type' => 'richtext', 'max' => 1500],
            'primary_button_label' => ['label' => 'Primary button label', 'type' => 'text', 'max' => 80],
            'primary_button_url' => ['label' => 'Primary button URL', 'type' => 'url', 'max' => 2048],
            'secondary_button_label' => ['label' => 'Secondary button label', 'type' => 'text', 'max' => 80],
            'secondary_button_url' => ['label' => 'Secondary button URL', 'type' => 'url', 'max' => 2048],
            'background_image' => ['label' => 'Hero background image', 'type' => 'image', 'max' => 2048],
            'foreground_image' => ['label' => 'Hero foreground image', 'type' => 'image', 'max' => 2048],
        ],
    ],
];

$sections = array_merge($sections, require __DIR__.'/cms-sections/about-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/statistics-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/services-piller.php');

$sections['properties.index'] = [
    'key' => 'properties.index',
    'label' => 'Featured properties — Home 9',
    'type' => 'property-list',
    'view' => 'frontend.sections.properties.index',
    'defaults' => [
        'eyebrow' => 'Popular Properties',
        'heading' => 'Best Properties Sale',
        'items_limit' => 8,
        'listing_filter' => 'all',
        'button_label' => 'View All Properties',
        'button_url' => '/properties',
        'card_button_label' => 'View More',
        'sale_label' => 'For Sale',
        'rent_label' => 'For Rent',
        'bed_label' => 'Bed',
        'bath_label' => 'Bath',
        'empty_state_text' => 'Featured properties will appear here once published.',
    ],
    'fields' => [
        'eyebrow' => ['label' => 'Section eyebrow', 'type' => 'text', 'max' => 120],
        'heading' => ['label' => 'Section heading', 'type' => 'text', 'max' => 180],
        'description' => ['label' => 'Introductory text', 'type' => 'textarea', 'max' => 1000],
        'items_limit' => ['label' => 'Number of properties', 'type' => 'number', 'min' => 1, 'max' => 12],
        'listing_filter' => ['label' => 'Listing filter', 'type' => 'select', 'options' => ['all' => 'All listings', 'sale' => 'For sale', 'rent' => 'For rent', 'featured' => 'Featured only']],
        'button_label' => ['label' => 'View-all button label', 'type' => 'text', 'max' => 80],
        'button_url' => ['label' => 'View-all button URL', 'type' => 'url', 'max' => 2048],
        'card_button_label' => ['label' => 'Property card button label', 'type' => 'text', 'max' => 80],
        'sale_label' => ['label' => 'For-sale badge label', 'type' => 'text', 'max' => 40],
        'rent_label' => ['label' => 'For-rent badge label', 'type' => 'text', 'max' => 40],
        'bed_label' => ['label' => 'Bedroom label', 'type' => 'text', 'max' => 30],
        'bath_label' => ['label' => 'Bathroom label', 'type' => 'text', 'max' => 30],
        'empty_state_text' => ['label' => 'Empty state text', 'type' => 'text', 'max' => 255],
    ],
];

$sections = array_merge($sections, require __DIR__.'/cms-sections/featured-gallery-home-9.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/home-details-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/team.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/testimonials-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/blog.php');

return ['sections' => $sections];
