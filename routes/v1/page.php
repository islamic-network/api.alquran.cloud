<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Quran\Helper\Log;
use Quran\Helper\Request as ApiRequest;

$app->group('/v1', function() {
    $this->get('/page/{number}', function (Request $request, Response $response) {

        $number = $request->getAttribute('number');
        $offset = $request->getQueryParam('offset');
        $limit = $request->getQueryParam('limit');
        $edition = 'quran-simple';
        $page = new Quran\Api\PageResponse($number, $edition, $offset, $limit);
        // $this->logger->addInfo('page ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));

        return $response->withJson($page->get(), $page->getCode());
    });

    $this->get('/page/{number}/{edition}', function (Request $request, Response $response) {

        $number = $request->getAttribute('number');
        $edition = $request->getAttribute('edition');
        $offset = $request->getQueryParam('offset');
        $limit = $request->getQueryParam('limit');
        $page = new Quran\Api\PageResponse($number, $edition, $offset, $limit);
        // $this->logger->addInfo('page ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));

        return $response->withJson($page->get(), $page->getCode());
    });
});
