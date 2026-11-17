<?php
$fields = [
    'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 100],
    'heading' => ['label' => 'Heading', 'type' => 'text', 'max' => 200],
    'description' => ['label' => 'Description', 'type' => 'textarea', 'max' => 1200],
    'items_limit' => ['label' => 'Number of questions', 'type' => 'number', 'min' => 1, 'max' => 10],
    'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
    'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
    'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
];
for ($i = 1; $i <= 10; $i++) {
    $fields['item_'.$i.'_question'] = ['label' => 'Question '.$i, 'type' => 'text', 'max' => 240];
    $fields['item_'.$i.'_answer'] = ['label' => 'Answer '.$i, 'type' => 'textarea', 'max' => 1200];
}
return [
    'faq.home-10' => [
        'key' => 'faq.home-10',
        'label' => 'FAQ — Full-width accordion (Piller FAQ page)',
        'type' => 'faq-accordion',
        'source_design' => 'home-10',
        'view' => 'frontend.sections.faq.home-10',
        'defaults' => [
            'eyebrow' => 'FAQS',
            'heading' => 'What would you like to know about Pillar?',
            'description' => 'Answers to common questions about buying, selling and renting property.',
            'items_limit' => 5,
            'show_mobile' => true, 'show_tablet' => true, 'show_desktop' => true,
            'item_1_question' => 'How do I start my property search?',
            'item_1_answer' => 'Tell us what you are looking for and our team will help you shortlist suitable properties.',
        ],
        'fields' => $fields,
    ],
];
