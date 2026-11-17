<?php

$fields = [
    'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
    'heading' => ['label' => 'Heading', 'type' => 'text', 'max' => 200],
    'description' => ['label' => 'Description', 'type' => 'textarea', 'max' => 1200],
    'items_limit' => ['label' => 'Maximum testimonials', 'type' => 'number', 'min' => 1, 'max' => 24],
    'button_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 80],
    'button_url' => ['label' => 'Button URL', 'type' => 'url', 'max' => 2048],
    'quote_icon' => ['label' => 'Quote icon image', 'type' => 'image', 'max' => 2048],
    'rating_shape_image' => ['label' => 'Rating decorative shape', 'type' => 'image', 'max' => 2048],
    'show_rating' => ['label' => 'Show ratings', 'type' => 'checkbox'],
    'show_company' => ['label' => 'Show company', 'type' => 'checkbox'],
    'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
    'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
    'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
];
$defaults = [
    'eyebrow' => 'Testimonials',
    'heading' => 'What Our Clients Say',
    'description' => 'Real experiences from clients who found the right property with our team.',
    'items_limit' => 8,
    'button_label' => 'Explore All Reviews',
    'button_url' => '/contact',
    'quote_icon' => '/theme-assets/img/icon/quote.svg',
    'rating_shape_image' => '/theme-assets/img/shape/download-circle.png',
    'show_rating' => true,
    'show_company' => true,
    'show_mobile' => true, 'show_tablet' => true, 'show_desktop' => true,
];

$designs = [
    'testimonials.index' => ['label' => 'Testimonials — Classic centered slider (Home 1)', 'design' => 'index', 'view' => 'frontend.sections.testimonials.index'],
    'testimonials.home-4' => ['label' => 'Testimonials — Editorial split cards (Home 4)', 'design' => 'home-4', 'view' => 'frontend.sections.testimonials.home-4'],
    'testimonials.home-10' => ['label' => 'Testimonials — Modern card grid (Home 10)', 'design' => 'home-10', 'view' => 'frontend.sections.testimonials.home-10'],
];

$sections = [];
foreach ($designs as $key => $design) {
    $sections[$key] = [
        'key' => $key,
        'label' => $design['label'],
        'type' => 'testimonial-slider',
        'source_design' => $design['design'],
        'view' => $design['view'],
        'defaults' => $defaults,
        'fields' => $fields,
    ];
}
return $sections;
