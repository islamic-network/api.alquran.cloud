<?php
header('Access-Control-Allow-Origin: *');

require_once (realpath(__DIR__ . '/../vendor/autoload.php'));

use Mamluk\Kipchak\Api;

// Instantiate Slim, load dependencies and middlewares
$app = Api::boot();

$app->run();