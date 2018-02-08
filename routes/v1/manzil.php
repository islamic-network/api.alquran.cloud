<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Quran\Helper\Log;
use Quran\Helper\Request as ApiRequest;

$app->group('/v1', function() {
    $this->get('/manzil/{number}', function (Request $request, Response $response) {

        $number = $request->getAttribute('number');
        $offset = $request->getQueryParam('offset');
        $limit = $request->getQueryParam('limit');
        $edition = 'quran-simple';
        $manzil = new Quran\Api\ManzilResponse($number, $edition, $offset, $limit);
        $this->logger->addInfo('manzil ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));

        return $response->withJson($manzil->get(), $manzil->getCode());
    });

    $this->get('/manzil/{number}/{edition}', function (Request $request, Response $response) {

        $number = $request->getAttribute('number');
        $edition = $request->getAttribute('edition');
        $offset = $request->getQueryParam('offset');
        $limit = $request->getQueryParam('limit');
        $manzil = new Quran\Api\ManzilResponse($number, $edition, $offset, $limit);
        $this->logger->addInfo('manzil ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));

        return $response->withJson($manzil->get(), $manzil->getCode());
    });
});
