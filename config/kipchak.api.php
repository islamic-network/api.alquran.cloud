<?php

use function Mamluk\Kipchak\env;

return [
    'name' => 'alquran-api', // Hyphen or underscore separated string
    'debug' => (bool) env('DEBUG', false),
    'logExceptions' => true,
    'logExceptionDetails' => false,
    'importerkey' => env('IMPORTER_KEY', 'AllahuAkbar')
    // If debug is enabled, loglevel is debug. Otherwise, it is info. Overwrite it by specifying it below.
    // 'loglevel' => \Monolog\Level::Debug
];