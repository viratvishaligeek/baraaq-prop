<?php

return [
    'properties.featured-index' => [
        'key' => 'properties.featured-index',
        'label' => 'Featured luxury properties — Home 1',
        'type' => 'property-list',
        'source_design' => 'index',
        'view' => 'frontend.sections.properties.index',
        'defaults' => [
            'eyebrow' => 'Listing Property',
            'heading' => 'Featured Luxury Properties',
            'items_limit' => 4,
            'listing_filter' => 'featured',
            'button_label' => 'View All Properties',
            'button_url' => '/properties',
        ],
        'fields' => [
            'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
            'heading' => ['label' => 'Heading', 'type' => 'text', 'max' => 180],
            'description' => ['label' => 'Introductory text', 'type' => 'textarea', 'max' => 1000],
            'items_limit' => ['label' => 'Number of properties', 'type' => 'number', 'min' => 1, 'max' => 6],
            'listing_filter' => ['label' => 'Listing filter', 'type' => 'select', 'options' => ['all' => 'All listings', 'sale' => 'For sale', 'rent' => 'For rent', 'featured' => 'Featured only']],
            'button_label' => ['label' => 'View-all button label', 'type' => 'text', 'max' => 80],
            'button_url' => ['label' => 'View-all button URL', 'type' => 'url', 'max' => 2048],
        ],
    ],
];
