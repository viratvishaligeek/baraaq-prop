<?php
$fields = [
    'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 100],
    'heading' => ['label' => 'Heading', 'type' => 'text', 'max' => 200],
    'description' => ['label' => 'Description', 'type' => 'textarea', 'max' => 1000],
    'items_limit' => ['label' => 'Number of gallery items', 'type' => 'number', 'min' => 1, 'max' => 12],
    'button_label' => ['label' => 'Button label', 'type' => 'text', 'max' => 80],
    'button_url' => ['label' => 'Button URL', 'type' => 'url', 'max' => 2048],
    'background_color' => ['label' => 'Background color', 'type' => 'color'],
    'background_image' => ['label' => 'Background image', 'type' => 'image', 'max' => 2048],
    'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
    'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
    'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
];
for ($i=1; $i<=12; $i++) {
    $fields['item_'.$i.'_image'] = ['label' => 'Image '.$i, 'type' => 'image', 'max' => 2048];
    $fields['item_'.$i.'_title'] = ['label' => 'Title '.$i, 'type' => 'text', 'max' => 140];
    $fields['item_'.$i.'_text'] = ['label' => 'Description '.$i, 'type' => 'textarea', 'max' => 500];
    $fields['item_'.$i.'_url'] = ['label' => 'Link '.$i, 'type' => 'url', 'max' => 2048];
}
$defaults = [
    'eyebrow' => 'Explore More', 'heading' => 'Discover the details that make a home special',
    'description' => '', 'items_limit' => 6, 'button_label' => 'Explore All Projects',
    'button_url' => '/properties', 'show_mobile' => true, 'show_tablet' => true, 'show_desktop' => true,
];
return [
    'gallery.home-5-rooms' => [
        'key' => 'gallery.home-5-rooms', 'label' => 'Image Gallery — Room details showcase (Home 5)',
        'type' => 'room-gallery', 'source_design' => 'home-5', 'view' => 'frontend.sections.gallery.home-5-rooms',
        'defaults' => array_merge($defaults, [
            'eyebrow' => 'Get Room Details', 'heading' => 'Discover Luxury Home Every Rooms', 'items_limit' => 5,
            'item_1_image' => '/theme-assets/img/normal/room-img-1.png', 'item_1_title' => 'Living Room', 'item_1_text' => 'A warm and inviting space filled with comfortable seating and engaging entertainment options.',
            'item_2_image' => '/theme-assets/img/normal/room-img-2.png', 'item_2_title' => 'Dining Room', 'item_2_text' => 'A contemporary culinary haven designed for innovation and inspiration.',
            'item_3_image' => '/theme-assets/img/normal/room-img-3.png', 'item_3_title' => 'Kitchen', 'item_3_text' => 'A cozy and welcoming space designed for enjoying meals and engaging conversations.',
            'item_4_image' => '/theme-assets/img/normal/room-img-4.png', 'item_4_title' => 'Master Bedroom', 'item_4_text' => 'A tranquil retreat crafted for restorative sleep and refreshing starts.',
            'item_5_image' => '/theme-assets/img/normal/room-img-5.png', 'item_5_title' => 'Bathroom', 'item_5_text' => 'A serene wellness retreat with modern amenities and a tranquil atmosphere.',
        ]),
        'fields' => $fields,
    ],
    'gallery.home-10-projects' => [
        'key' => 'gallery.home-10-projects', 'label' => 'Image Gallery — Asymmetric project gallery (Home 10)',
        'type' => 'project-gallery', 'source_design' => 'home-10', 'view' => 'frontend.sections.gallery.home-10-projects',
        'defaults' => array_merge($defaults, [
            'eyebrow' => 'Our Projects', 'heading' => 'Collaborative designs, Extraordinary Results', 'items_limit' => 8,
            'item_1_image' => '/theme-assets/img/project/project_2_1.jpg', 'item_1_title' => 'Interior Excellence', 'item_1_text' => 'Luxurious Designs',
            'item_2_image' => '/theme-assets/img/project/project_2_2.jpg', 'item_2_title' => 'Interior Excellence', 'item_2_text' => 'Luxurious Designs',
            'item_3_image' => '/theme-assets/img/project/project_2_3.jpg', 'item_3_title' => 'Interior Excellence', 'item_3_text' => 'Luxurious Designs',
            'item_4_image' => '/theme-assets/img/project/project_2_4.jpg', 'item_4_title' => 'Interior Excellence', 'item_4_text' => 'Luxurious Designs',
            'item_5_image' => '/theme-assets/img/project/project_2_5.jpg', 'item_5_title' => 'Interior Excellence', 'item_5_text' => 'Luxurious Designs',
            'item_6_image' => '/theme-assets/img/project/project_2_6.jpg', 'item_6_title' => 'Interior Excellence', 'item_6_text' => 'Luxurious Designs',
            'item_7_image' => '/theme-assets/img/project/project_2_7.jpg', 'item_7_title' => 'Interior Excellence', 'item_7_text' => 'Luxurious Designs',
            'item_8_image' => '/theme-assets/img/project/project_2_8.jpg', 'item_8_title' => 'Interior Excellence', 'item_8_text' => 'Luxurious Designs',
        ]),
        'fields' => $fields,
    ],
];
