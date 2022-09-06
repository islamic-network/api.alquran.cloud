<?php
use Api\Controllers;

$app->get('/status',
    [
        Controllers\Status::class,
        'get'
    ]
);

