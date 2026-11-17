<?php

$fields = [
    'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
    'heading' => ['label' => 'Heading', 'type' => 'text', 'max' => 200],
    'background_image' => ['label' => 'Section background image', 'type' => 'image', 'max' => 2048],
    'trust_icon' => ['label' => 'Trust rating icon', 'type' => 'image', 'max' => 2048],
    'client_avatar_image' => ['label' => 'Client avatar group image', 'type' => 'image', 'max' => 2048],
    'trust_label' => ['label' => 'Trust platform label', 'type' => 'text', 'max' => 80],
    'client_count' => ['label' => 'Client count', 'type' => 'number', 'min' => 0, 'max' => 999999],
    'client_suffix' => ['label' => 'Client count suffix', 'type' => 'text', 'max' => 12],
    'client_label' => ['label' => 'Client count label', 'type' => 'text', 'max' => 80],
    'hover_icon' => ['label' => 'Card hover icon', 'type' => 'image', 'max' => 2048],
    'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
    'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
    'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
];
for ($i = 1; $i <= 3; $i++) {
    $fields['item_'.$i.'_icon'] = ['label' => 'Card '.$i.' icon', 'type' => 'image', 'max' => 2048];
    $fields['item_'.$i.'_title'] = ['label' => 'Card '.$i.' title', 'type' => 'text', 'max' => 120];
    $fields['item_'.$i.'_text'] = ['label' => 'Card '.$i.' text', 'type' => 'richtext', 'max' => 1000];
    $fields['item_'.$i.'_button'] = ['label' => 'Card '.$i.' button label', 'type' => 'text', 'max' => 80];
    $fields['item_'.$i.'_url'] = ['label' => 'Card '.$i.' button URL', 'type' => 'url', 'max' => 2048];
}

return [
    'why.home-1' => [
        'key' => 'why.home-1',
        'label' => 'Why choose us — Trust summary and service cards (Home 1)',
        'type' => 'why-choose',
        'source_design' => 'index',
        'view' => 'frontend.sections.why.home-1',
        'defaults' => [
            'eyebrow' => 'Why Choose Us',
            'heading' => 'Trusted by 100+ Million Buyers',
            'background_image' => '/theme-assets/img/why/why-choose-1-bg.jpg',
            'trust_icon' => '/theme-assets/img/icon/star-icon.svg',
            'client_avatar_image' => '/theme-assets/img/hero/heror-1-avator-group.png',
            'trust_label' => 'Trustipilot',
            'client_count' => 19,
            'client_suffix' => 'k+',
            'client_label' => 'clients',
            'hover_icon' => '/theme-assets/img/icon/why-hover-icon-1.png',
            'show_mobile' => true, 'show_tablet' => true, 'show_desktop' => true,
            'item_1_icon' => '/theme-assets/img/icon/why-icon-1-1.svg',
            'item_1_title' => 'Find your home',
            'item_1_text' => 'Explore homes that match your needs with guidance from our property team.',
            'item_1_button' => 'Find a Home',
            'item_1_url' => '/properties',
            'item_2_icon' => '/theme-assets/img/icon/why-icon-1-2.svg',
            'item_2_title' => 'Sell a Property',
            'item_2_text' => 'Reach qualified buyers and get support through every step of your sale.',
            'item_2_button' => 'Sell a Home',
            'item_2_url' => '/contact',
            'item_3_icon' => '/theme-assets/img/icon/why-icon-1-3.svg',
            'item_3_title' => 'Rent a Home',
            'item_3_text' => 'Find a rental that fits your lifestyle, location and budget.',
            'item_3_button' => 'Rent a Home',
            'item_3_url' => '/properties?listing_type=rent',
        ],
        'fields' => $fields,
    ],
];
