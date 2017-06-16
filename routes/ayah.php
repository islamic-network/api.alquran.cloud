<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Quran\Helper\Log;
use Quran\Helper\Request as ApiRequest;

// With Ayat Number or All
$app->get('/ayah/random', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = rand(1, 6326);
    $edition = 'quran-simple';
    if ($number == 'all') {
        $ayat = new Quran\Api\AyatResponse($number, $edition, true);
    } else {
        $ayat = new Quran\Api\AyatResponse($number, $edition);
    }
    $this->logger->addInfo('ayah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));

    return $response->withJson($ayat->get(), $ayat->getCode());
});

// With Ayat Number AND editions
$app->get('/ayah/random/editions', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = rand(1, 6326);
    $editions = ['quran-simple'];
    $ayats = [];
    if ($editions) {
        foreach ($editions as $edition) {
            $ayat = new Quran\Api\AyatResponse($number, $edition);
            $ayats[] = $ayat->get()->data;
        }
    }
    $this->logger->addInfo('ayah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));    

    $r = $ayat->get();
    $r->data = $ayats;
    return $response->withJson($r, $ayat->getCode());
});

// With Ayat Number or All AND edition
$app->get('/ayah/random/{edition}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = rand(1, 6326);
    $edition = $request->getAttribute('edition');
    if ($number == 'all') {
        $ayat = new Quran\Api\AyatResponse($number, $edition, true);
    } else {
        $ayat = new Quran\Api\AyatResponse($number, $edition);
    }
    $this->logger->addInfo('ayah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));    

    return $response->withJson($ayat->get(), $ayat->getCode());
});


// With Ayat Number AND editions
$app->get('/ayah/random/editions/{editions}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = rand(1, 6326);
    $editions = ApiRequest::editions($request->getAttribute('editions'));
    $ayats = [];
    if ($editions) {
        foreach ($editions as $edition) {
            $ayat = new Quran\Api\AyatResponse($number, $edition);
            $ayats[] = $ayat->get()->data;
        }
    }
    $this->logger->addInfo('ayah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));    

    $r = $ayat->get();
    $r->data = $ayats;
    return $response->withJson($r, $ayat->getCode());
});

// With Ayat Number or All
$app->get('/ayah/{number}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $edition = 'quran-simple';
    if ($number == 'all') {
        $ayat = new Quran\Api\AyatResponse($number, $edition, true);
    } else {
        $ayat = new Quran\Api\AyatResponse($number, $edition);
    }
    $this->logger->addInfo('ayah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));

    return $response->withJson($ayat->get(), $ayat->getCode());
});

// With Ayat Number AND editions
$app->get('/ayah/{number}/editions', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $editions = ['quran-simple'];
    $ayats = [];
    if ($editions) {
        foreach ($editions as $edition) {
            $ayat = new Quran\Api\AyatResponse($number, $edition);
            $ayats[] = $ayat->get()->data;
        }
    }
    $this->logger->addInfo('ayah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));    

    $r = $ayat->get();
    $r->data = $ayats;
    return $response->withJson($r, $ayat->getCode());
}); 

// With Ayat Number or All AND edition
$app->get('/ayah/{number}/{edition}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $edition = $request->getAttribute('edition');
    if ($number == 'all') {
        $ayat = new Quran\Api\AyatResponse($number, $edition, true);
    } else {
        $ayat = new Quran\Api\AyatResponse($number, $edition);
    }
    $this->logger->addInfo('ayah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));    
    
    return $response->withJson($ayat->get(), $ayat->getCode());
}); 


// With Ayat Number AND editions
$app->get('/ayah/{number}/editions/{editions}', function (Request $request, Response $response) {
    $this->alquranAutoLoader;
    $number = $request->getAttribute('number');
    $editions = ApiRequest::editions($request->getAttribute('editions'));
    $ayats = [];
    if ($editions) {
        foreach ($editions as $edition) {
            $ayat = new Quran\Api\AyatResponse($number, $edition);
            $ayats[] = $ayat->get()->data;
        }
    }
    $this->logger->addInfo('ayah ::: ' . time() . ' ::', Log::format($_SERVER, $_REQUEST));    

    $r = $ayat->get();
    $r->data = $ayats;
    return $response->withJson($r, $ayat->getCode());
}); 
