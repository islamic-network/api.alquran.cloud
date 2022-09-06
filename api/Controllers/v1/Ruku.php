<?php

namespace Api\Controllers\v1;

use Api\Controllers\AlQuranController;
use Mamluk\Kipchak\Components\Http;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Api\Models\RukuResponse;

/**
 * All Controllers extending Controllers\Slim Contain the Service / DI Container as a protected property called $container.
 * Access it using $this->container in your controller.
 * Default objects bundled into a container are:
 * logger - which returns an instance of \Monolog\Logger. This is also a protected property on your controller. Access it using $this->logger.
 */

class Ruku extends AlQuranController
{

    public function getByNumber(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $number = Http\Request::getAttribute($request, 'number');
        $offset = Http\Request::getQueryParam($request, 'offset');
        $limit = Http\Request::getQueryParam($request, 'limit');
        $edition = 'quran-uthmani-quran-academy';

        $result = $this->mc->get('ruku_' . $number . '_'. $offset . '_' . $limit, function (ItemInterface $item) use ($number, $offset, $limit, $edition) {
            $item->expiresAfter(604800);
            $r = new RukuResponse($this->em, $number, $edition, $offset, $limit);

            return [
                $r->get(),
                $r->getCode()
            ];

        });

        return Http\Response::json($response,
            $result[0],
            $result[1],
            true,
            86400
        );

    }

    public function getByEdition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $number = Http\Request::getAttribute($request, 'number');
        $edition = Http\Request::getAttribute($request, 'edition');
        $offset = Http\Request::getQueryParam($request, 'offset');
        $limit = Http\Request::getQueryParam($request, 'limit');

        $result = $this->mc->get('ruku_' . $number . '_' . $edition . '_' . $offset . '_' . $limit, function (ItemInterface $item) use ($number, $offset, $limit, $edition) {
            $item->expiresAfter(604800);
            $r = new RukuResponse($this->em, $number, $edition, $offset, $limit);

            return [
                $r->get(),
                $r->getCode()
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