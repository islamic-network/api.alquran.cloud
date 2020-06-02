<?php
header('Access-Control-Allow-Origin: *');

// Main AlQuran autoloader
require realpath(__DIR__) . '/../config/autoloader.php';

use Slim\Http\Request;
use Slim\Http\Response;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

//error_reporting(E_ALL);
//ini_set('display_errors', 1);

/** App settings **/
$config['displayErrorDetails'] = false;

$app = new \Slim\App(["settings" => $config]);

$container = $app->getContainer();
$container['logger'] = function($c) {
    $logId = uniqid();
    $logStamp = time();
    $logFile = date('Y-m-d', $logStamp);
    // Create the logger
    $logger = new Logger('QuranApi');
    // Now add some handlers
    $logger->pushHandler(new \Monolog\Handler\ErrorLogHandler());

    return $logger;
};

$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        $r = [
        'code' => 404,
        'status' => 'Not Found',
        'data' => 'Invalid endpoint or resource.'
        ];
        $resp = json_encode($r);
        return $c['response']
            ->withStatus(404)
            ->withHeader('Content-Type', 'application/json')
            ->write($resp);
    };
};

$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        if ($exception instanceof \Quran\Exception\WafKeyMismatchException) {
            $r = [
                'code' => 403,
                'status' => 'Forbidden',
                'data' => 'WAF Key Mismatch.'
            ];

            return $c['response']
                ->withStatus(403)
                ->withHeader('Content-Type', 'application/json')
                ->write(json_encode($r));
        };


        $r = [
        'code' => 500,
        'status' => 'Internal Server Error',
        'data' => 'Something went wrong when the server tried to process this request. Sorry!'
        ];

        return $c['response']
            ->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($r));
    };
};


/** App Settings End **/

/** Endpoint Definition ***/

require realpath(__DIR__) . '/../routes/archive/surah.php';
require realpath(__DIR__) . '/../routes/archive/ayah.php';
require realpath(__DIR__) . '/../routes/archive/juz.php';
require realpath(__DIR__) . '/../routes/archive/manzil.php';
require realpath(__DIR__) . '/../routes/archive/page.php';
require realpath(__DIR__) . '/../routes/archive/ruku.php';
require realpath(__DIR__) . '/../routes/archive/sajda.php';
require realpath(__DIR__) . '/../routes/archive/hizbQuarter.php';
require realpath(__DIR__) . '/../routes/archive/edition.php';
require realpath(__DIR__) . '/../routes/archive/quran.php';
require realpath(__DIR__) . '/../routes/archive/search.php';
require realpath(__DIR__) . '/../routes/archive/other.php';
require realpath(__DIR__) . '/../routes/v1/surah.php';
require realpath(__DIR__) . '/../routes/v1/ayah.php';
require realpath(__DIR__) . '/../routes/v1/juz.php';
require realpath(__DIR__) . '/../routes/v1/manzil.php';
require realpath(__DIR__) . '/../routes/v1/page.php';
require realpath(__DIR__) . '/../routes/v1/ruku.php';
require realpath(__DIR__) . '/../routes/v1/sajda.php';
require realpath(__DIR__) . '/../routes/v1/hizbQuarter.php';
require realpath(__DIR__) . '/../routes/v1/edition.php';
require realpath(__DIR__) . '/../routes/v1/quran.php';
require realpath(__DIR__) . '/../routes/v1/search.php';
require realpath(__DIR__) . '/../routes/v1/other.php';
require realpath(__DIR__) . '/../routes/v1/status.php';

$app->run();
