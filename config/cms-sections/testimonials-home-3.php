<?php

return [
    'testimonials.home-3' => [
        'key' => 'testimonials.home-3',
        'label' => 'Trust score and testimonials — Home 3',
        'type' => 'testimonial-slider',
        'source_design' => 'home-3',
        'view' => 'frontend.sections.testimonials.home-3',
        'defaults' => [
            'rating' => '4.9',
            'review_count' => 189,
            'view_all_label' => 'View All Reviews',
            'view_all_url' => '/testimonials',
            'trust_brand' => 'Trustipilot',
            'eyebrow' => 'Testimonials',
            'heading' => 'What Clients Say About Pillar',
            'items_limit' => 4,
            'show_mobile' => true, 'show_tablet' => true, 'show_desktop' => true,
            'item_1_quote' => 'Adinventitias sequi cerno cedo vapulus adhaero decretum suppono iure voluptate. Trans triumphus toties sed cogito carbo valetudo aequus ciminatio conventus.',
            'item_1_quote_2' => 'Aer arceo umerus asperiores templum desidero caritas. Velum adipisci verumtamen comparo ascisco ceno vitiosus aeneus tenetur bibo.',
            'item_1_name' => 'Georgia Schaden', 'item_1_role' => 'Property owner',
            'item_2_quote' => 'Tenax comitatus ambulo regnum eligo, conturbo vis caelum cohors. Infit ustulo adoptio collum, speciosus lumen soluta.',
            'item_2_quote_2' => 'Aer arceo umerus asperiores templum desidero caritas. Tracto caterva cavus denique culpa vigor.',
            'item_2_name' => 'Walter Deckow', 'item_2_role' => 'Property owner',
            'item_3_quote' => 'Contendo votum traho clamo statim suffragium numquam teneo. Altus iudicium ceno vitiosus aeneus tenetur.',
            'item_3_quote_2' => 'Verecundia capio denego. Tracto caterva cavus denique culpa vigor tergum possimus.',
            'item_3_name' => 'Carlos Bauch', 'item_3_role' => 'Property owner',
            'item_4_quote' => 'Sporto momentum, virgo sollemne aurora amo cedo umquam cedo.',
            'item_4_quote_2' => 'Tempus appono validus translatio, exemplum adnuo radius caecus exsisto.',
            'item_4_name' => 'Julia Era', 'item_4_role' => 'Property owner',
        ],
        'fields' => array_merge([
            'rating' => ['label' => 'Trust rating', 'type' => 'text', 'max' => 8],
            'review_count' => ['label' => 'Review count', 'type' => 'number', 'min' => 0, 'max' => 10000000],
            'view_all_label' => ['label' => 'View all button label', 'type' => 'text', 'max' => 80],
            'view_all_url' => ['label' => 'View all button URL', 'type' => 'url', 'max' => 2048],
            'trust_brand' => ['label' => 'Review platform name', 'type' => 'text', 'max' => 80],
            'avatar_group_image' => ['label' => 'Review avatar group image', 'type' => 'image', 'max' => 2048],
            'eyebrow' => ['label' => 'Section eyebrow', 'type' => 'text', 'max' => 120],
            'heading' => ['label' => 'Section heading', 'type' => 'text', 'max' => 180],
            'items_limit' => ['label' => 'Visible testimonials', 'type' => 'number', 'min' => 1, 'max' => 4],
            'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
            'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
            'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
        ], (function () {
            $fields = [];
            for ($i = 1; $i <= 4; $i++) {
                $fields['item_'.$i.'_quote'] = ['label' => 'Testimonial '.$i.' first paragraph', 'type' => 'textarea', 'max' => 1200];
                $fields['item_'.$i.'_quote_2'] = ['label' => 'Testimonial '.$i.' second paragraph', 'type' => 'textarea', 'max' => 1200];
                $fields['item_'.$i.'_image'] = ['label' => 'Testimonial '.$i.' portrait', 'type' => 'image', 'max' => 2048];
                $fields['item_'.$i.'_name'] = ['label' => 'Testimonial '.$i.' name', 'type' => 'text', 'max' => 120];
                $fields['item_'.$i.'_role'] = ['label' => 'Testimonial '.$i.' role', 'type' => 'text', 'max' => 120];
            }
            return $fields;
        })()),
    ],
];
