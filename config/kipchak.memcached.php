<?php

use function Mamluk\Kipchak\env;

return [
    'enabled' => true,
    'pools' => [
        'cache' => [
            [
                'host' => env('MEMCACHED_HOST', 'memcached'),
                'port' => env('MEMCACHED_PORT', 11211),
            ]
        ],
        'sessions' => [
            [
                'host' => env('MEMCACHED_HOST', 'memcached'),
                'port' => env('MEMCACHED_PORT', 11211),
            ]
        ]
    ]
];

