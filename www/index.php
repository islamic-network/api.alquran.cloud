<?php
header('Access-Control-Allow-Origin: *');

// Main AlQuran autoloader
require realpath(__DIR__) . '/../config/autoloader.php';

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

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
            ->withHeader('Content-Type', 'text/html')
            ->write($resp);
    };
};

/** App Settings End **/

/** Endpoint Definition ***/

/** Surat **/
// Without Surat Number
$app->get('/surah', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $edition = 'quran-simple';
    $surat = new Quran\Api\SuratResponse();
    $json = $response->withJson($surat->get(), $surat->getCode());
    $this->logger->addInfo('surah ::: ' . time(), ['server' => $_SERVER, 'request' => $_REQUEST, 'response' => $json]);
    
    return $json;
});

// With Surat Number
$app->get('/surah/{number}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $edition = 'quran-simple';
    $surat = new Quran\Api\SuratResponse($number, true, $edition, true);
    $json = $response->withJson($surat->get(), $surat->getCode());
    $this->logger->addInfo('surah ::: ' . time(), ['server' => $_SERVER, 'request' => $_REQUEST, 'response' => $json]);
    
    return $json;
});

// With Surat Number and edition
$app->get('/surah/{number}/{edition}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $edition = $request->getAttribute('edition');
    $surat = new Quran\Api\SuratResponse($number, true, $edition, true);
    $json = $response->withJson($surat->get(), $surat->getCode());
    $this->logger->addInfo('surah ::: ' . time(), ['server' => $_SERVER, 'request' => $_REQUEST, 'response' => $json]);
    
    return $json;
});
                                                   
/** Ayat **/ 

// With Ayat Number or All
$app->get('/ayah/{number}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $edition = 'quran-simple';
    if ($number == 'all') {
        $ayat = new Quran\Api\AyatResponse($number, $edition, true);
    } else {
        $ayat = new Quran\Api\AyatResponse($number, $edition);
    }
    $json = $response->withJson($ayat->get(), $ayat->getCode());
    $this->logger->addInfo('ayah ::: ' . time(), ['server' => $_SERVER, 'request' => $_REQUEST, 'response' => $json]);
    
    
    return $json;
});
// With Ayat Number or All AND edition
$app->get('/ayah/{number}/{edition}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $edition = $request->getAttribute('edition');
    if ($number == 'all') {
        $ayat = new Quran\Api\AyatResponse($number, $edition, true);
    } else {
        $ayat = new Quran\Api\AyatResponse($number, $edition);
    }
    $json = $response->withJson($ayat->get(), $ayat->getCode());
    $this->logger->addInfo('ayah ::: ' . time(), ['server' => $_SERVER, 'request' => $_REQUEST, 'response' => $json]);
    
    
    return $json;
}); 


/** Juz **/
$app->get('/juz/{number}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $juz = new Quran\Api\JuzResponse($number);
    $json = $response->withJson($juz->get(), $juz->getCode());
    $this->logger->addInfo('juz ::: ' . time(), ['server' => $_SERVER, 'request' => $_REQUEST, 'response' => $json]);
    
    return $json;
});

$app->get('/juz/{number}/{edition}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $edition = $request->getAttribute('edition');
    $juz = new Quran\Api\JuzResponse($number, $edition);
    $json = $response->withJson($juz->get(), $juz->getCode());
    $this->logger->addInfo('juz ::: ' . time(), ['server' => $_SERVER, 'request' => $_REQUEST, 'response' => $json]);
    
    return $json;
});

/** Edition **/
// Without Edition Number or Type
$app->get('/edition', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $edition = new Quran\Api\EditionResponse();
    $json = $response->withJson($edition->get(), $edition->getCode());
    $this->logger->addInfo('edition ::: ' . time(), ['server' => $_SERVER, 'request' => $_REQUEST, 'response' => $json]);
    
    return $json;
});

// Edition Types
$app->get('/edition/type', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $type = $request->getAttribute('type');
    $json = $response->withJson(['status' => 'OK', 'code' => 200, 'data' => ['tafsir', 'translation', 'quran', 'transliteration']], 200);
    $this->logger->addInfo('edition ::: ' . time(), ['server' => $_SERVER, 'request' => $_REQUEST, 'response' => $json]);
    
    return $json;
});

$app->get('/edition/type/{type}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $type = $request->getAttribute('type');
    $edition = new Quran\Api\EditionResponse(null, $type);
    $json = $response->withJson($edition->get(), $edition->getCode());
    $this->logger->addInfo('edition ::: ' . time(), ['server' => $_SERVER, 'request' => $_REQUEST, 'response' => $json]);
    
    return $json;
});

// Edition Languages
$app->get('/edition/language', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $json = $response->withJson(['status' => 'OK', 'code' => 200, 'data' => ['fa','fr','ha','hi','id','it','ja','ko','ku','ml','nl','no','pl','pt','ro','ru','sd','so']], 200);
    $this->logger->addInfo('edition ::: ' . time(), ['server' => $_SERVER, 'request' => $_REQUEST, 'response' => $json]);
    
    return $json;
});

$app->get('/edition/language/{lang}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $lang = $request->getAttribute('lang');
    $edition = new Quran\Api\EditionResponse(null, null, $lang);
    $json = $response->withJson($edition->get(), $edition->getCode());
    $this->logger->addInfo('edition ::: ' . time(), ['server' => $_SERVER, 'request' => $_REQUEST, 'response' => $json]);
    
    return $json;
});

/** Complete **/
// Without edition
$app->get('/quran', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $edition = 'quran-simple';
    $quran = new Quran\Api\CompleteResponse($edition);
    $json = $response->withJson($quran->get(), $quran->getCode());
    $this->logger->addInfo('edition ::: ' . time(), ['server' => $_SERVER, 'request' => $_REQUEST, 'response' => $json]);
    
    return $json;
});

// With edition
$app->get('/quran/{edition}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $edition = $request->getAttribute('edition');
    $quran = new Quran\Api\CompleteResponse($edition);
    $json = $response->withJson($quran->get(), $quran->getCode());
    $this->logger->addInfo('edition ::: ' . time(), ['server' => $_SERVER, 'request' => $_REQUEST, 'response' => $json]);
    
    return $json;
});

$app->get('/search/{word}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $word = $request->getAttribute('word');
    $search = new Quran\Api\SearchResponse($word);
    $json = $response->withJson($search->get(), $search->getCode());
    $this->logger->addInfo('edition ::: ' . time(), ['server' => $_SERVER, 'request' => $_REQUEST, 'response' => $json]);
    
    return $json;
});

$app->get('/search/{word}/{surah}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $word = $request->getAttribute('word');
    $surat = $request->getAttribute('surah');
    $search = new Quran\Api\SearchResponse($word, $surat);
    $json = $response->withJson($search->get(), $search->getCode());
    $this->logger->addInfo('edition ::: ' . time(), ['server' => $_SERVER, 'request' => $_REQUEST, 'response' => $json]);
    
    return $json;
});

$app->get('/search/{word}/{surah}/{language}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $word = $request->getAttribute('word');
    $surat = $request->getAttribute('surah');
    $language = $request->getAttribute('language');
    $search = new Quran\Api\SearchResponse($word, $surat, $language);
    $json = $response->withJson($search->get(), $search->getCode());
    $this->logger->addInfo('edition ::: ' . time(), ['server' => $_SERVER, 'request' => $_REQUEST, 'response' => $json]);
    
    return $json;
});

                                                   
$app->run();
