<?php

// Without edition
$app->get('/quran', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $edition = 'quran-simple';
    $quran = new Quran\Api\CompleteResponse($edition);
    $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($quran->get(), $quran->getCode());
});

// With edition
$app->get('/quran/{edition}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $edition = $request->getAttribute('edition');
    $quran = new Quran\Api\CompleteResponse($edition);
    $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($quran->get(), $quran->getCode());
});
