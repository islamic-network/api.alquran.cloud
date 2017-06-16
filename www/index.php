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

/** Surat **/
// Without Surat Number
$app->get('/surah', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $edition = 'quran-simple';
    $surat = new Quran\Api\SuratResponse();
    $this->logger->addInfo('surah ::: ' . time() . ' :: ', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($surat->get(), $surat->getCode());
});

// With Surat Number
$app->get('/surah/{number}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $offset = $request->getQueryParam('offset');
    $limit = $request->getQueryParam('limit');
    $edition = 'quran-simple';
    $surat = new Quran\Api\SuratResponse($number, true, $edition, true, $offset, $limit);
    $this->logger->addInfo('surah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($surat->get(), $surat->getCode());
});

$app->get('/surah/{number}/editions', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $offset = $request->getQueryParam('offset');
    $limit = $request->getQueryParam('limit');
    $editions = ['quran-simple'];
    $surats = [];
    if ($editions) {
        foreach ($editions as $edition) {
            $surat = new Quran\Api\SuratResponse($number, true, $edition, true, $offset, $limit);
            $surats[] = $surat->get()->data;
        }
    }
    $this->logger->addInfo('surah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    $r = $surat->get();
    $r->data = $surats;

    return $response->withJson($r, $surat->getCode());
});

// With Surat Number and edition
$app->get('/surah/{number}/{edition}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $edition = $request->getAttribute('edition');
    $offset = $request->getQueryParam('offset');
    $limit = $request->getQueryParam('limit');
    $surat = new Quran\Api\SuratResponse($number, true, $edition, true, $offset, $limit);
    $this->logger->addInfo('surah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($surat->get(), $surat->getCode());
});

$app->get('/surah/{number}/editions/{editions}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $offset = $request->getQueryParam('offset');
    $limit = $request->getQueryParam('limit');
    $editions = ApiRequest::editions($request->getAttribute('editions'));
    $surats = [];
    if ($editions) {
        foreach ($editions as $edition) {
            $surat = new Quran\Api\SuratResponse($number, true, $edition, true, $offset, $limit);
            $surats[] = $surat->get()->data;
        }
    }
    $this->logger->addInfo('surah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    $r = $surat->get();
    $r->data = $surats;

    return $response->withJson($r, $surat->getCode());
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
    $this->logger->addInfo('ayah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    
    return $response->withJson($ayat->get(), $ayat->getCode());
});

// With Ayat Number AND editions
$app->get('/ayah/{number}/editions', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $editions = ['quran-simple'];
    $ayats = [];
    if ($editions) {
        foreach ($editions as $edition) {
            $ayat = new Quran\Api\AyatResponse($number, $edition);
            $ayats[] = $ayat->get()->data;
        }
    }
    $this->logger->addInfo('ayah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));    

    $r = $ayat->get();
    $r->data = $ayats;
    return $response->withJson($r, $ayat->getCode());
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
    $this->logger->addInfo('ayah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));    
    
    return $response->withJson($ayat->get(), $ayat->getCode());
}); 


// With Ayat Number AND editions
$app->get('/ayah/{number}/editions/{editions}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $editions = ApiRequest::editions($request->getAttribute('editions'));
    $ayats = [];
    if ($editions) {
        foreach ($editions as $edition) {
            $ayat = new Quran\Api\AyatResponse($number, $edition);
            $ayats[] = $ayat->get()->data;
        }
    }
    $this->logger->addInfo('ayah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));    

    $r = $ayat->get();
    $r->data = $ayats;
    return $response->withJson($r, $ayat->getCode());
}); 

/** Juz **/
$app->get('/juz/{number}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $offset = $request->getQueryParam('offset');
    $limit = $request->getQueryParam('limit');
    $edition = 'quran-simple';
    $juz = new Quran\Api\JuzResponse($number, $edition, $offset, $limit);
    $this->logger->addInfo('juz ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($juz->get(), $juz->getCode());
});

$app->get('/juz/{number}/{edition}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $edition = $request->getAttribute('edition');
    $offset = $request->getQueryParam('offset');
    $limit = $request->getQueryParam('limit');
    $juz = new Quran\Api\JuzResponse($number, $edition, $offset, $limit);
    $this->logger->addInfo('juz ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($juz->get(), $juz->getCode());
});

/** Edition **/
// Without Edition Number or Type
$app->get('/edition', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $type = $request->getQueryParam('type');
    $format = $request->getQueryParam('format');
    $language = $request->getQueryParam('language');
    $edition = new Quran\Api\EditionResponse(null, $type, $language, $format);
    $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($edition->get(), $edition->getCode());
});

// Edition Types
$app->get('/edition/type', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson(['status' => 'OK', 'code' => 200, 'data' => ['tafsir', 'translation', 'quran', 'transliteration', 'versebyverse']], 200);
});

$app->get('/edition/type/{type}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $type = $request->getAttribute('type');
    $edition = new Quran\Api\EditionResponse(null, $type);
    $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($edition->get(), $edition->getCode());
});

$app->get('/edition/format', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson(['status' => 'OK', 'code' => 200, 'data' => ['text', 'audio']], 200);
});

$app->get('/edition/format/{format}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $format = $request->getAttribute('format');
    $edition = new Quran\Api\EditionResponse(null, null, null, $format);
    $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($edition->get(), $edition->getCode());
});

// Edition Languages
$app->get('/edition/language', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $languages = ['ar', 'az', 'bn', 'cs', 'de', 'dv', 'en', 'es', 'fa', 'fr','ha', 'hi', 'id', 'it', 'ja', 'ko', 'ku', 'ml', 'nl', 'no', 'pl', 'pt', 'ro', 'ru', 'sd', 'so', 'sq', 'sv', 'sw', 'ta', 'tg', 'th', 'tr', 'tt', 'ug', 'ur', 'uz'];
    $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson(['status' => 'OK', 'code' => 200, 'data' => $languages], 200);
});

$app->get('/edition/language/{lang}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $lang = $request->getAttribute('lang');
    $edition = new Quran\Api\EditionResponse(null, null, $lang);
    $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($edition->get(), $edition->getCode());
});

/** Complete **/
// Without edition
$app->get('/quran', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $edition = 'quran-simple';
    $quran = new Quran\Api\CompleteResponse($edition);
    $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($quran->get(), $quran->getCode());
});

// With edition
$app->get('/quran/{edition}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $edition = $request->getAttribute('edition');
    $quran = new Quran\Api\CompleteResponse($edition);
    $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($quran->get(), $quran->getCode());
});

$app->get('/search/{word}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $word = $request->getAttribute('word');
    $search = new Quran\Api\SearchResponse($word);
    $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($search->get(), $search->getCode());
});

$app->get('/search/{word}/{surah}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $word = $request->getAttribute('word');
    $surat = $request->getAttribute('surah');
    $search = new Quran\Api\SearchResponse($word, $surat);
    $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($search->get(), $search->getCode());
});

$app->get('/search/{word}/{surah}/{language}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $word = $request->getAttribute('word');
    $surat = $request->getAttribute('surah');
    $language = $request->getAttribute('language');
    $search = new Quran\Api\SearchResponse($word, $surat, $language);
    $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($search->get(), $search->getCode());
});

                                                   
$app->run();
