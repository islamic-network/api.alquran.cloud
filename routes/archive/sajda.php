<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Quran\Helper\Log;
use Quran\Helper\Request as ApiRequest;

$app->get('/sajda', function (Request $request, Response $response) {

    $edition = 'quran-simple';
    $sajda = new Quran\Api\SajdaResponse($edition);
    $this->logger->addInfo('sajda ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));

    return $response->withJson($sajda->get(), $sajda->getCode());
});

$app->get('/sajda/{edition}', function (Request $request, Response $response) {

    $edition = $request->getAttribute('edition');
    $sajda = new Quran\Api\SajdaResponse($edition);
    $this->logger->addInfo('sajda ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));

    return $response->withJson($sajda->get(), $sajda->getCode());
});
