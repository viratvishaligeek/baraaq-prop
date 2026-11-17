<?php
$fields = [
    'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 100],
    'heading' => ['label' => 'Heading', 'type' => 'text', 'max' => 200],
    'background_color' => ['label' => 'Background color', 'type' => 'color'],
    'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
    'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
    'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
    'items_limit' => ['label' => 'Number of logos', 'type' => 'number', 'min' => 1, 'max' => 12],
];
for ($i=1; $i<=12; $i++) {
    $fields['item_'.$i.'_image'] = ['label' => 'Logo '.$i, 'type' => 'image', 'max' => 2048];
    $fields['item_'.$i.'_url'] = ['label' => 'Logo '.$i.' URL', 'type' => 'url', 'max' => 2048];
}
$defaults = ['eyebrow' => 'Trusted by', 'heading' => 'Our valued partners', 'items_limit' => 6, 'show_mobile' => true, 'show_tablet' => true, 'show_desktop' => true];
$designs = [
    'brands.grid' => ['label' => 'Client logos — Responsive logo grid', 'design' => 'grid', 'view' => 'frontend.sections.brands.grid'],
    'brands.marquee' => ['label' => 'Client logos — Continuous marquee strip', 'design' => 'marquee', 'view' => 'frontend.sections.brands.marquee'],
];
$sections = [];
foreach ($designs as $key => $design) $sections[$key] = [
    'key' => $key, 'label' => $design['label'], 'type' => 'brand-slider',
    'source_design' => $design['design'], 'view' => $design['view'],
    'defaults' => $defaults, 'fields' => $fields,
];
return $sections;
