<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Quran\Helper\Log;
use Quran\Helper\Request as ApiRequest;

$app->get('/ruku/{number}', function (Request $request, Response $response) {

    $number = $request->getAttribute('number');
    $offset = $request->getQueryParam('offset');
    $limit = $request->getQueryParam('limit');
    $edition = 'quran-simple';
    $ruku = new Quran\Api\RukuResponse($number, $edition, $offset, $limit);
    $this->logger->addInfo('ruku ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));

    return $response->withJson($ruku->get(), $ruku->getCode());
});

$app->get('/ruku/{number}/{edition}', function (Request $request, Response $response) {

    $number = $request->getAttribute('number');
    $edition = $request->getAttribute('edition');
    $offset = $request->getQueryParam('offset');
    $limit = $request->getQueryParam('limit');
    $ruku = new Quran\Api\RukuResponse($number, $edition, $offset, $limit);
    $this->logger->addInfo('ruku ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));

    return $response->withJson($ruku->get(), $ruku->getCode());
});
