<?php

return [
    'admin' => [
        'name' => env('CMS_ADMIN_NAME', 'Site Administrator'),
        'email' => env('CMS_ADMIN_EMAIL'),
        'password' => env('CMS_ADMIN_PASSWORD'),
    ],

    // Null means local/testing only. Production needs an explicit opt-in.
    'demo_properties' => env('CMS_DEMO_PROPERTIES'),
];
