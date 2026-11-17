<?php

$sharedFields = [
    'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
    'heading' => ['label' => 'Heading', 'type' => 'textarea', 'max' => 240],
    'description' => ['label' => 'Description', 'type' => 'textarea', 'max' => 1800],
    'button_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 80],
    'button_url' => ['label' => 'Button URL', 'type' => 'url', 'max' => 2048],
    'image_primary' => ['label' => 'Primary image', 'type' => 'image', 'max' => 2048],
    'image_secondary' => ['label' => 'Secondary image', 'type' => 'image', 'max' => 2048],
    'image_tertiary' => ['label' => 'Third image', 'type' => 'image', 'max' => 2048],
    'mission_title' => ['label' => 'Mission title', 'type' => 'text', 'max' => 100],
    'mission_text' => ['label' => 'Mission text', 'type' => 'textarea', 'max' => 700],
    'vision_title' => ['label' => 'Vision title', 'type' => 'text', 'max' => 100],
    'vision_text' => ['label' => 'Vision text', 'type' => 'textarea', 'max' => 700],
    'show_stats' => ['label' => 'Show statistics', 'type' => 'checkbox'],
    'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
    'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
    'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
];
for ($i = 1; $i <= 4; $i++) {
    $sharedFields['stat_'.$i.'_value'] = ['label' => 'Statistic '.$i.' value', 'type' => 'text', 'max' => 24];
    $sharedFields['stat_'.$i.'_label'] = ['label' => 'Statistic '.$i.' label', 'type' => 'text', 'max' => 80];
}
$sharedDefaults = [
    'eyebrow' => 'About Us',
    'heading' => 'A better way to discover your next home',
    'description' => 'We combine local knowledge, thoughtful service and a carefully curated property portfolio to help every client move forward with confidence.',
    'button_label' => 'More About Us',
    'button_url' => '/about',
    'image_primary' => '/theme-assets/img/about/about-1-1.jpg',
    'image_secondary' => '/theme-assets/img/about/about-1_1.jpg',
    'image_tertiary' => '/theme-assets/img/about/about-1_2.jpg',
    'mission_title' => 'Our Mission',
    'mission_text' => 'To make every property decision clear, informed and rewarding through dependable guidance and personal attention.',
    'vision_title' => 'Our Vision',
    'vision_text' => 'To set a higher standard for real estate service through integrity, local expertise and lasting relationships.',
    'show_stats' => true,
    'show_mobile' => true, 'show_tablet' => true, 'show_desktop' => true,
    'stat_1_value' => '1250+', 'stat_1_label' => 'Project handovers',
    'stat_2_value' => '$20M', 'stat_2_label' => 'Property value',
    'stat_3_value' => '100%', 'stat_3_label' => 'Client commitment',
    'stat_4_value' => '150+', 'stat_4_label' => 'Happy customers',
];

$designs = [
    'about.home-4' => ['label' => 'About — Editorial mission and vision (Home 4)', 'design' => 'home-4', 'view' => 'frontend.sections.about.home-4'],
    'about.home-7' => ['label' => 'About — Split statement and image story (Home 7)', 'design' => 'home-7', 'view' => 'frontend.sections.about.home-7'],
    'about.home-8' => ['label' => 'About — Local agency with statistics (Home 8)', 'design' => 'home-8', 'view' => 'frontend.sections.about.home-8'],
    'about.home-9' => ['label' => 'About — Mission and vision triptych (Home 9)', 'design' => 'home-9', 'view' => 'frontend.sections.about.home-9'],
    'about.home-10' => ['label' => 'About — Modern image panorama (Home 10)', 'design' => 'home-10', 'view' => 'frontend.sections.about.home-10'],
];

$sections = [];
foreach ($designs as $key => $design) {
    $sections[$key] = [
        'key' => $key,
        'label' => $design['label'],
        'type' => 'about-composition',
        'source_design' => $design['design'],
        'view' => $design['view'],
        'defaults' => $sharedDefaults,
        'fields' => $sharedFields,
    ];
}

return $sections;
