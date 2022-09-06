<?php
use Api\Controllers;

$app->get('/liveness',
    [
        Controllers\Liveness::class,
        'get'
    ]
);

