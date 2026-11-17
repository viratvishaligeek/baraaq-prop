<?php

$fields = [
    'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
    'heading' => ['label' => 'Heading', 'type' => 'textarea', 'max' => 240],
    'video_url' => ['label' => 'Video URL', 'type' => 'url', 'max' => 2048],
    'video_image' => ['label' => 'Video thumbnail image', 'type' => 'image', 'max' => 2048],
    'background_image' => ['label' => 'Section background image', 'type' => 'image', 'max' => 2048],
    'side_image' => ['label' => 'Decorative right-side image', 'type' => 'image', 'max' => 2048],
    'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
    'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
    'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
];
for ($i = 1; $i <= 4; $i++) {
    $fields['counter_'.$i.'_icon'] = ['label' => 'Counter '.$i.' icon', 'type' => 'image', 'max' => 2048];
    $fields['counter_'.$i.'_value'] = ['label' => 'Counter '.$i.' value', 'type' => 'number', 'min' => 0, 'max' => 9999999];
    $fields['counter_'.$i.'_suffix'] = ['label' => 'Counter '.$i.' suffix', 'type' => 'text', 'max' => 12];
    $fields['counter_'.$i.'_label'] = ['label' => 'Counter '.$i.' label', 'type' => 'text', 'max' => 100];
}

return [
    'property-values.home-1' => [
        'key' => 'property-values.home-1',
        'label' => 'Property values — Video and statistics (Home 1)',
        'type' => 'property-values-gallery',
        'source_design' => 'index',
        'view' => 'frontend.sections.property-values.home-1',
        'defaults' => [
            'eyebrow' => 'Property Values',
            'heading' => 'Market Your Property with Pillar',
            'video_url' => 'https://www.youtube.com/watch?v=_sI_Ps7JSEk',
            'video_image' => '/theme-assets/img/property-values/property-values-1-1.jpg',
            'background_image' => '/theme-assets/img/bg/property-values-bg-shape.png',
            'side_image' => '/theme-assets/img/property-values/propery-values-right-man.png',
            'show_mobile' => true, 'show_tablet' => true, 'show_desktop' => true,
            'counter_1_icon' => '/theme-assets/img/icon/counter-icon-1-1.svg',
            'counter_1_value' => 28, 'counter_1_suffix' => '+', 'counter_1_label' => 'Years of Business',
            'counter_2_icon' => '/theme-assets/img/icon/counter-icon-1-2.svg',
            'counter_2_value' => 58, 'counter_2_suffix' => '+', 'counter_2_label' => 'Properties Sold',
            'counter_3_icon' => '/theme-assets/img/icon/counter-icon-1-3.svg',
            'counter_3_value' => 25, 'counter_3_suffix' => 'k', 'counter_3_label' => '5 Stars Reviews',
            'counter_4_icon' => '/theme-assets/img/icon/counter-icon-1-4.svg',
            'counter_4_value' => 98, 'counter_4_suffix' => '%', 'counter_4_label' => 'Happy Customers',
        ],
        'fields' => $fields,
    ],
];
