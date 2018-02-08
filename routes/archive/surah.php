<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Quran\Helper\Log;
use Quran\Helper\Request as ApiRequest;

// Without Surat Number
$app->get('/surah', function (Request $request, Response $response) {

    $number = $request->getAttribute('number');
    $edition = 'quran-simple';
    $surat = new Quran\Api\SuratResponse();
    $this->logger->addInfo('surah ::: ' . time() . ' :: ', Log::format($_SERVER, $_REQUEST));

    return $response->withJson($surat->get(), $surat->getCode());
});

// With Surat Number
$app->get('/surah/{number}', function (Request $request, Response $response) {

    $number = $request->getAttribute('number');
    $offset = $request->getQueryParam('offset');
    $limit = $request->getQueryParam('limit');
    $edition = 'quran-simple';
    $surat = new Quran\Api\SuratResponse($number, true, $edition, true, $offset, $limit);
    $this->logger->addInfo('surah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));

    return $response->withJson($surat->get(), $surat->getCode());
});

$app->get('/surah/{number}/editions', function (Request $request, Response $response) {

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

    $number = $request->getAttribute('number');
    $edition = $request->getAttribute('edition');
    $offset = $request->getQueryParam('offset');
    $limit = $request->getQueryParam('limit');
    $surat = new Quran\Api\SuratResponse($number, true, $edition, true, $offset, $limit);
    $this->logger->addInfo('surah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));

    return $response->withJson($surat->get(), $surat->getCode());
});

$app->get('/surah/{number}/editions/{editions}', function (Request $request, Response $response) {

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
