<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Quran\Helper\Log;
use Quran\Helper\Request as ApiRequest;

$app->get('/hizbQuarter/{number}', function (Request $request, Response $response) {

    $number = $request->getAttribute('number');
    $offset = $request->getQueryParam('offset');
    $limit = $request->getQueryParam('limit');
    $edition = 'quran-simple';
    $hizbQuarter = new Quran\Api\HizbQuarterResponse($number, $edition, $offset, $limit);
    // $this->logger->addInfo('hizbQuarter ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));

    return $response->withJson($hizbQuarter->get(), $hizbQuarter->getCode());
});

$app->get('/hizbQuarter/{number}/{edition}', function (Request $request, Response $response) {

    $number = $request->getAttribute('number');
    $edition = $request->getAttribute('edition');
    $offset = $request->getQueryParam('offset');
    $limit = $request->getQueryParam('limit');
    $hizbQuarter = new Quran\Api\HizbQuarterResponse($number, $edition, $offset, $limit);
    // $this->logger->addInfo('hizbQuarter ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));

    return $response->withJson($hizbQuarter->get(), $hizbQuarter->getCode());
});
