<?php

use Api\Controllers;
use Slim\Routing\RouteCollectorProxy;

$app->group('/v1', function(RouteCollectorProxy $group) {

    $group->get('/ayah/random',[Controllers\v1\Ayah::class, 'getRandom']);
    $group->get('/ayah/random/{edition}', [Controllers\v1\Ayah::class, 'getRandomEdition']);
    $group->get('/ayah/random/editions/{editions}', [Controllers\v1\Ayah::class, 'getRandomEditions']);
    $group->get('/ayah/{number}', [Controllers\v1\Ayah::class, 'getOneByNumber']);
    $group->get('/ayah/{number}/{edition}', [Controllers\v1\Ayah::class, 'getOneByNumberAndEdition']);
    $group->get('/ayah/{number}/editions/{editions}', [Controllers\v1\Ayah::class, 'getManyByNumberAndEditions']);

});