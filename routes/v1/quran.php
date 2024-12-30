<?php

use Api\Controllers;
use Slim\Routing\RouteCollectorProxy;

$app->group('/v1', function(RouteCollectorProxy $group) {

    $group->get('/quran',
        [
            Controllers\v1\Quran::class,
            'get'
        ]
    );

    $group->get('/quran/{edition}',
        [
            Controllers\v1\Quran::class,
            'getByEdition'
        ]
    );

    $group->post('/quran',
        [
            Controllers\v1\Quran::class,
            'createEdition'
        ]
    );

    $group->put('/quran/{edition}',
        [
            Controllers\v1\Quran::class,
            'updateEdition'
        ]
    );

});