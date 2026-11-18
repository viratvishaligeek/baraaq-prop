<?php
$fields = [
    'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 100],
    'heading' => ['label' => 'Heading', 'type' => 'text', 'max' => 200],
    'description' => ['label' => 'Description', 'type' => 'textarea', 'max' => 800],
    'background_color' => ['label' => 'Background color', 'type' => 'color'],
    'background_image' => ['label' => 'Background image', 'type' => 'image', 'max' => 2048],
    'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
    'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
    'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
];
for ($i=1; $i<=4; $i++) {
    $fields['stat_'.$i.'_value'] = ['label' => 'Statistic '.$i.' value', 'type' => 'text', 'max' => 24];
    $fields['stat_'.$i.'_suffix'] = ['label' => 'Statistic '.$i.' suffix', 'type' => 'text', 'max' => 12];
    $fields['stat_'.$i.'_label'] = ['label' => 'Statistic '.$i.' label', 'type' => 'text', 'max' => 80];
}
$defaults = [
    'eyebrow' => 'Our Impact', 'heading' => 'Results that speak for themselves',
    'description' => '', 'background_color' => '',
    'show_mobile' => true, 'show_tablet' => true, 'show_desktop' => true,
    'stat_1_value' => '1250', 'stat_1_suffix' => '+', 'stat_1_label' => 'Project handovers',
    'stat_2_value' => '20', 'stat_2_suffix' => 'M', 'stat_2_label' => 'Property value',
    'stat_3_value' => '100', 'stat_3_suffix' => '%', 'stat_3_label' => 'Client commitment',
    'stat_4_value' => '150', 'stat_4_suffix' => '+', 'stat_4_label' => 'Happy customers',
];
$designs = [
    'statistics.home-9' => ['label' => 'Statistics — Home 9 counter strip', 'design' => 'home-9', 'view' => 'frontend.sections.statistics.home-9'],
    'statistics.home-4' => ['label' => 'Statistics — Luxury counter strip (Home 4)', 'design' => 'home-4', 'view' => 'frontend.sections.statistics.home-4'],
    'statistics.home-6' => ['label' => 'Statistics — Corporate counter cards (Home 6)', 'design' => 'home-6', 'view' => 'frontend.sections.statistics.home-6'],
    'statistics.home-10' => ['label' => 'Statistics — Modern divider counters (Home 10)', 'design' => 'home-10', 'view' => 'frontend.sections.statistics.home-10'],
];
$sections = [];
foreach ($designs as $key => $design) $sections[$key] = [
    'key' => $key, 'label' => $design['label'], 'type' => 'statistics',
    'source_design' => $design['design'], 'view' => $design['view'],
    'defaults' => $defaults, 'fields' => $fields,
];
return $sections;
