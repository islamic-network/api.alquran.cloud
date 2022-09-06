<?php

use Api\Controllers;
use Slim\Routing\RouteCollectorProxy;

$app->group('/v1', function(RouteCollectorProxy $group) {

    $group->get('/juz/{number}',
        [
            Controllers\v1\Juz::class,
            'getByNumber'
        ]
    );

    $group->get('/juz/{number}/{edition}',
        [
            Controllers\v1\Juz::class,
            'getByEdition'
        ]
    );

});