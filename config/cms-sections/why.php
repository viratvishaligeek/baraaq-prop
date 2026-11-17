<?php

return [
    'why.home-2' => [
        'key' => 'why.home-2',
        'label' => 'Benefits cards — Home 2',
        'type' => 'why-cards',
        'source_design' => 'home-2',
        'view' => 'frontend.sections.why.home-2',
        'defaults' => [
            'eyebrow' => 'What We Do',
            'heading' => 'The Benefits For You',
            'columns' => 3,
            'vertical_spacing' => 'space',
            'shape_image' => '/theme-assets/img/shape/why-2-shape.png',
            'hover_icon' => '/theme-assets/img/icon/why-hover-icon-1.png',
            'animate_cards' => true,
            'show_mobile' => true,
            'show_tablet' => true,
            'show_desktop' => true,
            'item_1_number' => '01',
            'item_1_icon' => '/theme-assets/img/icon/why-icon-1-1.svg',
            'item_1_title' => 'Find your perfect new home',
            'item_1_text' => 'Velox surgo clarus tantillus confido carus video lumen cedo virtus spes decerno.',
            'item_1_button' => 'Find a Home',
            'item_1_url' => '/properties',
            'item_2_number' => '02',
            'item_2_icon' => '/theme-assets/img/icon/why-icon-1-2.svg',
            'item_2_title' => 'You build wealth effortlessly',
            'item_2_text' => 'Altus cedo tantillus video patrocinor valeo carus subseco vestrum credo virtus.',
            'item_2_button' => 'Sell a Home',
            'item_2_url' => '/contact',
            'item_3_number' => '03',
            'item_3_icon' => '/theme-assets/img/icon/why-icon-1-3.svg',
            'item_3_title' => 'You rent and love it like your own',
            'item_3_text' => 'Tantillus certe patrocinor video adipisci valeo carus. Subseco vestrum taedium.',
            'item_3_button' => 'Rent a Home',
            'item_3_url' => '/properties?listing_type=rent',
        ],
        'fields' => array_merge([
            'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
            'heading' => ['label' => 'Heading', 'type' => 'text', 'max' => 180],
            'columns' => ['label' => 'Desktop card columns', 'type' => 'select', 'options' => [2 => '2', 3 => '3']],
            'vertical_spacing' => ['label' => 'Section spacing', 'type' => 'select', 'options' => ['space' => 'Theme spacing', 'space-top' => 'Top only', 'space-bottom' => 'Bottom only', 'py-0' => 'No vertical spacing']],
            'shape_image' => ['label' => 'Decorative shape image', 'type' => 'image', 'max' => 2048],
            'hover_icon' => ['label' => 'Card hover icon', 'type' => 'image', 'max' => 2048],
            'background_color' => ['label' => 'Background color', 'type' => 'color'],
            'background_image' => ['label' => 'Background image', 'type' => 'image', 'max' => 2048],
            'animate_cards' => ['label' => 'Enable entrance animations', 'type' => 'checkbox'],
            'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
            'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
            'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
        ], (function () {
            $fields = [];
            for ($i = 1; $i <= 3; $i++) {
                $fields['item_'.$i.'_number'] = ['label' => 'Card '.$i.' number', 'type' => 'text', 'max' => 8];
                $fields['item_'.$i.'_icon'] = ['label' => 'Card '.$i.' icon', 'type' => 'image', 'max' => 2048];
                $fields['item_'.$i.'_title'] = ['label' => 'Card '.$i.' title', 'type' => 'text', 'max' => 180];
                $fields['item_'.$i.'_text'] = ['label' => 'Card '.$i.' description', 'type' => 'textarea', 'max' => 500];
                $fields['item_'.$i.'_button'] = ['label' => 'Card '.$i.' button', 'type' => 'text', 'max' => 80];
                $fields['item_'.$i.'_url'] = ['label' => 'Card '.$i.' URL', 'type' => 'url', 'max' => 2048];
            }
            return $fields;
        })()),
    ],
];
