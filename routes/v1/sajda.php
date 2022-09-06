<?php

use Api\Controllers;
use Slim\Routing\RouteCollectorProxy;

$app->group('/v1', function(RouteCollectorProxy $group) {

    $group->get('/sajda',
        [
            Controllers\v1\Sajda::class,
            'get'
        ]
    );

    $group->get('/sajda/{edition}',
        [
            Controllers\v1\Sajda::class,
            'getByEdition'
        ]
    );

});