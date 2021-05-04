<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Quran\Helper\Response as ApiResponse;
use \Quran\Helper\Cacher;
use \Quran\Helper\Database;

$app->group('/v1', function() {
    // With Ayat Number or All
    $this->get('/status', function (Request $request, Response $response) {
        $mc = new Cacher();
        $dbx = new Database();
        $dbResult = false;
        try {
            $db = $dbx->getConnection('database');
            $dbResult = $db->fetchAssoc("SELECT id FROM ayat WHERE id = ? ", [7]);
        } catch (Exception $e) {
            $dbResult = false;
        }

        $status = [
            'memcached' => $mc === false ? 'NOT OK' : 'OK',
            'database' => $dbResult === false ? 'NOT OK' : 'OK (' . $dbResult['id']. ')',
        ];
        if ($mc === false || $dbResult === false) {
            return $response->withJson(ApiResponse::build($status, 500, 'Status Check Failed'), 500);
        }
        return $response->withJson(ApiResponse::build($status, 200, 'OK'), 200);

    });

    $this->get('/liveness', function (Request $request, Response $response) {
        return $response->withJson('OK', 200);
    });

});
