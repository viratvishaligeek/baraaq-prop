<?php

$heroDesigns = [
    'index' => ['label' => 'Hero — Home 1', 'view' => 'frontend.sections.hero.index'],
    'home-2' => ['label' => 'Hero — Home 2', 'view' => 'frontend.sections.hero.home-2'],
    'home-3' => ['label' => 'Hero — Home 3', 'view' => 'frontend.sections.hero.home-3'],
    'home-4' => ['label' => 'Hero — Home 4', 'view' => 'frontend.sections.hero.home-4'],
    'home-5' => ['label' => 'Hero — Home 5', 'view' => 'frontend.sections.hero.home-5'],
    'home-6' => ['label' => 'Hero — Home 6', 'view' => 'frontend.sections.hero.home-6'],
    'home-7' => ['label' => 'Hero — Home 7', 'view' => 'frontend.sections.hero.home-7'],
    'home-8' => ['label' => 'Hero — Home 8', 'view' => 'frontend.sections.hero.home-8'],
    'home-9' => ['label' => 'Hero — Home 9', 'view' => 'frontend.sections.hero.home-9'],
    'home-10' => ['label' => 'Hero — Home 10', 'view' => 'frontend.sections.hero.home-10'],
];

$heroFields = [
    'eyebrow' => ['label' => 'Eyebrow / small heading', 'type' => 'text', 'max' => 120],
    'heading' => ['label' => 'Main heading', 'type' => 'textarea', 'max' => 500],
    'description' => ['label' => 'Description', 'type' => 'richtext', 'max' => 1500],
    'primary_button_label' => ['label' => 'Primary button label', 'type' => 'text', 'max' => 80],
    'primary_button_url' => ['label' => 'Primary button URL', 'type' => 'url', 'max' => 2048],
    'secondary_button_label' => ['label' => 'Secondary button label', 'type' => 'text', 'max' => 80],
    'secondary_button_url' => ['label' => 'Secondary button URL', 'type' => 'url', 'max' => 2048],
    'background_image' => ['label' => 'Background image path or URL', 'type' => 'image', 'max' => 2048],
    'overlay_color' => ['label' => 'Overlay color', 'type' => 'color'],
    'overlay_opacity' => ['label' => 'Overlay opacity (%)', 'type' => 'number', 'min' => 0, 'max' => 100],
];

$home2HeroDefaults = [
    'heading_prefix' => 'Find Your',
    'heading_highlight' => 'Perfect Home',
    'heading_suffix' => 'For Spending Life',
    'hero_video_url' => '/theme-assets/img/hero/hero-2-video.mp4',
    'background_mask_image' => '/theme-assets/img/hero/hero-2-bg-mask.png',
    'logo_video_url' => 'https://www.youtube.com/watch?v=_sI_Ps7JSEk',
    'logo_text' => 'PILLAR REAL ESTATE HOME * REAL ESTATE',
    'scroll_label' => 'Scroll',
    'search_property_type_label' => 'Property Type',
    'search_property_type_placeholder' => 'Select Make',
    'search_location_label' => 'Location',
    'search_location_placeholder' => 'Find a location',
    'search_keyword_label' => 'Keyword',
    'search_keyword_placeholder' => 'Looking For?',
    'advanced_button_label' => 'Advanced',
    'search_button_label' => 'Search',
    'show_search_form' => true,
    'show_property_types' => true,
    'animate_content' => true,
    'show_mobile' => true,
    'show_tablet' => true,
    'show_desktop' => true,
    'social_facebook_url' => 'https://facebook.com/',
    'social_twitter_url' => 'https://twitter.com/',
    'social_instagram_url' => 'https://instagram.com/',
    'social_linkedin_url' => 'https://linkedin.com/',
    'search_type_1_label' => 'Apartment',
    'search_type_1_value' => 'Apartment',
    'search_type_2_label' => 'House',
    'search_type_2_value' => 'House',
    'search_type_3_label' => 'Villa',
    'search_type_3_value' => 'Villa',
    'search_type_4_label' => 'Office',
    'search_type_4_value' => 'Office',
    'quick_type_1_label' => 'Penthouse',
    'quick_type_1_icon' => '/theme-assets/img/icon/property-item-icon-2-1.svg',
    'quick_type_2_label' => 'Townhouse',
    'quick_type_2_icon' => '/theme-assets/img/icon/property-item-icon-2-2.svg',
    'quick_type_3_label' => 'Villa',
    'quick_type_3_icon' => '/theme-assets/img/icon/property-item-icon-2-3.svg',
    'quick_type_4_label' => 'Apartments',
    'quick_type_4_icon' => '/theme-assets/img/icon/property-item-icon-2-4.svg',
];

