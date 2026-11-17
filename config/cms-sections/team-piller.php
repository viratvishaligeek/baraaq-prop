<?php

$fields = [
    'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
    'heading' => ['label' => 'Heading', 'type' => 'text', 'max' => 180],
    'items_limit' => ['label' => 'Maximum team members', 'type' => 'number', 'min' => 1, 'max' => 4],
    'side_image' => ['label' => 'Large left-side team image', 'type' => 'image', 'max' => 2048],
    'background_shape' => ['label' => 'Section background shape', 'type' => 'image', 'max' => 2048],
    'card_background' => ['label' => 'Team card background', 'type' => 'image', 'max' => 2048],
    'show_email' => ['label' => 'Show email action', 'type' => 'checkbox'],
    'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
    'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
    'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
];

return [
    'team.home-10' => [
        'key' => 'team.home-10',
        'label' => 'Team — Modern asymmetric team composition (Home 10)',
        'type' => 'team-slider',
        'source_design' => 'home-10',
        'view' => 'frontend.sections.team.home-10',
        'defaults' => [
            'eyebrow' => 'Team Members',
            'heading' => 'Meet Our Pillar Agents',
            'items_limit' => 4,
            'side_image' => '/theme-assets/img/team/team-6-left.jpg',
            'background_shape' => '/theme-assets/img/bg/team6-bg-shape.png',
            'card_background' => '/theme-assets/img/shape/team-2-bg-shape.png',
            'show_email' => true,
            'show_mobile' => true,
            'show_tablet' => true,
            'show_desktop' => true,
        ],
        'fields' => $fields,
    ],
];
