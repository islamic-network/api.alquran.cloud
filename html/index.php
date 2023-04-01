<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Authorization, origin, if-none-match');
header('Access-Control-Expose-Headers: *');

require_once (realpath(__DIR__ . '/../vendor/autoload.php'));

use Mamluk\Kipchak\Api;

// Instantiate Slim, load dependencies and middlewares
$app = Api::boot();

$app->run();
