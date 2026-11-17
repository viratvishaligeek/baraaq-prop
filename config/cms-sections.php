<?php

$heroDesigns = [
    'index' => ['label' => 'Hero — Home 1', 'view' => 'frontend.sections.hero.index'],
    'home-2' => ['label' => 'Hero — Home 2', 'view' => 'frontend.sections.hero.home-2'],
    'home-3' => ['label' => 'Hero — Home 3', 'view' => 'frontend.sections.hero.home-3'],
    'home-4' => ['label' => 'Hero — Home 4', 'view' => 'frontend.sections.hero.home-4'],
    'home-5' => ['label' => 'Hero — Home 5', 'view' => 'frontend.sections.hero.home-5'],
    'home-6' => ['label' => 'Hero — Home 6', 'view' => 'frontend.sections.hero.home-6'],
    'home-7' => ['label' => 'Hero — Home 7', 'view' => 'frontend.sections.hero.home-7'],
    'home-8' => ['label' => 'Hero — Home 8', 'view' => 'frontend.sections.hero.home-8'],
    'home-9' => ['label' => 'Hero — Home 9', 'view' => 'frontend.sections.hero.home-9'],
    'home-10' => ['label' => 'Hero — Home 10', 'view' => 'frontend.sections.hero.home-10'],
];

$sections = [];

foreach ($heroDesigns as $design => $definition) {
    $key = 'hero.'.$design;

    $sections[$key] = [
        'key' => $key,
        'label' => $definition['label'],
        'type' => 'hero',
        'source_design' => $design,
        'view' => $definition['view'],
        'defaults' => [],
        'settings' => $design === 'index' ? [] : ['heading'],
    ];
}

$sections['properties.index'] = [
    'key' => 'properties.index',
    'label' => 'Featured properties — Home 1',
    'type' => 'property-list',
    'source_design' => 'index',
    'view' => 'frontend.sections.properties.index',
    'defaults' => [],
    'settings' => ['eyebrow', 'heading'],
];

$sections['team.index'] = [
    'key' => 'team.index',
    'label' => 'Team members — Home 1',
    'type' => 'team',
    'source_design' => 'index',
    'view' => 'frontend.sections.team.index',
    'defaults' => [],
    'settings' => ['eyebrow', 'heading'],
];

$sections['blog.index'] = [
    'key' => 'blog.index',
    'label' => 'Latest articles — Home 1',
    'type' => 'blog',
    'source_design' => 'index',
    'view' => 'frontend.sections.blog.index',
    'defaults' => [],
    'settings' => ['eyebrow', 'heading'],
];

return [
    'sections' => $sections,
];