$home2HeroFields = [
    'heading_prefix' => ['label' => 'Heading prefix', 'type' => 'text', 'max' => 100],
    'heading_highlight' => ['label' => 'Highlighted heading text', 'type' => 'text', 'max' => 100],
    'heading_suffix' => ['label' => 'Heading suffix', 'type' => 'text', 'max' => 180],
    'hero_video_url' => ['label' => 'Background video path or URL', 'type' => 'text', 'max' => 2048],
    'background_mask_image' => ['label' => 'Hero mask image', 'type' => 'image', 'max' => 2048],
    'logo_video_url' => ['label' => 'Logo play video URL', 'type' => 'url', 'max' => 2048],
    'logo_text' => ['label' => 'Circular logo text', 'type' => 'text', 'max' => 180],
    'scroll_label' => ['label' => 'Scroll label', 'type' => 'text', 'max' => 40],
    'search_property_type_label' => ['label' => 'Property type label', 'type' => 'text', 'max' => 80],
    'search_property_type_placeholder' => ['label' => 'Property type placeholder', 'type' => 'text', 'max' => 80],
    'search_location_label' => ['label' => 'Location label', 'type' => 'text', 'max' => 80],
    'search_location_placeholder' => ['label' => 'Location placeholder', 'type' => 'text', 'max' => 100],
    'search_keyword_label' => ['label' => 'Keyword label', 'type' => 'text', 'max' => 80],
    'search_keyword_placeholder' => ['label' => 'Keyword placeholder', 'type' => 'text', 'max' => 100],
    'advanced_button_label' => ['label' => 'Advanced button label', 'type' => 'text', 'max' => 60],
    'search_button_label' => ['label' => 'Search button label', 'type' => 'text', 'max' => 60],
    'show_search_form' => ['label' => 'Show hero search form', 'type' => 'checkbox'],
    'show_property_types' => ['label' => 'Show quick property type strip', 'type' => 'checkbox'],
    'animate_content' => ['label' => 'Enable entrance animations', 'type' => 'checkbox'],
    'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
    'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
    'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
    'social_facebook_url' => ['label' => 'Facebook URL', 'type' => 'url', 'max' => 2048],
    'social_twitter_url' => ['label' => 'Twitter URL', 'type' => 'url', 'max' => 2048],
    'social_instagram_url' => ['label' => 'Instagram URL', 'type' => 'url', 'max' => 2048],
    'social_linkedin_url' => ['label' => 'LinkedIn URL', 'type' => 'url', 'max' => 2048],
];

for ($i = 1; $i <= 4; $i++) {
    $home2HeroFields['search_type_'.$i.'_label'] = ['label' => 'Search type '.$i.' label', 'type' => 'text', 'max' => 80];
    $home2HeroFields['search_type_'.$i.'_value'] = ['label' => 'Search type '.$i.' value', 'type' => 'text', 'max' => 80];
    $home2HeroFields['quick_type_'.$i.'_label'] = ['label' => 'Quick type '.$i.' label', 'type' => 'text', 'max' => 80];
    $home2HeroFields['quick_type_'.$i.'_icon'] = ['label' => 'Quick type '.$i.' icon', 'type' => 'image', 'max' => 2048];
}

