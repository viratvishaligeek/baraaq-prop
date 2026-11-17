<?php

return [
    'brands.home-3' => [
        'key' => 'brands.home-3',
        'label' => 'Brand logo strip — Home 3',
        'type' => 'brand-slider',
        'source_design' => 'home-3',
        'view' => 'frontend.sections.brands.home-3',
        'defaults' => [
            'shape_image' => '/theme-assets/img/shape/download-circle.png',
            'items_limit' => 6,
            'slides_mobile' => 2,
            'slides_tablet' => 3,
            'slides_desktop' => 6,
            'vertical_spacing' => '',
            'background_color' => '',
            'item_1_image' => '/theme-assets/img/brand/brand_1_1.svg',
            'item_1_url' => '/about.html',
            'item_2_image' => '/theme-assets/img/brand/brand_1_2.svg',
            'item_2_url' => '/about.html',
            'item_3_image' => '/theme-assets/img/brand/brand_1_3.svg',
            'item_3_url' => '/about.html',
            'item_4_image' => '/theme-assets/img/brand/brand_1_4.svg',
            'item_4_url' => '/about.html',
            'item_5_image' => '/theme-assets/img/brand/brand_1_5.svg',
            'item_5_url' => '/about.html',
            'item_6_image' => '/theme-assets/img/brand/brand_1_6.svg',
            'item_6_url' => '/about.html',
            'show_mobile' => true, 'show_tablet' => true, 'show_desktop' => true,
        ],
        'fields' => array_merge([
            'shape_image' => ['label' => 'Decorative shape image', 'type' => 'image', 'max' => 2048],
            'items_limit' => ['label' => 'Number of logos', 'type' => 'number', 'min' => 1, 'max' => 6],
            'slides_mobile' => ['label' => 'Slides on mobile', 'type' => 'select', 'options' => [1 => '1', 2 => '2']],
            'slides_tablet' => ['label' => 'Slides on tablet', 'type' => 'select', 'options' => [2 => '2', 3 => '3', 4 => '4']],
            'slides_desktop' => ['label' => 'Slides on desktop', 'type' => 'select', 'options' => [4 => '4', 5 => '5', 6 => '6']],
            'vertical_spacing' => ['label' => 'Section spacing', 'type' => 'select', 'options' => ['' => 'Theme default', 'space' => 'Theme spacing', 'space-top' => 'Top only', 'space-bottom' => 'Bottom only', 'py-0' => 'No vertical spacing']],
            'background_color' => ['label' => 'Section background color', 'type' => 'color'],
            'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
            'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
            'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
        ], (function () {
            $fields = [];
            for ($i = 1; $i <= 6; $i++) {
                $fields['item_'.$i.'_image'] = ['label' => 'Logo '.$i.' image', 'type' => 'image', 'max' => 2048];
                $fields['item_'.$i.'_url'] = ['label' => 'Logo '.$i.' destination URL', 'type' => 'url', 'max' => 2048];
            }
            return $fields;
        })()),
    ],
];
