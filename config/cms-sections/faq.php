<?php

return [
    'faq.home-2' => [
        'key' => 'faq.home-2',
        'label' => 'FAQ accordion — Home 2',
        'type' => 'faq-accordion',
        'source_design' => 'home-2',
        'view' => 'frontend.sections.faq.home-2',
        'defaults' => [
            'eyebrow' => 'FAQ',
            'heading' => 'What would you like to know about pillar?',
            'description' => 'Ancilla cogo cognomen absorbeo coerceo apto. Ago vulgaris absens decor. Valetudo urbs sollers. Ter tricesimus currus aro vilis suadeo ulterius aveho.',
            'button_label' => 'Free Consultation',
            'button_url' => '/about',
            'shape_image' => '/theme-assets/img/shape/faq-2-shape.png',
            'circle_image' => '/theme-assets/img/shape/faq-2-circle-shape.png',
            'vertical_spacing' => 'space',
            'animate_items' => true,
            'show_mobile' => true,
            'show_tablet' => true,
            'show_desktop' => true,
            'item_1_question' => 'What is Pillar real estate?',
            'item_1_answer' => 'Most Islamic centers offer charity programs, counseling, and financial assistance for those in need. Non-Muslims are welcome to visit mosques. They should dress modestly and remove their shoes upon entering. You can attend community events, volunteer at the mosque, join study circles, or participate in outreach programs.',
            'item_2_question' => 'How to work pillar all services?',
            'item_2_answer' => 'Most Islamic centers offer charity programs, counseling, and financial assistance for those in need. Non-Muslims are welcome to visit mosques. They should dress modestly and remove their shoes upon entering. You can attend community events, volunteer at the mosque, join study circles, or participate in outreach programs.',
            'item_3_question' => 'What resources are available for interfaith dialogue?',
            'item_3_answer' => 'Most Islamic centers offer charity programs, counseling, and financial assistance for those in need. Non-Muslims are welcome to visit mosques. They should dress modestly and remove their shoes upon entering. You can attend community events, volunteer at the mosque, join study circles, or participate in outreach programs.',
            'item_4_question' => 'What programs are available for developer?',
            'item_4_answer' => 'Most Islamic centers offer charity programs, counseling, and financial assistance for those in need. Non-Muslims are welcome to visit mosques. They should dress modestly and remove their shoes upon entering. You can attend community events, volunteer at the mosque, join study circles, or participate in outreach programs.',
            'item_5_question' => 'How can I contribute to the local real estate?',
            'item_5_answer' => 'Most Islamic centers offer charity programs, counseling, and financial assistance for those in need. Non-Muslims are welcome to visit mosques. They should dress modestly and remove their shoes upon entering. You can attend community events, volunteer at the mosque, join study circles, or participate in outreach programs.',
        ],
        'fields' => array_merge([
            'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
            'heading' => ['label' => 'Heading', 'type' => 'text', 'max' => 180],
            'description' => ['label' => 'Description', 'type' => 'textarea', 'max' => 1000],
            'button_label' => ['label' => 'Consultation button label', 'type' => 'text', 'max' => 80],
            'button_url' => ['label' => 'Consultation button URL', 'type' => 'url', 'max' => 2048],
            'shape_image' => ['label' => 'Bottom decorative image', 'type' => 'image', 'max' => 2048],
            'circle_image' => ['label' => 'Rotating decorative image', 'type' => 'image', 'max' => 2048],
            'vertical_spacing' => ['label' => 'Section spacing', 'type' => 'select', 'options' => ['space' => 'Theme spacing', 'space-top' => 'Top only', 'space-bottom' => 'Bottom only', 'py-0' => 'No vertical spacing']],
            'animate_items' => ['label' => 'Enable entrance animations', 'type' => 'checkbox'],
            'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
            'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
            'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
        ], (function () {
            $fields = [];
            for ($i = 1; $i <= 5; $i++) {
                $fields['item_'.$i.'_question'] = ['label' => 'Question '.$i, 'type' => 'text', 'max' => 255];
                $fields['item_'.$i.'_answer'] = ['label' => 'Answer '.$i, 'type' => 'textarea', 'max' => 1500];
            }
            return $fields;
        })()),
    ],
];
