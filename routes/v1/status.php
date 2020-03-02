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
        $db2Result = false;
        $db3Result = false;
        try {
            $db = $dbx->getConnection('database_pxc_1');
            $dbResult = $db->fetchAssoc("SELECT id FROM ayat WHERE id = ? ", [7]);
        } catch (Exception $e) {
            $dbResult = false;
        }
        try {
            $db2 = $dbx->getConnection('database_pxc_2');
            $db2Result = $db2->fetchAssoc("SELECT id FROM ayat WHERE id = ? ", [77]);
        } catch (Exception $e) {
            $db2Result = false;
        }
        try {
            $db3 = $dbx->getConnection('database_pxc_3');
            $db3Result = $db3->fetchAssoc("SELECT id FROM ayat WHERE id = ? ", [777]);
        } catch (Exception $e) {
            $db3Result = false;
        }
        if ($mc !== false) {
            if ($dbResult !== false) {
                $mc->set('DB_CONNECTION', 'database_pxc_1');
            } elseif ($db2Result !== false && $dbResult === false) {
                $mc->set('DB_CONNECTION', 'database_pxc_2');
            } elseif ($db3Result !== false && $db2Result === false && $dbResult === false) {
                $mc->set('DB_CONNECTION', 'database_pxc_3');
            }
        }

        $status = [
            'memcached' => $mc === false ? 'NOT OK' : 'OK',
            'pxc1' => $dbResult === false ? 'NOT OK' : 'OK (' . $dbResult['id']. ')',
            'pxc2' => $db2Result === false ? 'NOT OK' : 'OK (' . $db2Result['id']. ')',
            'pxc3' => $db3Result === false ? 'NOT OK' : 'OK (' . $db3Result['id']. ')',
            'activeDb' => $mc === false ? 'NOT OK' : $mc->get('DB_CONNECTION')
        ];
        if ($mc === false || $dbResult === false || $db2Result === false || $db3Result === false) {
            return $response->withJson(ApiResponse::build($status, 500, 'Status Check Failed'), 500);
        }
        return $response->withJson(ApiResponse::build($status, 200, 'OK'), 200);

    });

});
