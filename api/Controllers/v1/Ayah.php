<?php

namespace Api\Controllers\v1;

use Api\Controllers\AlQuranController;
use Api\Utils\Request;
use Mamluk\Kipchak\Components\Http;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Api\Models\AyatResponse;
use Api\Utils\Ayah as AyahUtil;

/**
 * All Controllers extending Controllers\Slim Contain the Service / DI Container as a protected property called $container.
 * Access it using $this->container in your controller.
 * Default objects bundled into a container are:
 * logger - which returns an instance of \Monolog\Logger. This is also a protected property on your controller. Access it using $this->logger.
 */

class Ayah extends AlQuranController
{

    public function getRandom(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $number = rand(1, 6326);
        $edition = 'quran-uthmani-quran-academy';

        $result = $this->mc->get(md5('ayah_' . $number . '_'. $edition), function (ItemInterface $item) use ($number, $edition) {
            $item->expiresAfter(604800);
            $a = new AyatResponse($this->em, $number, $edition);

            return [
                $a->get(),
                $a->getCode()
            ];

        });

        return Http\Response::json($response,
            $result[0],
            $result[1],
            true,
            86400
        );
    }

    public function getRandomEdition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $number = rand(1, 6326);
        $edition = Http\Request::getAttribute($request, 'edition');

        $result = $this->mc->get(md5('ayah_' . $number . '_' . $edition), function (ItemInterface $item) use ($number, $edition) {
            $item->expiresAfter(604800);
            $a = new AyatResponse($this->em, $number, $edition);

            return [
                $a->get(),
                $a->getCode()
            ];

        });

        return Http\Response::json($response,
            $result[0],
            $result[1],
            true,
            86400
        );
    }

    public function getRandomEditions(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $number = rand(1, 6326);

        return AyahUtil::getMultipleEditions($request, $number, $response, $this->mc, $this->em);
    }

    public function getOneByNumber(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $number = Http\Request::getAttribute($request, 'number');
        $edition = 'quran-uthmani-quran-academy';

        $result = $this->mc->get(md5('ayah_' . $number . '_'. $edition), function (ItemInterface $item) use ($number, $edition) {
            $item->expiresAfter(604800);
            if ($number == 'all') {
                $a = new AyatResponse($this->em, $number, $edition, true);
            } else {
                $a = new AyatResponse($this->em, $number, $edition);
            }

            return [
                $a->get(),
                $a->getCode()
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

        $result = $this->mc->get(md5('ayah_' . $number.'_'. $edition), function (ItemInterface $item) use ($number, $edition) {
            $item->expiresAfter(604800);
            if ($number == 'all') {
                $a = new AyatResponse($this->em, $number, $edition, true);
            } else {
                $a = new AyatResponse($this->em, $number, $edition);
            }

            return [
                $a->get(),
                $a->getCode()
            ];

        });

        return Http\Response::json($response,
            $result[0],
            $result[1],
            true,
            86400
        );
    }

    public function getManyByNumberAndEditions(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $number = Http\Request::getAttribute($request, 'number');

        return AyahUtil::getMultipleEditions($request, $number, $response, $this->mc, $this->em);
    }



}