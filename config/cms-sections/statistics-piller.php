<?php

$fields = [
    'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
    'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
    'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
];

$defaults = [
    'show_mobile' => true,
    'show_tablet' => true,
    'show_desktop' => true,
    'stat_1_value' => '',
    'stat_1_suffix' => '',
    'stat_1_label' => '',
    'stat_2_value' => '',
    'stat_2_suffix' => '',
    'stat_2_label' => '',
    'stat_3_value' => '',
    'stat_3_suffix' => '',
    'stat_3_label' => '',
    'stat_4_value' => '',
    'stat_4_suffix' => '',
    'stat_4_label' => '',
];

for ($i = 1; $i <= 4; $i++) {
    $fields['stat_'.$i.'_value'] = ['label' => 'Statistic '.$i.' value', 'type' => 'text', 'max' => 24];
    $fields['stat_'.$i.'_suffix'] = ['label' => 'Statistic '.$i.' suffix', 'type' => 'text', 'max' => 12];
    $fields['stat_'.$i.'_label'] = ['label' => 'Statistic '.$i.' label', 'type' => 'text', 'max' => 80];
}

return [
    'statistics.home-9' => [
        'key' => 'statistics.home-9',
        'label' => 'Statistics — Home 9 counter strip',
        'type' => 'statistics',
        'view' => 'frontend.sections.statistics.home-9',
        'defaults' => $defaults,
        'fields' => $fields,
    ],
];
