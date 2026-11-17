<?php

$fields = [
    'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
    'heading' => ['label' => 'Heading', 'type' => 'textarea', 'max' => 240],
    'description' => ['label' => 'Description', 'type' => 'textarea', 'max' => 1200],
    'items_limit' => ['label' => 'Maximum services', 'type' => 'number', 'min' => 1, 'max' => 12],
    'columns' => ['label' => 'Columns', 'type' => 'select', 'options' => ['2' => '2 columns', '3' => '3 columns', '4' => '4 columns']],
    'button_label' => ['label' => 'Section button label', 'type' => 'text', 'max' => 80],
    'button_url' => ['label' => 'Section button URL', 'type' => 'url', 'max' => 2048],
    'card_link_label' => ['label' => 'Service card link label', 'type' => 'text', 'max' => 80],
    'vertical_spacing' => ['label' => 'Vertical spacing', 'type' => 'select', 'options' => ['space' => 'Large', 'space-top' => 'Top only', 'space-bottom' => 'Bottom only', 'space-sm' => 'Compact']],
    'background_color' => ['label' => 'Background color', 'type' => 'color'],
    'background_image' => ['label' => 'Background image', 'type' => 'image', 'max' => 2048],
    'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
    'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
    'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
];
$defaults = [
    'eyebrow' => 'Our Services',
    'heading' => 'Expert guidance at every step of your property journey',
    'description' => 'From the first conversation to the final handover, our team makes every step simpler.',
    'items_limit' => 8,
    'columns' => '3',
    'button_label' => 'Explore All Services',
    'button_url' => '/services',
    'vertical_spacing' => 'space',
    'background_color' => '',
    'show_mobile' => true, 'show_tablet' => true, 'show_desktop' => true,
];

$designs = [
    'services.home-4' => ['label' => 'Services — Icon cards (Home 4)', 'design' => 'home-4', 'view' => 'frontend.sections.services.home-4'],
    'services.home-6' => ['label' => 'Services — Editorial feature grid (Home 6)', 'design' => 'home-6', 'view' => 'frontend.sections.services.home-6'],
    'services.home-9' => ['label' => 'Services — Four-column service cards (Home 9)', 'design' => 'home-9', 'view' => 'frontend.sections.services.home-9'],
    'services.home-10' => ['label' => 'Services — Modern asymmetric grid (Home 10)', 'design' => 'home-10', 'view' => 'frontend.sections.services.home-10'],
];

$sections = [];
foreach ($designs as $key => $design) {
    $sections[$key] = [
        'key' => $key,
        'label' => $design['label'],
        'type' => 'service-list',
        'source_design' => $design['design'],
        'view' => $design['view'],
        'defaults' => $defaults,
        'fields' => $fields,
    ];
}
return $sections;
