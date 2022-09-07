<?php

use function Mamluk\Kipchak\env;

return [
    'name' => 'alquran-api', // Hyphen or underscore separated string
    'debug' => (bool) env('DEBUG', false),
    // If debug is enabled, loglevel is debug. Otherwise, it is info. Overwrite it by specifying it below.
    'loglevel' => \Monolog\Level::Debug
];