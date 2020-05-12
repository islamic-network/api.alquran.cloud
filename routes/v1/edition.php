<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Quran\Helper\Log;
use Quran\Helper\Request as ApiRequest;

$app->group('/v1', function() {
    // Without Edition Number or Type
    $this->get('/edition', function (Request $request, Response $response) {

        $type = $request->getQueryParam('type');
        $format = $request->getQueryParam('format');
        $language = $request->getQueryParam('language');
        $edition = new Quran\Api\EditionResponse(null, $type, $language, $format);
        // $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));

        return $response->withJson($edition->get(), $edition->getCode());
    });

    // Edition Types
    $this->get('/edition/type', function (Request $request, Response $response) {


        return $response->withJson(['status' => 'OK', 'code' => 200, 'data' => ['tafsir', 'translation', 'quran', 'transliteration', 'versebyverse']], 200);
    });

    $this->get('/edition/type/{type}', function (Request $request, Response $response) {

        $type = $request->getAttribute('type');
        $edition = new Quran\Api\EditionResponse(null, $type);

        return $response->withJson($edition->get(), $edition->getCode());
    });

    $this->get('/edition/format', function (Request $request, Response $response) {

        return $response->withJson(['status' => 'OK', 'code' => 200, 'data' => ['text', 'audio']], 200);
    });

    $this->get('/edition/format/{format}', function (Request $request, Response $response) {

        $format = $request->getAttribute('format');
        $edition = new Quran\Api\EditionResponse(null, null, null, $format);

        return $response->withJson($edition->get(), $edition->getCode());
    });

    // Edition Languages
    $this->get('/edition/language', function (Request $request, Response $response) {

        $languages = ['ar', 'az', 'bn', 'cs', 'de', 'dv', 'en', 'es', 'fa', 'fr','ha', 'hi', 'id', 'it', 'ja', 'ko', 'ku', 'ml', 'nl', 'no', 'pl', 'pt', 'ro', 'ru', 'sd', 'so', 'sq', 'sv', 'sw', 'ta', 'tg', 'th', 'tr', 'tt', 'ug', 'ur', 'uz'];

        return $response->withJson(['status' => 'OK', 'code' => 200, 'data' => $languages], 200);
    });

    $this->get('/edition/language/{lang}', function (Request $request, Response $response) {

        $lang = $request->getAttribute('lang');
        $edition = new Quran\Api\EditionResponse(null, null, $lang);

        return $response->withJson($edition->get(), $edition->getCode());
    });
});
