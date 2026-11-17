<?php

return [
    'testimonials.home-2' => [
        'key' => 'testimonials.home-2',
        'label' => 'Testimonials and review summary — Home 2',
        'type' => 'testimonial-slider',
        'source_design' => 'home-2',
        'view' => 'frontend.sections.testimonials.home-2',
        'defaults' => [
            'eyebrow' => 'Testimonials',
            'heading' => 'What Clients Say About Pillar',
            'quote_icon' => '/theme-assets/img/icon/testi-2quote.svg',
            'loop' => true,
            'vertical_spacing' => 'space',
            'show_trust_summary' => true,
            'trust_1_icon' => '/theme-assets/img/icon/star-icon.svg',
            'trust_1_name' => 'Trustipilot',
            'trust_1_avatars' => '/theme-assets/img/hero/heror-1-avator-group.png',
            'trust_1_score' => '19',
            'trust_1_suffix' => 'k+',
            'trust_1_label' => 'clients',
            'trust_2_icon' => '/theme-assets/img/icon/google-icon.svg',
            'trust_2_name' => 'Google',
            'trust_2_avatars' => '/theme-assets/img/hero/heror-1-avator-group-2.png',
            'trust_2_score' => '25',
            'trust_2_suffix' => 'k+',
            'trust_2_label' => 'clients',
            'show_mobile' => true,
            'show_tablet' => true,
            'show_desktop' => true,
            'item_1_quote' => '“Adinventitias sequi cerno cedo vapulus adhaero decretum suppono iure voluptate. Trans triumphus toties sed cogito carbo valetudo aequus ciminatio conventus. Voluptates decimus vorago suadeo. Culpo carmen adnuo. Verecundia capio denego. Tracto caterva cavus denique culpa vigor tergum possimus. Aer arceo umerus asperiores templum desidero caritas. Velum adipisci verumtamen comparo ascisco ceno vitiosus aeneus tenetur bibo.”',
            'item_1_name' => 'Georgia Schaden',
            'item_1_role' => 'Property owner',
            'item_1_avatar' => '/theme-assets/img/testimonial/testi-2-1.jpg',
            'item_1_rating' => 5,
            'item_2_quote' => '“Tenax comitatus ambulo regnum eligo, conturbo vis caelum cohors. Infit ustulo adoptio collum, speciosus lumen soluta. Condico spiculum ratio, sopor conventus adversus fiducia. Arma arcesso altaria impleo pax laboriosus. Tracto caterva cavus denique culpa vigor tergum possimus. Aer arceo umerus asperiores templum desidero caritas. Trepide asporto momentum, Tracto caterva cavus denique culpa vigor. virgo sollemne aurora amo”',
            'item_2_name' => 'Walter Deckow',
            'item_2_role' => 'Property owner',
            'item_2_avatar' => '/theme-assets/img/testimonial/testi-2-2.jpg',
            'item_2_rating' => 5,
            'item_3_quote' => '“Contendo votum traho clamo statim suffragium numquam teneo. Altus iudicium versor, perpetuus velum pax vestigium carpo. Candidus firmamentum celo, voluptas optio demitto sonitus. Obduro casus adinventio vestigium amplitudo sumo.vestigium carpo. Candidus firmamentum celo, voluptas Concido laboriosam varietas amplector, aureus stella sursum approbo. Gravitas turbo decet, hhe adulescens perpetuus auxilium comes virga.”',
            'item_3_name' => 'Carlos Bauch',
            'item_3_role' => 'Property owner',
            'item_3_avatar' => '/theme-assets/img/testimonial/testi-2-3.jpg',
            'item_3_rating' => 5,
            'item_4_quote' => '“Tenax comitatus ambulo regnum eligo, conturbo vis caelum cohors. Infit ustulo adoptio collum, speciosus lumen soluta. Condico spiculum ratio, sopor conventus adversus fiducia. Arma arcesso altaria impleo pax laboriosus. Trepide asporto momentum, virgo sollemne aurora amo. Cedo umquam cedo summisse damnum reiciendis veritas flamma. Tempus appono validus translatio, Exemplum adnuo sponte voluntas radius caecus exsisto.”',
            'item_4_name' => 'Julia Era',
            'item_4_role' => 'Property owner',
            'item_4_avatar' => '/theme-assets/img/testimonial/testi-2-4.jpg',
            'item_4_rating' => 5,
        ],
        'fields' => array_merge([
            'eyebrow' => ['label' => 'Eyebrow', 'type' => 'text', 'max' => 120],
            'heading' => ['label' => 'Heading', 'type' => 'text', 'max' => 180],
            'quote_icon' => ['label' => 'Quote icon', 'type' => 'image', 'max' => 2048],
            'loop' => ['label' => 'Loop testimonial slider', 'type' => 'checkbox'],
            'vertical_spacing' => ['label' => 'Section spacing', 'type' => 'select', 'options' => ['space' => 'Theme spacing', 'space-top' => 'Top only', 'space-bottom' => 'Bottom only', 'py-0' => 'No vertical spacing']],
            'show_trust_summary' => ['label' => 'Show review platform summary', 'type' => 'checkbox'],
            'show_mobile' => ['label' => 'Visible on mobile', 'type' => 'checkbox'],
            'show_tablet' => ['label' => 'Visible on tablet', 'type' => 'checkbox'],
            'show_desktop' => ['label' => 'Visible on desktop', 'type' => 'checkbox'],
        ], (function () {
            $fields = [];
            for ($i = 1; $i <= 4; $i++) {
                $fields['item_'.$i.'_quote'] = ['label' => 'Review '.$i.' quote', 'type' => 'textarea', 'max' => 1500];
                $fields['item_'.$i.'_name'] = ['label' => 'Review '.$i.' name', 'type' => 'text', 'max' => 150];
                $fields['item_'.$i.'_role'] = ['label' => 'Review '.$i.' role', 'type' => 'text', 'max' => 120];
                $fields['item_'.$i.'_avatar'] = ['label' => 'Review '.$i.' avatar', 'type' => 'image', 'max' => 2048];
                $fields['item_'.$i.'_rating'] = ['label' => 'Review '.$i.' rating', 'type' => 'number', 'min' => 1, 'max' => 5];
            }
            for ($i = 1; $i <= 2; $i++) {
                $fields['trust_'.$i.'_icon'] = ['label' => 'Platform '.$i.' icon', 'type' => 'image', 'max' => 2048];
                $fields['trust_'.$i.'_name'] = ['label' => 'Platform '.$i.' name', 'type' => 'text', 'max' => 80];
                $fields['trust_'.$i.'_avatars'] = ['label' => 'Platform '.$i.' avatar group', 'type' => 'image', 'max' => 2048];
                $fields['trust_'.$i.'_score'] = ['label' => 'Platform '.$i.' client count', 'type' => 'text', 'max' => 20];
                $fields['trust_'.$i.'_suffix'] = ['label' => 'Platform '.$i.' count suffix', 'type' => 'text', 'max' => 12];
                $fields['trust_'.$i.'_label'] = ['label' => 'Platform '.$i.' count label', 'type' => 'text', 'max' => 40];
            }
            return $fields;
        })()),
    ],
];
