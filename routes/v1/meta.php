<?php

use Api\Controllers;
use Slim\Routing\RouteCollectorProxy;

$app->group('/v1', function(RouteCollectorProxy $group) {

    $group->get('/meta',
        [
            Controllers\v1\Meta::class,
            'get'
        ]
    );

});