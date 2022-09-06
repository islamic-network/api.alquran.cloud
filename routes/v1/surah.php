<?php

use Api\Controllers;
use Slim\Routing\RouteCollectorProxy;

$app->group('/v1', function(RouteCollectorProxy $group) {

    $group->get('/surah',[Controllers\v1\Surah::class, 'get']);
    $group->get('/surah/{number}', [Controllers\v1\Surah::class, 'getByNumber']);
    $group->get('/surah/{number}/{edition}', [Controllers\v1\Surah::class, 'getOneByNumberAndEdition']);
    $group->get('/surah/{number}/editions/{editions}', [Controllers\v1\Surah::class, 'getManyByNumberAndEdition']);

});