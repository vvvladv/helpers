<?php

return [
    'noimage' => 'theme/images/noimage.png',

    'thumbs' => [
        'engine' => 'glide',
    ],

    'glide' => [
        'driver' => null,
        'cache' => 'assets/cache/thumbs',
        'temp_dir' => 'assets/cache',
        'defaults' => [
            'fm' => 'webp',
            'q' => 80,
            'sharp' => 5,
            'filt' => 'lanczos',
        ],
    ],

    'cache' => [
        'client_key' => null,
    ],
];
