<?php
header('Access-Control-Allow-Origin: *');

// Main AlQuran autoloader
require realpath(__DIR__) . '/../config/autoloader.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Quran\Helper\Log;
use Quran\Helper\Request as ApiRequest;

error_reporting(E_ALL);
ini_set('display_errors', 1);

/** App settings **/
$config['displayErrorDetails'] = true;

$app = new \Slim\App(["settings" => $config]);

$container = $app->getContainer();
$container['logger'] = function($c) {
    $logId = uniqid();
    $logStamp = time();
    $logFile = date('Y-m-d', $logStamp);
    // Create the logger
    $logger = new Logger('QuranApi');
    // Now add some handlers
    $logger->pushHandler(new StreamHandler(__DIR__.'/../logs/' . $logFile . '.log', Logger::INFO));
    return $logger;
};
$container['alquranAutoLoader'] = function($c) {
    require realpath(__DIR__) . '/../config/doctrineBootstrap.php';
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

/** App Settings End **/

/** Endpoint Definition ***/

require realpath(__DIR__) . '/../routes/surah.php';
require realpath(__DIR__) . '/../routes/ayah.php';
require realpath(__DIR__) . '/../routes/juz.php';
require realpath(__DIR__) . '/../routes/edition.php';
require realpath(__DIR__) . '/../routes/quran.php';
require realpath(__DIR__) . '/../routes/search.php';

$app->run();
