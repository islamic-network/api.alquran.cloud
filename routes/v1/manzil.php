<?php

use Api\Controllers;
use Slim\Routing\RouteCollectorProxy;

$app->group('/v1', function(RouteCollectorProxy $group) {

    $group->get('/manzil/{number}',
        [
            Controllers\v1\Manzil::class,
            'getByNumber'
        ]
    );

    $group->get('/manzil/{number}/{edition}',
        [
            Controllers\v1\Manzil::class,
            'getByEdition'
        ]
    );

});