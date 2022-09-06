<?php

use function Mamluk\Kipchak\env;

return [
    'jwks' => [
        'enabled' => false, // to enable this globally
        'jwksUri' => 'https://auth.islamic.network/auth/realms/islamic-network/protocol/openid-connect/certs',
        'validate_scopes' => true, // If enabled, the following scopes will be validated unless custom ones are passed to the AuthWJKS Middleware
        'scopes' => [
            'email',
            'profile'
        ],
    ],
    'key' => [
        'enabled' => false, // Will check for key in key query parameter (?key=xxxxxxx) or x-api-key header globally
        'authorised_keys' => [
            // This format is chosen because it's faster than array with just keys. The values key1, key2 or key3 will be checked
            'key1' => 'client1',
            'key2' => 'client2',
            'key3' => 'client3'
        ]
    ]
];