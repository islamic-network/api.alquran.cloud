<?php

$app->get('/search/{word}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $word = $request->getAttribute('word');
    $search = new Quran\Api\SearchResponse($word);
    $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($search->get(), $search->getCode());
});

$app->get('/search/{word}/{surah}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $word = $request->getAttribute('word');
    $surat = $request->getAttribute('surah');
    $search = new Quran\Api\SearchResponse($word, $surat);
    $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($search->get(), $search->getCode());
});

$app->get('/search/{word}/{surah}/{language}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $word = $request->getAttribute('word');
    $surat = $request->getAttribute('surah');
    $language = $request->getAttribute('language');
    $search = new Quran\Api\SearchResponse($word, $surat, $language);
    $this->logger->addInfo('edition ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));
    
    return $response->withJson($search->get(), $search->getCode());
});
