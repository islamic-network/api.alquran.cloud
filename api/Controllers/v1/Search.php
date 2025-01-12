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

#[OA\OpenApi(
    openapi: '3.1.0',
    info: new OA\Info(
        version: 'v1',
        description: '<p><b />AlQuran API - Search</p>
        Search the text of the Holy Quran. Please note that only text editions of the Holy Quran are searchable.',
        title: 'بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ'
    ),
    servers: [
        new OA\Server(url: 'https://api.alquran.cloud/v1'),
        new OA\Server(url: 'http://api.alquran.cloud/v1')
    ],
    tags: [
        new OA\Tag(name: 'Search')
    ]
)]
#[OA\Components(
    schemas: [
        new OA\Schema(
            schema: '200SearchEditionSahihResponse',
            properties: [
                new OA\Property(property: 'identifier', type: 'string', example: "en.sahih"),
                new OA\Property(property: 'language', type: 'string', example: 'en'),
                new OA\Property(property: 'name', type: 'string', example: "Saheeh International"),
                new OA\Property(property: 'englishName', type: 'string', example: "Saheeh International"),
                new OA\Property(property: 'type', type: 'string', example: 'translation')
            ]
        ),
        new OA\Schema(
            schema: '200SearchPartialSurahResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: "سُورَةُ ٱلْفَاتِحَةِ"),
                new OA\Property(property: 'englishName', type: 'string', example: "Al-Faatiha"),
                new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Opening"),
                new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan')
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200SearchSahihResponse',
            properties: [
                new OA\Property(property: 'count', type: 'integer', example: 25),
                new OA\Property(property: 'matches', type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'number', type: 'integer', example: 6),
                            new OA\Property(property: 'text', type: 'string', example: "Guide us to the straight path -"),
                            new OA\Property(property: 'edition', ref: '#/components/schemas/200SearchEditionSahihResponse', type: 'object'),
                            new OA\Property(property: 'surah', ref: '#/components/schemas/200SearchPartialSurahResponse', type: 'object'),
                            new OA\Property(property: 'numberInSurah', type: 'integer', example: 6),
                        ], type: 'object'
                    )
                )
            ], type: 'object'
        )
    ],
    responses: [
        new OA\Response(response: '404SearchResponse', description: "User's Search - Not Found",content: new OA\MediaType(mediaType: 'application/json',
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: 'code', type: 'integer', example: 404),
                    new OA\Property(property: 'status', type: 'string', example: 'NOT FOUND'),
                    new OA\Property(property: 'data', type: 'string', example: "Nothing matching your search was found..")
                ]
            )
        ))
    ],
    parameters: [
        new OA\PathParameter(parameter: 'SearchWordParameter', name: 'word', description: 'Word to search in text',
            in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'Path'),
        new OA\PathParameter(parameter: 'SearchSurahParameter', name: 'surah', description: "Enter a surah number (between 1 and 114) to search a specific surah or 'all' to search all the text",
            in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: '1'),
    ]
)]

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