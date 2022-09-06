<?php

use Api\Controllers;
use Slim\Routing\RouteCollectorProxy;

$app->group('/v1', function(RouteCollectorProxy $group) {

    $group->get('/edition',[Controllers\v1\Edition::class, 'get']);
    $group->get('/edition/type',[Controllers\v1\Edition::class, 'getTypes']);
    $group->get('/edition/type/{type}',[Controllers\v1\Edition::class, 'getByType']);
    $group->get('/edition/format',[Controllers\v1\Edition::class, 'getFormats']);
    $group->get('/edition/format/{format}',[Controllers\v1\Edition::class, 'getByFormat']);
    $group->get('/edition/language',[Controllers\v1\Edition::class, 'getLanguages']);
    $group->get('/edition/language/{lang}',[Controllers\v1\Edition::class, 'getByLanguage']);

});