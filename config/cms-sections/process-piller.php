<?php
$fields = [
    'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 100],
    'heading' => ['label' => 'Heading', 'type' => 'textarea', 'max' => 240],
    'description' => ['label' => 'Description', 'type' => 'textarea', 'max' => 1000],
    'image' => ['label' => 'Background image', 'type' => 'image', 'max' => 2048],
    'content_heading' => ['label' => 'Content panel heading', 'type' => 'text', 'max' => 180],
    'content_text' => ['label' => 'Content panel text', 'type' => 'textarea', 'max' => 1000],
    'button_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 80],
    'button_url' => ['label' => 'Button URL', 'type' => 'url', 'max' => 2048],
    'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
    'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
    'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
];
for($i=1;$i<=4;$i++){
    $fields['step_'.$i.'_title'] = ['label' => 'Step '.$i.' title', 'type' => 'text', 'max' => 120];
    $fields['step_'.$i.'_text'] = ['label' => 'Step '.$i.' description', 'type' => 'textarea', 'max' => 400];
}
return [
    'process.home-8' => [
        'key' => 'process.home-8', 'label' => 'Process Steps — Four-step home journey (Home 8)',
        'type' => 'process-steps', 'source_design' => 'home-8', 'view' => 'frontend.sections.process.home-8',
        'defaults' => [
            'eyebrow' => 'Work Process', 'heading' => 'Peace of mind at every step of your journey home',
            'content_heading' => 'We are committed to delivering an enjoyable real estate journey',
            'content_text' => 'Our local real estate agency is built on a deep understanding of the neighbourhoods, communities and property trends that shape our area.',
            'image' => '/theme-assets/img/normal/process-bg-img.jpg', 'button_label' => 'Explore All Property',
            'button_url' => '/properties', 'show_mobile' => true, 'show_tablet' => true, 'show_desktop' => true,
            'step_1_title' => 'Discover House', 'step_2_title' => 'Schedule to Visit',
            'step_3_title' => 'Hassle free Purchase', 'step_4_title' => 'Buy back Guarantee',
        ],
        'fields' => $fields,
    ],
];
