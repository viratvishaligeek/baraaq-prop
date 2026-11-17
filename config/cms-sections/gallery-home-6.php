<?php
$galleryDefinitions = require __DIR__.'/gallery-piller.php';
$fields = $galleryDefinitions['gallery.home-5-rooms']['fields'];

return [
    'gallery.home-6-projects' => [
        'key' => 'gallery.home-6-projects', 'label' => 'Image Gallery — Featured project cards (Home 6)',
        'type' => 'project-gallery', 'source_design' => 'home-6', 'view' => 'frontend.sections.gallery.home-6-projects',
        'defaults' => [
            'eyebrow' => 'Featured Projects',
            'heading' => 'Explore a Curated Selection of Our Finest Projects',
            'items_limit' => 5, 'button_label' => 'View All Projects', 'button_url' => '/properties',
            'item_1_image' => '/theme-assets/img/project/project_1_1.jpg', 'item_1_title' => 'Premium Urban Living', 'item_1_text' => 'A Sophisticated Luxury Apartment Experience', 'item_1_url' => '/properties',
            'item_2_image' => '/theme-assets/img/project/project_1_2.jpg', 'item_2_title' => 'Corporate Hub', 'item_2_text' => 'Modern Business & Innovation Center', 'item_2_url' => '/properties',
            'item_3_image' => '/theme-assets/img/project/project_1_3.jpg', 'item_3_title' => 'Greenview Residences', 'item_3_text' => 'Luxury Living Apartment Complex', 'item_3_url' => '/properties',
            'item_4_image' => '/theme-assets/img/project/project_1_4.jpg', 'item_4_title' => 'Descriptive & Elegant', 'item_4_text' => 'Premium Grade-A Office Building', 'item_4_url' => '/properties',
            'item_5_image' => '/theme-assets/img/project/project_1_5.jpg', 'item_5_title' => 'Commercial Tower', 'item_5_text' => 'Eden Estate Residential Community', 'item_5_url' => '/properties',
            'show_mobile' => true, 'show_tablet' => true, 'show_desktop' => true,
        ],
        'fields' => $fields,
    ],
];
