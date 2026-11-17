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
    'background_color' => ['label' => 'Background color', 'type' => 'color'],
    'background_image' => ['label' => 'Background image', 'type' => 'image', 'max' => 2048],
    'vertical_spacing' => ['label' => 'Vertical spacing', 'type' => 'select', 'options' => ['space' => 'Large', 'space-top' => 'Top only', 'space-bottom' => 'Bottom only', 'space-sm' => 'Compact']],
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
    'background_color' => '',
    'vertical_spacing' => 'space',
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
        'defaults' => array_merge($sharedDefaults, match ($key) {
            'about.home-4' => [
                'eyebrow' => 'Who We Are',
                'heading' => 'Elevating Standards In Luxury Real Estate',
                'description' => 'We specialize in luxury properties, sustainable homes, and vacation rentals — driven by a passion for exceptional living and a commitment to quality, innovation, and client satisfaction.',
                'image_primary' => '/theme-assets/img/about/about-1-1.jpg',
                'image_secondary' => '/theme-assets/img/about/about-1_1.jpg',
                'image_tertiary' => '/theme-assets/img/about/about-1_2.jpg',
            ],
            'about.home-7' => [
                'eyebrow' => 'About Us',
                'heading' => 'We help buyers, sellers, and investors navigate the property market with ease, offering expert guidance and a commitment to outstanding service.',
                'description' => 'Simply scan the QR code on the right with your phone’s camera, then click the prompt to open the app download page.',
                'image_primary' => '/theme-assets/img/about/about-5-left.jpg',
                'image_secondary' => '/theme-assets/img/about/about-5-right-1.jpg',
                'image_tertiary' => '/theme-assets/img/about/about-5-right-2.jpg',
                'stat_1_value' => '98+', 'stat_1_label' => 'Offices worldwide',
                'stat_2_value' => '160+', 'stat_2_label' => 'Completed projects',
                'stat_3_value' => '3M', 'stat_3_label' => 'Total project expenditure',
            ],
            'about.home-8' => [
                'eyebrow' => 'About Our Local Agency',
                'heading' => 'We are the leader in the Local Real Estate Agency',
                'description' => 'Our local real estate agency is built on a deep understanding of the neighborhoods, communities, and property trends that shape our area. With years of experience in the local market, we provide personalized guidance for buyers.',
                'image_primary' => '/theme-assets/img/about/about-6-img.jpg',
                'stat_1_value' => '98+', 'stat_1_label' => 'Offices worldwide',
                'stat_2_value' => '162+', 'stat_2_label' => 'Completed projects',
                'stat_3_value' => '150+', 'stat_3_label' => 'Products available',
                'stat_4_value' => '10M', 'stat_4_label' => 'Total project expenditure',
            ],
            'about.home-9' => [
                'eyebrow' => 'About Us',
                'heading' => 'Create Your Story in a place where dreams and reality merge',
                'image_primary' => '/theme-assets/img/about/about7-1.jpg',
                'image_secondary' => '/theme-assets/img/about/about7-2.jpg',
                'image_tertiary' => '/theme-assets/img/about/about7-3.jpg',
                'mission_text' => 'We specialize in residential and commercial property transactions, offering end-to-end assistance—from property valuation and marketing to negotiation, documentation, and closing support.',
                'vision_text' => 'At the heart of our agency is a commitment to honesty, availability, and tailored service for every buyer, seller and investor.',
            ],
            'about.home-10' => [
                'eyebrow' => 'About Us',
                'heading' => 'Delivering Exceptional Modern Homes That Blend Comfort, Style, and Advanced Living Features.',
                'description' => 'Our local real estate agency is built on a deep understanding of the neighborhoods, communities, and property trends that shape our area. With years of experience in the local market, we provide personalized guidance for buyers.',
                'image_primary' => '/theme-assets/img/about/about-8-left.jpg',
                'image_secondary' => '/theme-assets/img/about/about-8-right.jpg',
                'stat_1_value' => '1950+', 'stat_1_label' => 'Project handover',
                'stat_2_value' => '4M', 'stat_2_label' => 'Monthly visitors',
                'stat_3_value' => '850+', 'stat_3_label' => 'Property ready',
                'stat_4_value' => '98%', 'stat_4_label' => 'Happy customers',
            ],
            default => [],
        }),
        'fields' => $sharedFields,
    ];
}

return $sections;