$home3HeroDefaults = [
    'eyebrow' => 'Welcome To Pillar',
    'heading_prefix' => 'Find Your Perfect',
    'heading_highlight' => 'Home',
    'heading_suffix' => 'to Life Spend',
    'map_image' => '/theme-assets/img/hero/hero-3-right.jpg',
    'background_color' => '',
    'right_shape_image' => '/theme-assets/img/hero/hero-3-right-shape.png',
    'left_shape_image' => '/theme-assets/img/shape/hero-3-left-shape.png',
    'map_marker_image' => '/theme-assets/img/icon/map-marker.svg',
    'map_property_fallback_image' => '/theme-assets/img/popular/popular-1-3.jpg',
    'map_location_icon' => '/theme-assets/img/icon/popular-location.svg',
    'map_bed_icon' => '/theme-assets/img/icon/bed.svg',
    'map_bath_icon' => '/theme-assets/img/icon/bath.svg',
    'map_area_icon' => '/theme-assets/img/icon/sqft.svg',
    'counter_1_value' => 1950, 'counter_1_suffix' => '+', 'counter_1_label' => 'Project Handover',
    'counter_2_value' => 2, 'counter_2_suffix' => 'M +', 'counter_2_label' => 'Monthly Visitors',
    'counter_3_value' => 850, 'counter_3_suffix' => '+', 'counter_3_label' => 'Property Ready',
    'counter_4_value' => 98, 'counter_4_suffix' => '%', 'counter_4_label' => 'Happy Customers',
    'buy_tab_label' => 'Buy', 'sell_tab_label' => 'Sell', 'rent_tab_label' => 'Rent',
    'keyword_label' => 'Keyword', 'keyword_placeholder' => 'Looking For?',
    'location_label' => 'Location', 'location_placeholder' => 'Find a location',
    'pricing_label' => 'Pricing', 'pricing_placeholder' => '$0 - $8,99500',
    'advanced_label' => 'Advanced', 'search_button_label' => 'Search',
    'advanced_type_label' => 'Property Type', 'advanced_bedrooms_label' => 'Max of Bedrooms',
    'advanced_location_label' => 'Location',
    'map_card_limit' => 4,
    'show_map' => true,
    'show_counters' => true,
    'show_search_form' => true,
    'animate_content' => true,
    'show_mobile' => true, 'show_tablet' => true, 'show_desktop' => true,
];
$home3HeroFields = [
    'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
    'heading_prefix' => ['label' => 'Heading prefix', 'type' => 'text', 'max' => 100],
    'heading_highlight' => ['label' => 'Highlighted heading', 'type' => 'text', 'max' => 100],
    'heading_suffix' => ['label' => 'Heading suffix', 'type' => 'text', 'max' => 150],
    'map_image' => ['label' => 'Map-side hero image', 'type' => 'image', 'max' => 2048],
    'background_color' => ['label' => 'Hero background color', 'type' => 'color'],
    'right_shape_image' => ['label' => 'Top-right decorative shape', 'type' => 'image', 'max' => 2048],
    'left_shape_image' => ['label' => 'Bottom-left decorative shape', 'type' => 'image', 'max' => 2048],
    'map_marker_image' => ['label' => 'Map marker icon', 'type' => 'image', 'max' => 2048],
    'map_property_fallback_image' => ['label' => 'Map property fallback image', 'type' => 'image', 'max' => 2048],
    'map_location_icon' => ['label' => 'Map property location icon', 'type' => 'image', 'max' => 2048],
    'map_bed_icon' => ['label' => 'Map property bedroom icon', 'type' => 'image', 'max' => 2048],
    'map_bath_icon' => ['label' => 'Map property bathroom icon', 'type' => 'image', 'max' => 2048],
    'map_area_icon' => ['label' => 'Map property area icon', 'type' => 'image', 'max' => 2048],
    'buy_tab_label' => ['label' => 'Buy tab label', 'type' => 'text', 'max' => 40],
    'sell_tab_label' => ['label' => 'Sell tab label', 'type' => 'text', 'max' => 40],
    'rent_tab_label' => ['label' => 'Rent tab label', 'type' => 'text', 'max' => 40],
    'keyword_label' => ['label' => 'Keyword label', 'type' => 'text', 'max' => 60],
    'keyword_placeholder' => ['label' => 'Keyword placeholder', 'type' => 'text', 'max' => 100],
    'location_label' => ['label' => 'Location label', 'type' => 'text', 'max' => 60],
    'location_placeholder' => ['label' => 'Location placeholder', 'type' => 'text', 'max' => 100],
    'pricing_label' => ['label' => 'Pricing label', 'type' => 'text', 'max' => 60],
    'pricing_placeholder' => ['label' => 'Pricing placeholder', 'type' => 'text', 'max' => 100],
    'advanced_label' => ['label' => 'Advanced search label', 'type' => 'text', 'max' => 60],
    'search_button_label' => ['label' => 'Search button label', 'type' => 'text', 'max' => 60],
    'advanced_type_label' => ['label' => 'Advanced property type label', 'type' => 'text', 'max' => 80],
    'advanced_bedrooms_label' => ['label' => 'Advanced bedrooms label', 'type' => 'text', 'max' => 80],
    'advanced_location_label' => ['label' => 'Advanced location label', 'type' => 'text', 'max' => 80],
    'map_card_limit' => ['label' => 'Map property cards', 'type' => 'number', 'min' => 1, 'max' => 4],
    'show_map' => ['label' => 'Show property map', 'type' => 'checkbox'],
    'show_counters' => ['label' => 'Show hero counters', 'type' => 'checkbox'],
    'show_search_form' => ['label' => 'Show property search', 'type' => 'checkbox'],
    'animate_content' => ['label' => 'Enable hero entrance animations', 'type' => 'checkbox'],
    'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
    'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
    'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
];
for ($i = 1; $i <= 4; $i++) {
    $home3HeroFields['counter_'.$i.'_value'] = ['label' => 'Counter '.$i.' value', 'type' => 'number', 'min' => 0, 'max' => 9999999];
    $home3HeroFields['counter_'.$i.'_suffix'] = ['label' => 'Counter '.$i.' suffix', 'type' => 'text', 'max' => 12];
    $home3HeroFields['counter_'.$i.'_label'] = ['label' => 'Counter '.$i.' label', 'type' => 'text', 'max' => 80];
}

