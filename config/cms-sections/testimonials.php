<?php

return [
    'testimonials.home-2' => [
        'key' => 'testimonials.home-2',
        'label' => 'Published testimonials — Home 2',
        'type' => 'testimonial-slider',
        'source_design' => 'home-2',
        'view' => 'frontend.sections.testimonials.home-2',
        'defaults' => [
            'eyebrow' => 'Testimonials',
            'heading' => 'What Our Clients Say',
            'quote_icon' => '/theme-assets/img/icon/testi-2quote.svg',
            'loop' => true,
            'vertical_spacing' => 'space',
            'show_trust_summary' => true,
            'items_limit' => 4,
            'show_mobile' => true,
            'show_tablet' => true,
            'show_desktop' => true,
        ],
        'fields' => [
            'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
            'heading' => ['label' => 'Heading', 'type' => 'text', 'max' => 180],
            'quote_icon' => ['label' => 'Quote icon', 'type' => 'image', 'max' => 2048],
            'loop' => ['label' => 'Loop testimonial slider', 'type' => 'checkbox'],
            'vertical_spacing' => ['label' => 'Section spacing', 'type' => 'select', 'options' => ['space' => 'Theme spacing', 'space-top' => 'Top only', 'space-bottom' => 'Bottom only', 'py-0' => 'No vertical spacing']],
            'show_trust_summary' => ['label' => 'Show rating summary derived from published testimonials', 'type' => 'checkbox'],
            'items_limit' => ['label' => 'Visible testimonials', 'type' => 'number', 'min' => 1, 'max' => 12],
            'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
            'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
            'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
        ],
    ],
];
