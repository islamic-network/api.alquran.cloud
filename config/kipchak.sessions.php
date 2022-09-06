<?php

use function Mamluk\Kipchak\env;

return [
    'enabled' => false,
    'name' => 'kipchak-api', // Session name in the browser
    'lifetime' => '30 days',
    'store' => 'couchdb', // couchdb or memcached
    'store_config' => [
        'couchdb' => [
            'connection' => 'default',
            'database' => 'api_sessions',
        ],
        'memcached' => [
            'pool' => 'sessions'
        ]
    ],
    'cookie_options' => [
        'path' => '/',
        'domain' => 'localhost',
        'secure' => false,
        'httponly' => true,
        'samesite' => true,
    ]


];