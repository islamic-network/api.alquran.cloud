<?php

namespace Api\Controllers\v1;

use Api\Controllers\AlQuranController;
use Api\Models\EditionResponse;
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

class Edition extends AlQuranController
{

    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $type = Http\Request::getQueryParam($request, 'type');
        $format = Http\Request::getQueryParam($request,'format');
        $language = Http\Request::getQueryParam($request,'language');

        $result = $this->mc->get(md5('edition_x_' . $type . '_'. $format . '_' . $language),
            function (ItemInterface $item) use ($type, $format, $language) {
            $item->expiresAfter(604800);
            $e = new EditionResponse($this->em, null, $type, $language, $format);

            return [
                $e->get(),
                $e->getCode()
            ];

        });

        return Http\Response::json($response,
            $result[0],
            $result[1],
            true,
            86400
        );

    }

    public function getTypes(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return Http\Response::json($response,
            [
                'tafsir', 'translation', 'quran', 'transliteration', 'versebyverse'
            ],
            200,
            true,
            86400
        );

    }

    public function getByType(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $type = Http\Request::getAttribute($request, 'type');

        $result = $this->mc->get(md5('edition_type_' . $type), function (ItemInterface $item) use ($type) {
                $item->expiresAfter(604800);
                $e = new EditionResponse($this->em,null, $type);

                return [
                    $e->get(),
                    $e->getCode()
                ];

            });

        return Http\Response::json($response,
            $result[0],
            $result[1],
            true,
            86400
        );

    }

    public function getFormats(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return Http\Response::json($response,
            ['text', 'audio'],
            200,
            true,
            86400
        );

    }

    public function getByFormat(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $format = Http\Request::getAttribute($request, 'format');


        $result = $this->mc->get(md5('edition_format_' . $format), function (ItemInterface $item) use ($format) {
            $item->expiresAfter(604800);
            $e = new EditionResponse($this->em,null, null, null, $format);

            return [
                $e->get(),
                $e->getCode()
            ];

        });

        return Http\Response::json($response,
            $result[0],
            $result[1],
            true,
            86400
        );

    }

    public function getLanguages(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return Http\Response::json($response,
            ['ar', 'am', 'az', 'ber', 'bn', 'cs', 'de', 'dv', 'en', 'es', 'fa', 'fr','ha', 'hi', 'id', 'it', 'ja', 'ko', 'ku', 'ml', 'nl', 'no', 'pl','ps', 'pt', 'ro', 'ru', 'sd', 'so', 'sq', 'sv', 'sw', 'ta', 'tg', 'th', 'tr', 'tt', 'ug', 'ur', 'uz'],
            200,
            true,
            86400
        );

    }

    public function getByLanguage(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $lang = $request->getAttribute('lang');

        $result = $this->mc->get(md5('edition_lang_' . $lang), function (ItemInterface $item) use ($lang) {
            $item->expiresAfter(604800);
            $e = new EditionResponse($this->em,null, null, $lang);

            return [
                $e->get(),
                $e->getCode()
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