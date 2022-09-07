<?php

use function Mamluk\Kipchak\env;

return
[
    'dbal' => [
        'enabled' => true,
        'connections' => [
            // https://www.doctrine-project.org/projects/doctrine-dbal/en/current/reference/configuration.html
                'primary' => [
                    'dbname' => env('DB_NAME', 'api'),
                    'user' => env('DB_USER', 'api'),
                    'password' =>  env('DB_PASSWORD', 'api'),
                    'host' => env('DB_HOST', 'mysql'),
                    'port' => env('DB_PORT', 3306),
                    'driver' => 'pdo_mysql',
                    'charset' => 'utf8'
                ]
            ]
    ],

    'orm' => [
        'enabled' => true,
        'entity_managers' => [
            'primary' => [
                // Enables or disables Doctrine metadata caching
                // for either performance or convenience during development.
                'dev_mode' => (bool) env('DEBUG', false),

                // List of paths where Doctrine will search for metadata.
                // Metadata can be either YML/XML files or PHP classes annotated
                // with comments or PHP8 attributes (effectively Doctrine's database entities)
                'metadata_dirs' => [
                    realpath(__DIR__ . '/../api/Entities/Doctrine/Primary')
                ],
                'metadata_format' => 'annotations', // attributes or annotations
                'connection' => 'primary', // Name of connection from 'dbal' above to use for the ORM
                'cache' => [
                    'enabled' => true, // Kipchak will only look at this if dev_mode = false
                    'store' => 'memcached', // file or memcached.
                ],
                'cache_config' => [
                    'memcached' => [
                        // Pool where Doctrine will cache the processed metadata when 'dev_mode' is false
                        'pool' => 'cache'
                    ],
                    'file' => [
                        // Path where Doctrine will cache the processed metadata when 'dev_mode' is false
                        'dir' => realpath(__DIR__) . '/../tmp/doctrine/cache',
                    ]
                ]
            ],
        ]
    ]
];
