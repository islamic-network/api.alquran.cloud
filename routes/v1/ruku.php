<?php

use Api\Controllers;
use Slim\Routing\RouteCollectorProxy;

$app->group('/v1', function(RouteCollectorProxy $group) {

    $group->get('/ruku/{number}',
        [
            Controllers\v1\Ruku::class,
            'getByNumber'
        ]
    );

    $group->get('/ruku/{number}/{edition}',
        [
            Controllers\v1\Ruku::class,
            'getByEdition'
        ]
    );

});