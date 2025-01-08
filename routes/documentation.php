<?php

use Api\Controllers\v1\Documentation;
use Slim\Routing\RouteCollectorProxy;

/**
 * @var \Slim\App $app
 */

$app->group('/v1/documentation/openapi', function(RouteCollectorProxy $group) {
    $group->get('/ayah/yaml',[Documentation\Ayat::class, 'generate']);
    $group->get('/edition/yaml',[Documentation\Edition::class, 'generate']);
    $group->get('/hizb-quarter/yaml',[Documentation\HizbQuarter::class, 'generate']);
    $group->get('/juz/yaml',[Documentation\Juz::class, 'generate']);
    $group->get('/manzil/yaml',[Documentation\Manzil::class, 'generate']);
    $group->get('/meta/yaml',[Documentation\Meta::class, 'generate']);
    $group->get('/page/yaml',[Documentation\Page::class, 'generate']);
    $group->get('/quran/yaml', [Documentation\Quran::class, 'generate']);
    $group->get('/ruku/yaml', [Documentation\Ruku::class, 'generate']);
    $group->get('/sajda/yaml', [Documentation\Sajda::class, 'generate']);
    $group->get('/search/yaml', [Documentation\Search::class, 'generate']);
    $group->get('/surah/yaml', [Documentation\Surah::class, 'generate']);
});