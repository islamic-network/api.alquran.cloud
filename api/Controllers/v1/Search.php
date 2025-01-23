<?php

namespace Api\Controllers\v1;

use Api\Controllers\AlQuranController;
use Mamluk\Kipchak\Components\Http;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Api\Models\SearchResponse;
use OpenApi\Attributes as OA;

/**
 * All Controllers extending Controllers\Slim Contain the Service / DI Container as a protected property called $container.
 * Access it using $this->container in your controller.
 * Default objects bundled into a container are:
 * logger - which returns an instance of \Monolog\Logger. This is also a protected property on your controller. Access it using $this->logger.
 */


class Search extends AlQuranController
{

    #[OA\Get(
        path: '/search/{word}',
        description: "Returns ayahs that match the specified 'word' parameter in a given edition or language. 
        <br />If you do not specify an edition or language, all english language texts are searched.
        <br />(Text) In this example all ayahs that contain the word 'Path' are returned in all the english editions from all Surahs / Chapters.",
        summary: "Returns all ayahs that contain the specified 'word' parameter in all the english editions",
        tags: ['Search'],
        parameters: [
            new OA\PathParameter(ref: '#/components/parameters/SearchWordParameter')
        ],
        responses: [
            new OA\Response(response: '200', description: "Returns all ayahs that contain the word 'Path' in all the english editions from all Surahs / Chapters.",
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200SearchSahihResponse', type: 'object')
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404SearchResponse', response: '404')
        ]
    )]

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

    #[OA\Get(
        path: '/search/{word}/{surah}',
        description: "Returns ayahs that match the specified 'word' parameter from the specified 'surah' parameter in a given edition or language. 
        <br />If you do not specify an edition or language, all english language texts are searched.
        <br />(Text) In this example all ayahs that contain the word 'Path' are returned in all the english editions from the specified chapter / surah '1'.",
        summary: "Returns all ayahs that contain the specified 'word' parameter from the specified 'surah' parameter in all the english editions",
        tags: ['Search'],
        parameters: [
            new OA\PathParameter(ref: '#/components/parameters/SearchWordParameter'),
            new OA\PathParameter(ref: '#/components/parameters/SearchSurahParameter')
        ],
        responses: [
            new OA\Response(response: '200', description: "Returns all ayahs that contain the word 'Path' from the specified surah '1' in all the english editions.",
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200SearchSahihResponse', type: 'object')
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404SearchResponse', response: '404')
        ]
    )]

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

    #[OA\Get(
        path: '/search/{word}/{surah}/{language}',
        description: "Returns ayahs that match the specified 'word' parameter from the specified 'surah' parameter in a specified language / edition identifier from 'language' parameter. 
        <br />If you do not specify an edition or language, all english language texts are searched.
        <br />(Text) In this example all ayahs that contain the word 'Path' are returned in the specified language / edition identifier 'en' or 'en.sahih' from the specified chapter / surah '1'.",
        summary: "Returns all ayahs that contain the specified 'word' parameter from the specified 'surah' parameter in the specified language / edition identifier 'language' parameter",
        tags: ['Search'],
        parameters: [
            new OA\PathParameter(ref: '#/components/parameters/SearchWordParameter'),
            new OA\PathParameter(ref: '#/components/parameters/SearchSurahParameter'),
            new OA\PathParameter(name: 'language', description: "Enter a language / edition identifier Example: 'en' or 'en.sahih' to search the text in the specified language or edition.
            <br />If specifying language please remember 'language' parameter is 2 digit alpha language code. Example: en for English, fr for french
            ", in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'en.sahih' )
        ],
        responses: [
            new OA\Response(response: '200', description: "Returns all ayahs that contain the word 'Path' from the specified surah '1' in the specified language / edition identifier 'en' or 'en.sahih'.",
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200SearchSahihResponse', type: 'object')
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404SearchResponse', response: '404')
        ]
    )]

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