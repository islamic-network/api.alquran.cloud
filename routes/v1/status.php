<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Quran\Helper\Log;
use Quran\Helper\Request as ApiRequest;

$app->group('/v1', function() {
    // With Ayat Number or All
    $this->get('/status', function (Request $request, Response $response) {

        $number = rand(1, 6326);
        $edition = 'quran-simple';
        $ayat = new Quran\Api\AyatResponse($number, $edition);
        // $this->logger->addInfo('ayah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));

        return $response->withJson($ayat->get(), $ayat->getCode());
    });

});
