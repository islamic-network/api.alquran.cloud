<?php

use Api\Controllers;
use Slim\Routing\RouteCollectorProxy;

$app->group('/v1', function(RouteCollectorProxy $group) {

    $group->get('/hizbQuarter/{number}',
        [
            Controllers\v1\HizbQuarter::class,
            'getByNumber'
        ]
    );

    $group->get('/hizbQuarter/{number}/{edition}',
        [
            Controllers\v1\HizbQuarter::class,
            'getByEdition'
        ]
    );

});