<?php

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
