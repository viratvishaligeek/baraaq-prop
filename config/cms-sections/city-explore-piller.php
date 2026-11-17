<?php
return [
    'gallery.home-1-city-explore' => [
        'key' => 'gallery.home-1-city-explore',
        'label' => 'Image Gallery — Panoramic property explorer (Home 1)',
        'type' => 'city-explore-gallery',
        'source_design' => 'home-1',
        'view' => 'frontend.sections.gallery.city-explore-home-1',
        'defaults' => [
            'eyebrow' => 'Explore Places',
            'heading' => 'Discover Homes and Neighbourhoods Around You',
            'items_limit' => 1,
            'panorama_image_1' => '/theme-assets/img/explore-cites/explore-cites-bg-1.jpg',
            'panorama_image_2' => '/theme-assets/img/explore-cites/explore-cites-bg-2.jpg',
            'panorama_image_3' => '/theme-assets/img/explore-cites/explore-cites-bg-3.jpg',
            'show_mobile' => true, 'show_tablet' => true, 'show_desktop' => true,
        ],
        'fields' => [
            'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 100],
            'heading' => ['label' => 'Heading', 'type' => 'text', 'max' => 200],
            'panorama_image_1' => ['label' => 'Panorama image 1', 'type' => 'image', 'max' => 2048],
            'panorama_image_2' => ['label' => 'Panorama image 2', 'type' => 'image', 'max' => 2048],
            'panorama_image_3' => ['label' => 'Panorama image 3', 'type' => 'image', 'max' => 2048],
            'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
            'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
            'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
        ],
    ],
];
