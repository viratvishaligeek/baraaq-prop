<?php
$fields = [
    'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 100],
    'heading' => ['label' => 'Heading', 'type' => 'text', 'max' => 200],
    'description' => ['label' => 'Description', 'type' => 'textarea', 'max' => 1000],
    'phone' => ['label' => 'Phone', 'type' => 'text', 'max' => 40],
    'email' => ['label' => 'Email', 'type' => 'text', 'max' => 180],
    'address' => ['label' => 'Address', 'type' => 'textarea', 'max' => 400],
    'hours' => ['label' => 'Office hours', 'type' => 'text', 'max' => 120],
    'map_url' => ['label' => 'Map URL', 'type' => 'url', 'max' => 2048],
    'button_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 80],
    'button_url' => ['label' => 'Button URL', 'type' => 'url', 'max' => 2048],
    'background_color' => ['label' => 'Background color', 'type' => 'color'],
    'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
    'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
    'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
];
$defaults = [
    'eyebrow' => 'Contact Us', 'heading' => 'Do you have questions? Contact Us',
    'description' => 'Our team is ready to help with your property questions.',
    'phone' => '', 'email' => '', 'address' => '', 'hours' => '',
    'map_url' => '', 'button_label' => 'Get in touch', 'button_url' => '/contact',
    'show_mobile' => true, 'show_tablet' => true, 'show_desktop' => true,
];
$designs = [
    'contact.home-1' => ['label' => 'Contact — Three information cards (Piller Contact)', 'design' => 'home-1', 'view' => 'frontend.sections.contact.home-1'],
    'contact.home-2' => ['label' => 'Contact — Split details and map panel', 'design' => 'home-2', 'view' => 'frontend.sections.contact.home-2'],
    'contact.home-3' => ['label' => 'Contact — Dark conversion strip', 'design' => 'home-3', 'view' => 'frontend.sections.contact.home-3'],
];
$sections = [];
foreach ($designs as $key => $design) $sections[$key] = [
    'key' => $key, 'label' => $design['label'], 'type' => 'contact-panel',
    'source_design' => $design['design'], 'view' => $design['view'],
    'defaults' => $defaults, 'fields' => $fields,
];
return $sections;
