<?php

return [
    'marquee.home-3' => [
        'key' => 'marquee.home-3',
        'label' => 'Promotional marquee — Home 3',
        'type' => 'marquee-strip',
        'source_design' => 'home-3',
        'view' => 'frontend.sections.marquee.home-3',
        'defaults' => [
            'shape_image' => '/theme-assets/img/shape/faq-2-right.png',
            'items_limit' => 3,
            'duplicate_groups' => 2,
            'background_color' => '',
            'item_1_text' => 'Luxury Home Available - Explore Now!',
            'item_1_url' => '/properties',
            'item_1_icon' => '/theme-assets/img/icon/marquee-icon-1-1.svg',
            'item_2_text' => 'Deluxe Cottage - Explore Now!',
            'item_2_url' => '/properties',
            'item_2_icon' => '/theme-assets/img/icon/marquee-icon-1-1.svg',
            'item_3_text' => 'Pet-friendly Rental Houses',
            'item_3_url' => '/properties',
            'item_3_icon' => '/theme-assets/img/icon/marquee-icon-1-1.svg',
            'show_mobile' => true, 'show_tablet' => true, 'show_desktop' => true,
        ],
        'fields' => array_merge([
            'shape_image' => ['label' => 'Decorative shape image', 'type' => 'image', 'max' => 2048],
            'items_limit' => ['label' => 'Marquee messages', 'type' => 'number', 'min' => 1, 'max' => 3],
            'duplicate_groups' => ['label' => 'Repeated marquee groups', 'type' => 'number', 'min' => 1, 'max' => 4],
            'background_color' => ['label' => 'Marquee background color', 'type' => 'color'],
            'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
            'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
            'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
        ], (function () {
            $fields = [];
            for ($i = 1; $i <= 3; $i++) {
                $fields['item_'.$i.'_text'] = ['label' => 'Message '.$i, 'type' => 'text', 'max' => 180];
                $fields['item_'.$i.'_url'] = ['label' => 'Message '.$i.' URL', 'type' => 'url', 'max' => 2048];
                $fields['item_'.$i.'_icon'] = ['label' => 'Message '.$i.' icon', 'type' => 'image', 'max' => 2048];
            }
            return $fields;
        })()),
    ],
];