$sections = [];

$heroVariantFields = [
    'home-4' => [
        'logo_video_url' => ['label' => 'Logo video URL', 'type' => 'url', 'max' => 2048],
        'logo_text' => ['label' => 'Circular logo text', 'type' => 'text', 'max' => 180],
        'avatar_image' => ['label' => 'Review avatar image', 'type' => 'image', 'max' => 2048],
        'rating_text' => ['label' => 'Review rating text', 'type' => 'text', 'max' => 120],
        'slide_1_background_image' => ['label' => 'Slide 1 background', 'type' => 'image', 'max' => 2048],
        'slide_2_background_image' => ['label' => 'Slide 2 background', 'type' => 'image', 'max' => 2048],
        'slide_3_background_image' => ['label' => 'Slide 3 background', 'type' => 'image', 'max' => 2048],
        'slide_4_background_image' => ['label' => 'Slide 4 background', 'type' => 'image', 'max' => 2048],
    ],
    'home-5' => [
        'logo_text' => ['label' => 'Circular logo text', 'type' => 'text', 'max' => 180],
        'avatar_image' => ['label' => 'Review avatar image', 'type' => 'image', 'max' => 2048],
        'rating_text' => ['label' => 'Review rating text', 'type' => 'text', 'max' => 120],
        'counter_1_value' => ['label' => 'Counter 1 value', 'type' => 'number', 'min' => 0, 'max' => 999999],
        'counter_1_suffix' => ['label' => 'Counter 1 suffix', 'type' => 'text', 'max' => 12],
        'counter_1_label' => ['label' => 'Counter 1 label', 'type' => 'text', 'max' => 80],
        'counter_2_prefix' => ['label' => 'Counter 2 prefix', 'type' => 'text', 'max' => 12],
        'counter_2_value' => ['label' => 'Counter 2 value', 'type' => 'number', 'min' => 0, 'max' => 999999],
        'counter_2_suffix' => ['label' => 'Counter 2 suffix', 'type' => 'text', 'max' => 12],
        'counter_2_label' => ['label' => 'Counter 2 label', 'type' => 'text', 'max' => 80],
        'property_address' => ['label' => 'Featured property address', 'type' => 'text', 'max' => 180],
        'property_title' => ['label' => 'Featured property title', 'type' => 'text', 'max' => 180],
        'bedrooms' => ['label' => 'Bedrooms', 'type' => 'number', 'min' => 0, 'max' => 50],
        'bathrooms' => ['label' => 'Bathrooms', 'type' => 'number', 'min' => 0, 'max' => 50],
        'area_value' => ['label' => 'Area value', 'type' => 'number', 'min' => 0, 'max' => 999999],
        'area_unit' => ['label' => 'Area unit', 'type' => 'text', 'max' => 20],
        'parking_label' => ['label' => 'Parking label', 'type' => 'text', 'max' => 60],
        'property_price' => ['label' => 'Property price', 'type' => 'text', 'max' => 80],
        'agent_name' => ['label' => 'Agent name', 'type' => 'text', 'max' => 120],
        'agent_designation' => ['label' => 'Agent designation', 'type' => 'text', 'max' => 80],
        'listing_badge' => ['label' => 'Listing badge', 'type' => 'text', 'max' => 40],
        'tour_button_label' => ['label' => 'Tour button label', 'type' => 'text', 'max' => 80],
        'tour_button_url' => ['label' => 'Tour button URL', 'type' => 'url', 'max' => 2048],
        'slide_1_background_image' => ['label' => 'Slide 1 background', 'type' => 'image', 'max' => 2048],
        'slide_2_background_image' => ['label' => 'Slide 2 background', 'type' => 'image', 'max' => 2048],
        'slide_3_background_image' => ['label' => 'Slide 3 background', 'type' => 'image', 'max' => 2048],
        'slide_4_background_image' => ['label' => 'Slide 4 background', 'type' => 'image', 'max' => 2048],
    ],
    'home-6' => [
        'slide_2_heading' => ['label' => 'Slide 2 heading', 'type' => 'text', 'max' => 180],
        'slide_3_heading' => ['label' => 'Slide 3 heading', 'type' => 'text', 'max' => 180],
        'slide_1_background_image' => ['label' => 'Slide 1 background', 'type' => 'image', 'max' => 2048],
        'slide_2_background_image' => ['label' => 'Slide 2 background', 'type' => 'image', 'max' => 2048],
        'slide_3_background_image' => ['label' => 'Slide 3 background', 'type' => 'image', 'max' => 2048],
    ],
    'home-7' => [
        'logo_video_url' => ['label' => 'Logo video URL', 'type' => 'url', 'max' => 2048],
        'logo_text' => ['label' => 'Circular logo text', 'type' => 'text', 'max' => 180],
        'social_facebook_url' => ['label' => 'Facebook URL', 'type' => 'url', 'max' => 2048],
        'social_twitter_url' => ['label' => 'Twitter URL', 'type' => 'url', 'max' => 2048],
        'social_instagram_url' => ['label' => 'Instagram URL', 'type' => 'url', 'max' => 2048],
        'social_linkedin_url' => ['label' => 'LinkedIn URL', 'type' => 'url', 'max' => 2048],
        'slide_2_heading' => ['label' => 'Slide 2 heading', 'type' => 'text', 'max' => 240],
        'slide_3_heading' => ['label' => 'Slide 3 heading', 'type' => 'text', 'max' => 240],
        'slide_1_background_image' => ['label' => 'Slide 1 background', 'type' => 'image', 'max' => 2048],
        'slide_2_background_image' => ['label' => 'Slide 2 background', 'type' => 'image', 'max' => 2048],
        'slide_3_background_image' => ['label' => 'Slide 3 background', 'type' => 'image', 'max' => 2048],
    ],
    'home-8' => [
        'heading_prefix' => ['label' => 'First heading line', 'type' => 'text', 'max' => 120],
        'heading_suffix' => ['label' => 'Second heading line', 'type' => 'text', 'max' => 120],
        'foreground_image' => ['label' => 'Foreground image', 'type' => 'image', 'max' => 2048],
    ],
    'home-9' => [
        'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
        'foreground_image' => ['label' => 'Foreground image', 'type' => 'image', 'max' => 2048],
    ],
    'home-10' => [
        'logo_video_url' => ['label' => 'Logo video URL', 'type' => 'url', 'max' => 2048],
        'logo_text' => ['label' => 'Circular logo text', 'type' => 'text', 'max' => 180],
        'rating_icon' => ['label' => 'Review rating icon', 'type' => 'image', 'max' => 2048],
        'avatar_image' => ['label' => 'Review avatar image', 'type' => 'image', 'max' => 2048],
        'trust_label' => ['label' => 'Trust platform label', 'type' => 'text', 'max' => 80],
        'client_count' => ['label' => 'Client count', 'type' => 'number', 'min' => 0, 'max' => 999999],
        'client_suffix' => ['label' => 'Client count suffix', 'type' => 'text', 'max' => 12],
        'client_label' => ['label' => 'Client count label', 'type' => 'text', 'max' => 80],
        'image_1' => ['label' => 'Hero image 1', 'type' => 'image', 'max' => 2048],
        'image_2' => ['label' => 'Hero image 2', 'type' => 'image', 'max' => 2048],
        'image_3' => ['label' => 'Hero image 3', 'type' => 'image', 'max' => 2048],
    ],
];

