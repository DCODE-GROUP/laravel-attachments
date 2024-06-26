<?php

return [
    'media' => [
        'conversions' => [
            'thumb' => [
                'width' => 43,
                'height' => 43,
            ],
            'list' => [
                'width' => 43,
                'height' => 43,
            ],
            'grid' => [
                'width' => 160,
                'height' => 192,
            ],
        ],
    ],

    'features' => [
        // annotations
        // categories
    ],

    'route_name_prefix' => env('LARAVEL_ATTACHMENTS_ROUTE_NAME_PREFIX', 'laravel_attachments'),

    'signed' => env('LARAVEL_ATTACHMENTS_URLS_SIGNED', false),
];
