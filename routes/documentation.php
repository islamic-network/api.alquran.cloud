<?php

use Api\Controllers\v1\Documentation;

$app->get('/v1/documentation/openapi/alquran/yaml', [Documentation\AlQuranOpenApi::class, 'generate']);