$heroVariantDefaults = [
    'home-4' => [
        'eyebrow' => 'Welcome To Pillar',
        'heading' => 'Find your Perfect Luxury Home',
        'primary_button_label' => 'Explore Properties',
        'primary_button_url' => '/properties',
        'logo_video_url' => 'https://www.youtube.com/watch?v=_sI_Ps7JSEk',
        'logo_text' => 'PILLAR REAL ESTATE HOME * REAL ESTATE',
        'avatar_image' => '/theme-assets/img/hero/heror-4-avator-group.png',
        'rating_text' => '5.0 Rating on The Reviews',
        'slide_1_background_image' => '/theme-assets/img/hero/hero_bg_4_1.jpg',
        'slide_2_background_image' => '/theme-assets/img/hero/hero_bg_4_2.jpg',
        'slide_3_background_image' => '/theme-assets/img/hero/hero_bg_4_3.jpg',
        'slide_4_background_image' => '/theme-assets/img/hero/hero_bg_4_4.jpg',
    ],
    'home-5' => [
        'heading' => 'Discover Your Dream Living Space',
        'description' => 'Explore top listings and let our expert agents guide you every step of the way to your new home.',
        'primary_button_label' => 'Explore Properties',
        'primary_button_url' => '/properties',
        'secondary_button_label' => 'Request A Visit',
        'secondary_button_url' => '/contact',
        'logo_text' => 'LUXURY HOME ** REAL ESTATE SOLUTION **',
        'avatar_image' => '/theme-assets/img/hero/heror-4-avator-group.png',
        'rating_text' => '5.0 Rating on The Reviews',
        'counter_1_value' => 1250, 'counter_1_suffix' => '+', 'counter_1_label' => 'Project Completed',
        'counter_2_prefix' => '
    $key = 'hero.'.$design;

    $sections[$key] = [
        'key' => $key,
        'label' => $definition['label'],
        'type' => 'hero',
        'source_design' => $design,
        'view' => $definition['view'],
        'defaults' => $design === 'home-2' ? $home2HeroDefaults : ($design === 'home-3' ? $home3HeroDefaults : ($heroVariantDefaults[$design] ?? [])),
        'fields' => $design === 'home-2' ? $home2HeroFields : ($design === 'home-3' ? $home3HeroFields : array_merge($heroFields, $heroVariantFields[$design] ?? [])),
    ];
}

$sections['properties.index'] = [
    'key' => 'properties.index',
    'label' => 'Featured properties — Home 1',
    'type' => 'property-list',
    'source_design' => 'index',
    'view' => 'frontend.sections.properties.index',
    'defaults' => [
        'eyebrow' => 'Popular Properties',
        'heading' => 'Best Properties Sale',
        'items_limit' => 4,
        'listing_filter' => 'featured',
        'button_label' => 'View All Properties',
        'button_url' => '/properties',
    ],
    'fields' => [
        'eyebrow' => ['label' => 'Section eyebrow', 'type' => 'text', 'max' => 120],
        'heading' => ['label' => 'Section heading', 'type' => 'text', 'max' => 180],
        'description' => ['label' => 'Introductory text', 'type' => 'textarea', 'max' => 1000],
        'items_limit' => ['label' => 'Number of properties', 'type' => 'number', 'min' => 1, 'max' => 6],
        'listing_filter' => ['label' => 'Listing filter', 'type' => 'select', 'options' => ['all' => 'All listings', 'sale' => 'For sale', 'rent' => 'For rent', 'featured' => 'Featured only']],
        'button_label' => ['label' => 'View-all button label', 'type' => 'text', 'max' => 80],
        'button_url' => ['label' => 'View-all button URL', 'type' => 'url', 'max' => 2048],
    ],
];

$sections['team.index'] = [
    'key' => 'team.index',
    'label' => 'Team members — Home 1',
    'type' => 'team',
    'source_design' => 'index',
    'view' => 'frontend.sections.team.index',
    'defaults' => [],
    'fields' => [
        'eyebrow' => ['label' => 'Section eyebrow', 'type' => 'text', 'max' => 120],
        'heading' => ['label' => 'Section heading', 'type' => 'text', 'max' => 180],
        'description' => ['label' => 'Introductory text', 'type' => 'textarea', 'max' => 1000],
        'items_limit' => ['label' => 'Number of team members', 'type' => 'number', 'min' => 1, 'max' => 4],
        'show_email' => ['label' => 'Show email address', 'type' => 'checkbox'],
    ],
];

$sections['blog.index'] = [
    'key' => 'blog.index',
    'label' => 'Latest articles — Home 1',
    'type' => 'blog',
    'source_design' => 'index',
    'view' => 'frontend.sections.blog.index',
    'defaults' => [],
    'fields' => [
        'eyebrow' => ['label' => 'Section eyebrow', 'type' => 'text', 'max' => 120],
        'heading' => ['label' => 'Section heading', 'type' => 'text', 'max' => 180],
        'description' => ['label' => 'Introductory text', 'type' => 'textarea', 'max' => 1000],
        'items_limit' => ['label' => 'Number of articles', 'type' => 'number', 'min' => 1, 'max' => 3],
        'button_label' => ['label' => 'View-all button label', 'type' => 'text', 'max' => 80],
        'button_url' => ['label' => 'View-all button URL', 'type' => 'url', 'max' => 2048],
        'show_author' => ['label' => 'Show author', 'type' => 'checkbox'],
        'show_date' => ['label' => 'Show publish date', 'type' => 'checkbox'],
    ],
];

$sections = array_merge($sections, require __DIR__.'/cms-sections/categories.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/about.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/about-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/services-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/property-values.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/property-values-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/neighborhoods.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/team.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/team-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/team-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/gallery-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/why.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/why-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/why-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/properties.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/properties-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/properties-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/faq.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/faq-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/faq-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/download-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/featured-gallery-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/gallery-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/gallery-home-6.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/city-explore-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/process-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/testimonials-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/enquiry-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/marquee-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/blog-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/blog-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/brands-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/brands-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/contact-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/statistics-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/cta-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/download.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/marquee.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/testimonials.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/testimonials-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/featured-properties.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/blog.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/search.php');

return [
    'sections' => $sections,
];
, 'counter_2_value' => 25, 'counter_2_suffix' => 'M', 'counter_2_label' => 'Monthly Visitors',
        'property_address' => 'Property address text', 'property_title' => 'Luxurious Modern Home',
        'bedrooms' => 4, 'bathrooms' => 2, 'area_value' => 1500, 'area_unit' => 'sqft',
        'parking_label' => 'Parking Slot', 'property_price' => '$895,000.00',
        'agent_name' => 'Walter Deckow', 'agent_designation' => 'Sale Agent',
        'listing_badge' => 'For Sale', 'tour_button_label' => 'Request A Tour',
        'tour_button_url' => '/contact',
        'slide_1_background_image' => '/theme-assets/img/hero/hero_bg_5_1.jpg',
        'slide_2_background_image' => '/theme-assets/img/hero/hero_bg_5_2.jpg',
        'slide_3_background_image' => '/theme-assets/img/hero/hero_bg_5_3.jpg',
        'slide_4_background_image' => '/theme-assets/img/hero/hero_bg_5_4.jpg',
    ],
    'home-6' => [
        'eyebrow' => 'Welcome To Pillar',
        'heading' => 'Exclusive Suites',
        'slide_2_heading' => 'Premium Suites',
        'slide_3_heading' => 'Dream Suites',
        'description' => 'Premium Living. Affordable Prices',
        'primary_button_label' => 'View All Service',
        'primary_button_url' => '/services',
        'slide_1_background_image' => '/theme-assets/img/hero/hero_bg_6_1.jpg',
        'slide_2_background_image' => '/theme-assets/img/hero/hero_bg_6_2.jpg',
        'slide_3_background_image' => '/theme-assets/img/hero/hero_bg_6_3.jpg',
    ],
    'home-7' => [
        'heading' => 'Discover Homes Tailored to Your Lifestyle.',
        'description' => 'Explore top listings and let our expert agents guide you every step of the way to your new home.',
        'primary_button_label' => 'Explore Properties',
        'primary_button_url' => '/properties',
        'secondary_button_label' => 'Request A Visit',
        'secondary_button_url' => '/contact',
        'logo_video_url' => 'https://www.youtube.com/watch?v=_sI_Ps7JSEk',
        'logo_text' => 'PILLAR REAL ESTATE HOME * REAL ESTATE',
        'social_facebook_url' => 'https://facebook.com/',
        'social_twitter_url' => 'https://twitter.com/',
        'social_instagram_url' => 'https://instagram.com/',
        'social_linkedin_url' => 'https://linkedin.com/',
        'slide_2_heading' => 'Browse Homes Tailored to Elevate Your Living.',
        'slide_3_heading' => 'Unlock Homes Created to Match Your Daily Living.',
        'slide_1_background_image' => '/theme-assets/img/hero/hero_bg_7_1.jpg',
        'slide_2_background_image' => '/theme-assets/img/hero/hero_bg_7_2.jpg',
        'slide_3_background_image' => '/theme-assets/img/hero/hero_bg_7_3.jpg',
    ],
    'home-8' => [
        'heading' => 'Find Your Local Home.',
        'heading_prefix' => 'Find Your',
        'heading_suffix' => 'Local Home.',
        'description' => 'Explore top listings and let our expert agents guide you every step of the way to your new home.',
        'primary_button_label' => 'Explore Properties',
        'primary_button_url' => '/properties',
        'secondary_button_label' => 'Request A Visit',
        'secondary_button_url' => '/contact',
        'background_image' => '/theme-assets/img/hero/hero-8-bg.png',
        'foreground_image' => '/theme-assets/img/hero/hero-8-left.jpg',
    ],
    'home-9' => [
        'eyebrow' => 'Don’t Missed Out',
        'heading' => 'Discover Most Suitable Property',
        'description' => 'Explore top listings and let our expert agents guide you every step of the way to your new home.',
        'primary_button_label' => 'Explore Properties',
        'primary_button_url' => '/properties',
        'secondary_button_label' => 'Request A Visit',
        'secondary_button_url' => '/contact',
        'background_image' => '/theme-assets/img/hero/hero-9-bg.png',
        'foreground_image' => '/theme-assets/img/hero/hero-9-right.png',
    ],
    'home-10' => [
        'heading' => 'Inspired Living for the Modern World',
        'description' => 'Explore top listings and let our expert agents guide you every step of the way to your new home.',
        'primary_button_label' => 'Explore Properties',
        'primary_button_url' => '/properties',
        'secondary_button_label' => 'Request A Visit',
        'secondary_button_url' => '/contact',
        'logo_video_url' => 'https://www.youtube.com/watch?v=_sI_Ps7JSEk',
        'logo_text' => 'PILLAR REAL ESTATE HOME * REAL ESTATE',
        'rating_icon' => '/theme-assets/img/icon/star-icon.svg',
        'avatar_image' => '/theme-assets/img/hero/heror-1-avator-group.png',
        'trust_label' => 'Trustipilot',
        'client_count' => 19, 'client_suffix' => 'k+', 'client_label' => 'clients',
        'image_1' => '/theme-assets/img/hero/hero-10-1.jpg',
        'image_2' => '/theme-assets/img/hero/hero-10-2.jpg',
        'image_3' => '/theme-assets/img/hero/hero-10-3.jpg',
    ],
];

