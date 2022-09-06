<?php

namespace Api\Controllers\v1;

use Api\Controllers\AlQuranController;
use Mamluk\Kipchak\Components\Http;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Api\Models\CompleteResponse;

/**
 * All Controllers extending Controllers\Slim Contain the Service / DI Container as a protected property called $container.
 * Access it using $this->container in your controller.
 * Default objects bundled into a container are:
 * logger - which returns an instance of \Monolog\Logger. This is also a protected property on your controller. Access it using $this->logger.
 */

class Quran extends AlQuranController
{

    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $edition = 'quran-uthmani-quran-academy';

        $result = $this->mc->get('quran_' . $edition, function (ItemInterface $item) use ($edition) {
            $item->expiresAfter(604800);
            $q = new CompleteResponse($this->em, $edition);

            return [
                $q->get(),
                $q->getCode()
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

        $edition = Http\Request::getAttribute($request, 'edition');

        $result = $this->mc->get('quran_' . $edition, function (ItemInterface $item) use ($edition) {
            $item->expiresAfter(604800);
            $q = new CompleteResponse($this->em, $edition);

            return [
                $q->get(),
                $q->getCode()
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