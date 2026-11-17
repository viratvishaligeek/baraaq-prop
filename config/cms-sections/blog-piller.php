<?php

return [
    'blog.home-6' => [
        'key' => 'blog.home-6',
        'label' => 'Blog — Modern editorial insights slider (Home 6)',
        'type' => 'blog-slider',
        'source_design' => 'home-6',
        'view' => 'frontend.sections.blog.home-6',
        'defaults' => [
            'eyebrow' => 'Insights & Updates',
            'heading' => 'Our Guide to Smarter Living Modern Property Insights',
            'items_limit' => 6,
            'button_label' => 'View All Posts',
            'button_url' => '/blog',
            'show_date' => true,
            'show_mobile' => true,
            'show_tablet' => true,
            'show_desktop' => true,
        ],
        'fields' => [
            'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
            'heading' => ['label' => 'Heading', 'type' => 'textarea', 'max' => 240],
            'items_limit' => ['label' => 'Maximum posts', 'type' => 'number', 'min' => 1, 'max' => 12],
            'button_label' => ['label' => 'View-all button label', 'type' => 'text', 'max' => 80],
            'button_url' => ['label' => 'View-all button URL', 'type' => 'url', 'max' => 2048],
            'show_date' => ['label' => 'Show publish date', 'type' => 'checkbox'],
            'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
            'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
            'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
        ],
    ],
];