foreach ($heroDesigns as $design => $definition) {
    $key = 'hero.'.$design;

    $sections[$key] = [
        'key' => $key,
        'label' => $definition['label'],
        'type' => 'hero',
        'source_design' => $design,
        'view' => $definition['view'],
        'defaults' => $design === 'home-2' ? $home2HeroDefaults : ($design === 'home-3' ? $home3HeroDefaults : []),
        'fields' => $design === 'home-2' ? $home2HeroFields : ($design === 'home-3' ? $home3HeroFields : $heroFields),
    ];
}

$sections['properties.index'] = [
    'key' => 'properties.index',
    'label' => 'Featured properties — Home 1',
    'type' => 'property-list',
    'source_design' => 'index',
    'view' => 'frontend.sections.properties.index',
    'defaults' => [
        'eyebrow' => 'Popular Properties',
        'heading' => 'Best Properties Sale',
        'items_limit' => 4,
        'listing_filter' => 'featured',
        'button_label' => 'View All Properties',
        'button_url' => '/properties',
    ],
    'fields' => [
        'eyebrow' => ['label' => 'Section eyebrow', 'type' => 'text', 'max' => 120],
        'heading' => ['label' => 'Section heading', 'type' => 'text', 'max' => 180],
        'description' => ['label' => 'Introductory text', 'type' => 'textarea', 'max' => 1000],
        'items_limit' => ['label' => 'Number of properties', 'type' => 'number', 'min' => 1, 'max' => 6],
        'listing_filter' => ['label' => 'Listing filter', 'type' => 'select', 'options' => ['all' => 'All listings', 'sale' => 'For sale', 'rent' => 'For rent', 'featured' => 'Featured only']],
        'button_label' => ['label' => 'View-all button label', 'type' => 'text', 'max' => 80],
        'button_url' => ['label' => 'View-all button URL', 'type' => 'url', 'max' => 2048],
    ],
];

