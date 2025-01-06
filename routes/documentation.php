<?php

use Api\Controllers;

$app->get('/documentation/openapi/yaml',[Controllers\Documentation::class, 'generate']);
$app->get('/documentation/openapi/ayah/yaml',[Controllers\Documentation::class, 'generate']);
$app->get('/documentation/openapi/edition/yaml',[Controllers\Documentation::class, 'generate']);
$app->get('/documentation/openapi/hizb-quarter/yaml',[Controllers\Documentation::class, 'generate']);