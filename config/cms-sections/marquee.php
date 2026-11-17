<?php

return [
    'marquee.home-2' => [
        'key' => 'marquee.home-2',
        'label' => 'Property promotion marquee — Home 2',
        'type' => 'marquee-strip',
        'source_design' => 'home-2',
        'view' => 'frontend.sections.marquee.home-2',
        'defaults' => [
            'background_color' => '#f5f5f5',
            'animation_duration' => 28,
            'repeat_count' => 4,
            'show_mobile' => true,
            'show_tablet' => true,
            'show_desktop' => true,
            'item_1_icon' => '/theme-assets/img/icon/marquee-icon-1-1.svg',
            'item_1_text' => 'Luxury Home Available - Explore Now!',
            'item_1_url' => '/properties',
            'item_2_icon' => '/theme-assets/img/icon/marquee-icon-1-1.svg',
            'item_2_text' => 'Deluxe Cottage - Explore Now!',
            'item_2_url' => '/properties',
            'item_3_icon' => '/theme-assets/img/icon/marquee-icon-1-1.svg',
            'item_3_text' => 'Pet-friendly Rental Houses',
            'item_3_url' => '/properties?listing_type=rent',
        ],
        'fields' => array_merge([
            'background_color' => ['label' => 'Background color', 'type' => 'color'],
            'background_image' => ['label' => 'Background image', 'type' => 'image', 'max' => 2048],
            'animation_duration' => ['label' => 'Marquee cycle duration (seconds)', 'type' => 'number', 'min' => 10, 'max' => 90],
            'repeat_count' => ['label' => 'Repeat item set', 'type' => 'number', 'min' => 1, 'max' => 8],
            'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
            'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
            'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
        ], (function () {
            $fields = [];
            for ($i = 1; $i <= 3; $i++) {
                $fields['item_'.$i.'_icon'] = ['label' => 'Promotion '.$i.' icon', 'type' => 'image', 'max' => 2048];
                $fields['item_'.$i.'_text'] = ['label' => 'Promotion '.$i.' text', 'type' => 'text', 'max' => 180];
                $fields['item_'.$i.'_url'] = ['label' => 'Promotion '.$i.' URL', 'type' => 'url', 'max' => 2048];
            }
            return $fields;
        })()),
    ],
];
