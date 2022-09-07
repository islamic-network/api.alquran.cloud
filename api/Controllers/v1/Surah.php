<?php

namespace Api\Controllers\v1;

use Api\Controllers\AlQuranController;
use Api\Models\SuratResponse;
use Api\Utils\Request;
use Mamluk\Kipchak\Components\Http;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * All Controllers extending Controllers\Slim Contain the Service / DI Container as a protected property called $container.
 * Access it using $this->container in your controller.
 * Default objects bundled into a container are:
 * logger - which returns an instance of \Monolog\Logger. This is also a protected property on your controller. Access it using $this->logger.
 */

class Surah extends AlQuranController
{

    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $result = $this->mc->get('surahs', function (ItemInterface $item) {
            $item->expiresAfter(604800);
            $s = new SuratResponse($this->em, null, null);

            return [
                $s->get(),
                $s->getCode()
            ];

        });

        return Http\Response::json($response,
            $result[0],
            $result[1],
            true,
            86400
        );
    }

    public function getByNumber(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $number = Http\Request::getAttribute($request, 'number');
        $edition = 'quran-uthmani-quran-academy';
        $limit = Http\Request::getQueryParam($request, 'limit');
        $offset = Http\Request::getQueryParam($request, 'offset');

        $result = $this->mc->get(md5('surah_' . $number . '_' . $edition . '_' . $limit . '_' . $offset), function (ItemInterface $item) use ($number, $edition, $limit, $offset) {
            $item->expiresAfter(604800);
            $s = new SuratResponse($this->em, $number, true, $edition, true, $offset, $limit);

            return [
                $s->get(),
                $s->getCode()
            ];

        });

        return Http\Response::json($response,
            $result[0],
            $result[1],
            true,
            86400
        );
    }

    public function getOneByNumberAndEdition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $number = Http\Request::getAttribute($request, 'number');
        $edition = Http\Request::getAttribute($request, 'edition');
        $limit = Http\Request::getQueryParam($request, 'limit');
        $offset = Http\Request::getQueryParam($request, 'offset');

        $result = $this->mc->get(md5('surah_' . $number . '_' . $edition . '_' . $limit . '_' . $offset), function (ItemInterface $item) use ($number, $edition, $limit, $offset) {
            $item->expiresAfter(604800);
            $s = new SuratResponse($this->em, $number, true, $edition, true, $offset, $limit);

            return [
                $s->get(),
                $s->getCode()
            ];

        });

        return Http\Response::json($response,
            $result[0],
            $result[1],
            true,
            86400
        );
    }

    public function getManyByNumberAndEdition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $number = Http\Request::getAttribute($request, 'number');
        $editions = Request::editions(Http\Request::getAttribute($request, 'editions'));
        $limit = Http\Request::getQueryParam($request, 'limit');
        $offset = Http\Request::getQueryParam($request, 'offset');

        $result = $this->mc->get(md5('surah_' . $number . '_' . json_encode($editions) . '_' . $limit . '_' . $offset), function (ItemInterface $item) use ($number, $editions, $limit, $offset) {
            $item->expiresAfter(604800);

            $surats = [];
            if ($editions) {
                foreach ($editions as $edition) {
                    $s = new SuratResponse($this->em, $number, true, $edition, true, $offset, $limit);
                    $surats[] = $s->get();
                }
            }

            return [
                $surats,
                $s->getCode()
            ];

        });

        return Http\Response::json($response,
            $result[0],
            $result[1],
            true,
            86400
        );
    }

}