$sections['team.index'] = [
    'key' => 'team.index',
    'label' => 'Team members — Home 1',
    'type' => 'team',
    'source_design' => 'index',
    'view' => 'frontend.sections.team.index',
    'defaults' => [],
    'fields' => [
        'eyebrow' => ['label' => 'Section eyebrow', 'type' => 'text', 'max' => 120],
        'heading' => ['label' => 'Section heading', 'type' => 'text', 'max' => 180],
        'description' => ['label' => 'Introductory text', 'type' => 'textarea', 'max' => 1000],
        'items_limit' => ['label' => 'Number of team members', 'type' => 'number', 'min' => 1, 'max' => 4],
        'show_email' => ['label' => 'Show email address', 'type' => 'checkbox'],
    ],
];

$sections['blog.index'] = [
    'key' => 'blog.index',
    'label' => 'Latest articles — Home 1',
    'type' => 'blog',
    'source_design' => 'index',
    'view' => 'frontend.sections.blog.index',
    'defaults' => [],
    'fields' => [
        'eyebrow' => ['label' => 'Section eyebrow', 'type' => 'text', 'max' => 120],
        'heading' => ['label' => 'Section heading', 'type' => 'text', 'max' => 180],
        'description' => ['label' => 'Introductory text', 'type' => 'textarea', 'max' => 1000],
        'items_limit' => ['label' => 'Number of articles', 'type' => 'number', 'min' => 1, 'max' => 3],
        'button_label' => ['label' => 'View-all button label', 'type' => 'text', 'max' => 80],
        'button_url' => ['label' => 'View-all button URL', 'type' => 'url', 'max' => 2048],
        'show_author' => ['label' => 'Show author', 'type' => 'checkbox'],
        'show_date' => ['label' => 'Show publish date', 'type' => 'checkbox'],
    ],
];

$sections = array_merge($sections, require __DIR__.'/cms-sections/categories.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/about.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/about-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/services-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/property-values.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/property-values-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/neighborhoods.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/team.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/team-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/team-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/gallery-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/why.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/why-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/why-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/properties.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/properties-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/properties-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/faq.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/faq-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/faq-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/download-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/featured-gallery-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/gallery-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/gallery-home-6.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/city-explore-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/process-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/testimonials-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/enquiry-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/marquee-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/blog-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/blog-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/brands-home-3.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/brands-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/contact-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/statistics-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/cta-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/download.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/marquee.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/testimonials.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/testimonials-piller.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/featured-properties.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/blog.php');
$sections = array_merge($sections, require __DIR__.'/cms-sections/search.php');

return [
    'sections' => $sections,
];
