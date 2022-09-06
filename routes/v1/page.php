<?php

use Api\Controllers;
use Slim\Routing\RouteCollectorProxy;

$app->group('/v1', function(RouteCollectorProxy $group) {

    $group->get('/page/{number}',
        [
            Controllers\v1\Page::class,
            'getByNumber'
        ]
    );

    $group->get('/page/{number}/{edition}',
        [
            Controllers\v1\Page::class,
            'getByEdition'
        ]
    );

});