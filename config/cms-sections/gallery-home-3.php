<?php

return [
    'gallery.home-3' => [
        'key' => 'gallery.home-3',
        'label' => 'Neighborhood feature gallery — Home 3',
        'type' => 'neighborhood-feature-gallery',
        'source_design' => 'home-3',
        'view' => 'frontend.sections.gallery.home-3',
        'defaults' => [
            'eyebrow' => 'Explore Cities',
            'heading' => 'Explore The Neighborhoods',
            'description' => 'Find your dream apartment with our listing',
            'button_label' => 'Browse All Now',
            'button_url' => '/properties',
            'shape_image' => '/theme-assets/img/shape/property-values-3-shape.png',
            'vertical_spacing' => 'space',
            'animate_cards' => true,
            'show_mobile' => true,
            'show_tablet' => true,
            'show_desktop' => true,
            'item_1_image' => '/theme-assets/img/property-values/property-values-3-1.jpg',
            'item_1_count' => '42 Properties',
            'item_1_title' => 'Cape Town, South Africa',
            'item_1_url' => '/properties',
            'item_2_image' => '/theme-assets/img/property-values/property-values-3-2.jpg',
            'item_2_count' => '34 Properties',
            'item_2_title' => 'Sydney, Australia',
            'item_2_url' => '/properties',
            'item_3_image' => '/theme-assets/img/property-values/property-values-3-3.jpg',
            'item_3_count' => '55 Properties',
            'item_3_title' => 'Los Angeles, New York',
            'item_3_url' => '/properties',
            'item_4_image' => '/theme-assets/img/property-values/property-values-3-4.jpg',
            'item_4_count' => '25 Properties',
            'item_4_title' => 'Seoul, South Korea',
            'item_4_url' => '/properties',
        ],
        'fields' => array_merge([
            'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
            'heading' => ['label' => 'Heading', 'type' => 'text', 'max' => 180],
            'description' => ['label' => 'Description', 'type' => 'textarea', 'max' => 600],
            'button_label' => ['label' => 'Browse button label', 'type' => 'text', 'max' => 80],
            'button_url' => ['label' => 'Browse button URL', 'type' => 'url', 'max' => 2048],
            'shape_image' => ['label' => 'Decorative background shape', 'type' => 'image', 'max' => 2048],
            'vertical_spacing' => ['label' => 'Section spacing', 'type' => 'select', 'options' => ['space' => 'Theme spacing', 'space-top' => 'Top only', 'space-bottom' => 'Bottom only', 'py-0' => 'No vertical spacing']],
            'background_color' => ['label' => 'Background color', 'type' => 'color'],
            'background_image' => ['label' => 'Background image', 'type' => 'image', 'max' => 2048],
            'animate_cards' => ['label' => 'Enable entrance animations', 'type' => 'checkbox'],
            'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
            'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
            'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
        ], (function () {
            $fields = [];
            for ($i = 1; $i <= 4; $i++) {
                $fields['item_'.$i.'_image'] = ['label' => 'City '.$i.' image', 'type' => 'image', 'max' => 2048];
                $fields['item_'.$i.'_count'] = ['label' => 'City '.$i.' property count', 'type' => 'text', 'max' => 80];
                $fields['item_'.$i.'_title'] = ['label' => 'City '.$i.' title', 'type' => 'text', 'max' => 150];
                $fields['item_'.$i.'_url'] = ['label' => 'City '.$i.' URL', 'type' => 'url', 'max' => 2048];
            }
            return $fields;
        })()),
    ],
];
