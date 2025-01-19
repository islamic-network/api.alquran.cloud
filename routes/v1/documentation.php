<?php

use Api\Controllers\v1\Documentation;

$app->get('/v1/documentation/openapi/yaml', [\Api\Controllers\v1\Documentation::class, 'generate']);