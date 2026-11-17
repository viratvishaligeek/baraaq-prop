<?php

return [
    'why.home-3' => [
        'key' => 'why.home-3',
        'label' => 'Amenities carousel — Home 3',
        'type' => 'why-slider',
        'source_design' => 'home-3',
        'view' => 'frontend.sections.why.home-3',
        'defaults' => [
            'eyebrow' => 'What We Do',
            'heading' => 'See how Pillar can Help',
            'button_label' => 'View All Amenities',
            'button_url' => '/properties',
            'items_limit' => 7,
            'columns' => 4,
            'background_image' => '/theme-assets/img/shape/what-we-do-3-bg.png',
            'background_color' => '',
            'card_background' => '/theme-assets/img/shape/why-3-bg.png',
            'vertical_spacing' => 'space',
            'show_mobile' => true,
            'show_tablet' => true,
            'show_desktop' => true,
            'item_1_number' => '01',
            'item_1_icon' => '/theme-assets/img/icon/why-icon-1-1.svg',
            'item_1_title' => 'Find your perfect home',
            'item_1_text' => 'Velox surgo clarus tantillus confido carus video lumen spes decerno..',
            'item_1_button' => 'Find a Home',
            'item_1_url' => '/properties',
            'item_2_number' => '02',
            'item_2_icon' => '/theme-assets/img/icon/why-icon-1-2.svg',
            'item_2_title' => 'Rent love it like you',
            'item_2_text' => 'Altus cedo tantillus video patrocinor valeo subseco vestrum credo virtus.',
            'item_2_button' => 'Sell a Home',
            'item_2_url' => '/contact',
            'item_3_number' => '03',
            'item_3_icon' => '/theme-assets/img/icon/why-icon-1-3.svg',
            'item_3_title' => 'Build wealth effortlessly',
            'item_3_text' => 'Technology is revolutionizing the legal sector.Technology legal sector.',
            'item_3_button' => 'Rent a Home',
            'item_3_url' => '/properties?listing_type=rent',
            'item_4_number' => '04',
            'item_4_icon' => '/theme-assets/img/icon/why-icon-1-4.svg',
            'item_4_title' => 'Commercial Spaces',
            'item_4_text' => 'Confido carus video lumen Velox surgo clarus tantillus spes decerno.',
            'item_4_button' => 'Find a Home',
            'item_4_url' => '/properties',
            'item_5_number' => '05',
            'item_5_icon' => '/theme-assets/img/icon/why-icon-1-1.svg',
            'item_5_title' => 'Find your perfect home',
            'item_5_text' => 'Velox surgo clarus tantillus confido video lumen cedo spes decerno..',
            'item_5_button' => 'Sell a Home',
            'item_5_url' => '/contact',
            'item_6_number' => '06',
            'item_6_icon' => '/theme-assets/img/icon/why-icon-1-2.svg',
            'item_6_title' => 'Rent love it like you',
            'item_6_text' => 'Altus cedo tantillus video patrocinor valeo subseco vestrum credo virtus.',
            'item_6_button' => 'Rent a Home',
            'item_6_url' => '/properties?listing_type=rent',
            'item_7_number' => '07',
            'item_7_icon' => '/theme-assets/img/icon/why-icon-1-3.svg',
            'item_7_title' => 'Build wealth effortlessly',
            'item_7_text' => 'Technology is revolutionizing the legal sector.Technology legal sector.',
            'item_7_button' => 'Find a Home',
            'item_7_url' => '/properties',
        ],
        'fields' => array_merge([
            'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
            'heading' => ['label' => 'Heading', 'type' => 'text', 'max' => 180],
            'button_label' => ['label' => 'Section button label', 'type' => 'text', 'max' => 80],
            'button_url' => ['label' => 'Section button URL', 'type' => 'url', 'max' => 2048],
            'items_limit' => ['label' => 'Visible cards', 'type' => 'number', 'min' => 1, 'max' => 7],
            'columns' => ['label' => 'Desktop slides per view', 'type' => 'select', 'options' => [3 => '3', 4 => '4']],
            'background_image' => ['label' => 'Section background image', 'type' => 'image', 'max' => 2048],
            'background_color' => ['label' => 'Section background color', 'type' => 'color'],
            'card_background' => ['label' => 'Card background image', 'type' => 'image', 'max' => 2048],
            'vertical_spacing' => ['label' => 'Section spacing', 'type' => 'select', 'options' => ['space' => 'Theme spacing', 'space-top' => 'Top only', 'space-bottom' => 'Bottom only', 'py-0' => 'No vertical spacing']],
            'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
            'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
            'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
        ], (function () {
            $fields = [];
            for ($i = 1; $i <= 7; $i++) {
                $fields['item_'.$i.'_number'] = ['label' => 'Card '.$i.' number', 'type' => 'text', 'max' => 8];
                $fields['item_'.$i.'_icon'] = ['label' => 'Card '.$i.' icon', 'type' => 'image', 'max' => 2048];
                $fields['item_'.$i.'_title'] = ['label' => 'Card '.$i.' title', 'type' => 'text', 'max' => 180];
                $fields['item_'.$i.'_text'] = ['label' => 'Card '.$i.' description', 'type' => 'textarea', 'max' => 500];
                $fields['item_'.$i.'_button'] = ['label' => 'Card '.$i.' button label', 'type' => 'text', 'max' => 80];
                $fields['item_'.$i.'_url'] = ['label' => 'Card '.$i.' button URL', 'type' => 'url', 'max' => 2048];
            }
            return $fields;
        })()),
    ],
];
