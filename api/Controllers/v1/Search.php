<?php

namespace Api\Controllers\v1;

use Api\Controllers\AlQuranController;
use Mamluk\Kipchak\Components\Http;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Api\Models\SearchResponse;

/**
 * All Controllers extending Controllers\Slim Contain the Service / DI Container as a protected property called $container.
 * Access it using $this->container in your controller.
 * Default objects bundled into a container are:
 * logger - which returns an instance of \Monolog\Logger. This is also a protected property on your controller. Access it using $this->logger.
 */

class Search extends AlQuranController
{

    public function getWord(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $word = urldecode(Http\Request::getAttribute($request, 'word'));

        $result = $this->mc->get(md5('search_' . $word), function (ItemInterface $item) use ($word) {
            $item->expiresAfter(604800);
            $s = new SearchResponse($this->em, $word);

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

    public function getWordSurah(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $word = urldecode(Http\Request::getAttribute($request, 'word'));
        $surah = Http\Request::getAttribute($request, 'surah');

        $result = $this->mc->get(md5('search_' . $word . '_' . $surah), function (ItemInterface $item) use ($word, $surah) {
            $item->expiresAfter(604800);
            $s = new SearchResponse($this->em, $word, $surah);

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

    public function getWordSurahLanguage(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $word = urldecode(Http\Request::getAttribute($request, 'word'));
        $surah = Http\Request::getAttribute($request, 'surah');
        $language = Http\Request::getAttribute($request, 'language');

        $result = $this->mc->get(md5('search_' . $word . '_' . $surah . '_' . $language), function (ItemInterface $item) use ($word, $surah, $language) {
            $item->expiresAfter(604800);
            $s = new SearchResponse($this->em, $word, $surah, $language);

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

}