<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Quran\Helper\Log;
use Quran\Helper\Request as ApiRequest;

$app->group('/v1', function() {
    $this->get('/meta', function (Request $request, Response $response) {
        $this->logger->addInfo('meta ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));

        $meta = new Quran\Api\MetaResponse();
        return $response->withJson($meta->get(), $meta->getCode());
    });
});
