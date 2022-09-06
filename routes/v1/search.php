<?php

use Api\Controllers;
use Slim\Routing\RouteCollectorProxy;

$app->group('/v1', function(RouteCollectorProxy $group) {

    $group->get('/search/{word}', [Controllers\v1\Search::class, 'getWord']);
    $group->get('/search/{word}/{surah}', [Controllers\v1\Search::class, 'getWordSurah']);
    $group->get('/search/{word}/{surah}/{language}', [Controllers\v1\Search::class, 'getWordSurahLanguage']);

});