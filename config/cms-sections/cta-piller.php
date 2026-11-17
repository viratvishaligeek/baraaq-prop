<?php
$downloadTwo = require __DIR__.'/download.php';
$downloadThree = require __DIR__.'/download-home-3.php';
$enquiryThree = require __DIR__.'/enquiry-home-3.php';
$sections = [];
$sourceDesigns = [
    'cta.download-home-2' => [$downloadTwo['download.home-2'], 'Download CTA — Home 2'],
    'cta.download-home-3' => [$downloadThree['download.home-3'], 'App download CTA — Home 3'],
    'cta.enquiry-home-3' => [$enquiryThree['enquiry.home-3'], 'Property enquiry CTA — Home 3'],
];
foreach ($sourceDesigns as $key => [$definition, $label]) {
    $definition['key'] = $key;
    $definition['label'] = $label;
    $definition['type'] = 'cta';
    $sections[$key] = $definition;
}
$sections['cta.home-10'] = [
    'key' => 'cta.home-10',
    'label' => 'Call to action — Modern split banner',
    'type' => 'cta',
    'source_design' => 'home-10',
    'view' => 'frontend.sections.cta.home-10',
    'defaults' => [
        'eyebrow' => 'Ready for your next move?',
        'heading' => 'Let us help you find a place that feels like home.',
        'description' => 'Speak with our property specialists and take the next step with confidence.',
        'button_label' => 'Talk to our team',
        'button_url' => '/contact',
        'background_image' => '/theme-assets/img/bg/download-bg-3.jpg',
    ],
    'fields' => [
        'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 100],
        'heading' => ['label' => 'Heading', 'type' => 'textarea', 'max' => 240],
        'description' => ['label' => 'Description', 'type' => 'textarea', 'max' => 1000],
        'button_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 80],
        'button_url' => ['label' => 'Button URL', 'type' => 'url', 'max' => 2048],
        'background_image' => ['label' => 'Background image', 'type' => 'image', 'max' => 2048],
        'background_color' => ['label' => 'Background color', 'type' => 'color'],
        'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
        'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
        'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
    ],
];
return $sections;
