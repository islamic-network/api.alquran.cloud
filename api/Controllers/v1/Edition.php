<?php

namespace Api\Controllers\v1;

use Api\Controllers\AlQuranController;
use Api\Models\EditionResponse;
use Mamluk\Kipchak\Components\Http;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\Cache\ItemInterface;
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
        description: '<b />AlQuran API - Edition',
        title: 'بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ'
    ),
    servers: [
        new OA\Server(url: 'https://api.alquran.cloud/v1'),
        new OA\Server(url: 'http://api.alquran.cloud/v1')
    ],
    tags: [
        new OA\Tag(name: 'Edition')
    ]
)]
#[OA\Components(
    schemas: [
        new OA\Schema(
            schema: '200EditionQuranUthmaniResponse',
            properties: [
                new OA\Property(property: 'identifier', type: 'string', example: 'quran-uthmani-quran-academy'),
                new OA\Property(property: 'language', type: 'string', example: 'ar'),
                new OA\Property(property: 'name', type: 'string', example: "القرآن الكريم برسم العثماني (quran-academy)" ),
                new OA\Property(property: 'englishName', type: 'string', example: "Modified Quran Uthmani Text from the Quran Academy to work with the Kitab font"),
                new OA\Property(property: 'format', type: 'string', example: 'text'),
                new OA\Property(property: 'type', type: 'string', example: 'quran'),
                new OA\Property(property: 'direction', type: 'string', example: 'rtl')
            ]
        ),
        new OA\Schema(
            schema: '200EditionQuranSimpleResponse',
            properties: [
                new OA\Property(property: 'identifier', type: 'string', example: 'quran-simple'),
                new OA\Property(property: 'language', type: 'string', example: 'ar'),
                new OA\Property(property: 'name', type: 'string', example: "القرآن الكريم المبسط (تشكيل بسيط) (simple)"),
                new OA\Property(property: 'englishName', type: 'string', example: "Simple"),
                new OA\Property(property: 'format', type: 'string', example: 'text'),
                new OA\Property(property: 'type', type: 'string', example: 'quran'),
                new OA\Property(property: 'direction', type: 'string', example: 'rtl')
            ]
        )
    ],
    responses: [
        new OA\Response(response: '404EditionResponse', description: 'Unable to find the requested resource',
            content: new OA\MediaType(mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'code', type: 'integer', example: 404),
                        new OA\Property(property: 'status', type: 'string', example: 'RESOURCE_NOT_FOUND'),
                        new OA\Property(property: 'data', type: 'string', example: 'Not found.')
                    ]
                )
            )
        )
    ]
)]

class Edition extends AlQuranController
{

    #[OA\Get(
        path: '/edition',
        description: 'Returns a list of all editions',
        summary: 'List of all editions',
        tags: ['Edition'],
        parameters: [
            new OA\QueryParameter(name: 'type', description: 'Type of the editions', in: 'query',
                required: false, schema: new OA\Schema(type: 'string'), example: 'quran'),
            new OA\QueryParameter(name: 'format', description: 'Format of the editions', in: 'query',
                required: false, schema: new OA\Schema(type: 'string'), example: 'text'),
            new OA\QueryParameter(name: 'language', description: 'Language of the editions', in: 'query',
                required: false, schema: new OA\Schema(type: 'string'), example: 'ar'),
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns a list of all editions',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', type: 'array',
                                items: new OA\Items(
                                    oneOf: [
                                        new OA\Schema(ref: '#/components/schemas/200EditionQuranUthmaniResponse'),
                                        new OA\Schema(ref: '#/components/schemas/200EditionQuranSimpleResponse')
                                    ],
                                )
                            )
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404EditionResponse', response: '404')
        ]
    )]

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

    #[OA\Get(
        path: '/edition/type',
        description: 'Returns a list of all edition types',
        summary: 'List of edition types',
        tags: ['Edition'],
        responses: [
            new OA\Response(response: '200', description: 'Returns a list of all edition types',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'string'),
                                example: [
                                    "quran",
                                    "versebyverse",
                                    "transliteration",
                                    "translation",
                                    "tafsir"
                                ]
                            )
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404EditionResponse', response: '404')
        ]
    )]

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

    #[OA\Get(
        path: '/edition/type/{type}',
        description: 'Returns a list of all editions that belong to the requested type',
        summary: 'List of all editions for the requested type',
        tags: ['Edition'],
        parameters: [
            new OA\PathParameter(name: 'type', description: 'Type of the editions', in: 'path',
                required: true, schema: new OA\Schema(type: 'string'), example: 'quran')
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns a list of all editions that belong to the requested type',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', type: 'array',
                                items: new OA\Items(
                                    oneOf: [
                                        new OA\Schema(ref: '#/components/schemas/200EditionQuranUthmaniResponse'),
                                        new OA\Schema(ref: '#/components/schemas/200EditionQuranSimpleResponse')
                                    ],
                                )
                            )
                        ]
                    )
                )
            ),
            new OA\Response(response: '404', description: 'Edition Type - Not Found',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 404),
                            new OA\Property(property: 'status', type: 'string', example: 'NOT FOUND'),
                            new OA\Property(property: 'data', type: 'string', example: 'Invalid type.')
                        ]
                    )
                )
            )
        ]
    )]

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

    #[OA\Get(
        path: '/edition/format',
        description: 'Returns a list of all edition formats',
        summary: 'List of edition formats',
        tags: ['Edition'],
        responses: [
            new OA\Response(response: '200', description: 'Returns a list of all edition formats',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'string'),
                                example: [
                                    'text',
                                    'audio'
                                ]
                            )
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404EditionResponse', response: '404')
        ]
    )]

    public function getFormats(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return Http\Response::json($response,
            ['text', 'audio'],
            200,
            true,
            86400
        );

    }

    #[OA\Get(
        path: '/edition/format/{format}',
        description: 'Returns a list of all editions that belong to the requested format',
        summary: 'List of all editions for the requested format',
        tags: ['Edition'],
        parameters: [
            new OA\PathParameter(name: 'format', description: 'Format of the editions', in: 'path',
                required: true, schema: new OA\Schema(type: 'string'), example: 'text')
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns a list of all editions that belong to the requested format',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', type: 'array',
                                items: new OA\Items(
                                    oneOf: [
                                        new OA\Schema(ref: '#/components/schemas/200EditionQuranUthmaniResponse'),
                                        new OA\Schema(ref: '#/components/schemas/200EditionQuranSimpleResponse')
                                    ],
                                )
                            )
                        ]
                    )
                )
            ),
            new OA\Response(response: '404', description: 'Edition Format - Not Found',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 404),
                            new OA\Property(property: 'status', type: 'string', example: 'NOT FOUND'),
                            new OA\Property(property: 'data', type: 'string', example: 'Invalid format.')
                        ]
                    )
                )
            )
        ]
    )]

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

    #[OA\Get(
        path: '/edition/language',
        description: 'Returns a list of all edition languages',
        summary: 'List of edition languages',
        tags: ['Edition'],
        responses: [
            new OA\Response(response: '200', description: 'Returns a list of all edition languages',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'string'),
                                example: ['ar', 'am', 'az', 'ber', 'bn', 'cs', 'de', 'dv', 'en', 'es', 'fa', 'fr','ha', 'hi', 'id', 'it', 'ja', 'ko', 'ku', 'ml', 'nl', 'no', 'pl','ps', 'pt', 'ro', 'ru', 'sd', 'so', 'sq', 'sv', 'sw', 'ta', 'tg', 'th', 'tr', 'tt', 'ug', 'ur', 'uz'],
                            )
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404EditionResponse', response: '404')
        ]
    )]

    public function getLanguages(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return Http\Response::json($response,
            ['ar', 'am', 'az', 'ber', 'bn', 'cs', 'de', 'dv', 'en', 'es', 'fa', 'fr','ha', 'hi', 'id', 'it', 'ja', 'ko', 'ku', 'ml', 'nl', 'no', 'pl','ps', 'pt', 'ro', 'ru', 'sd', 'so', 'sq', 'sv', 'sw', 'ta', 'tg', 'th', 'tr', 'tt', 'ug', 'ur', 'uz'],
            200,
            true,
            86400
        );

    }

    #[OA\Get(
        path: '/edition/language/{lang}',
        description: 'Returns a list of all editions that belong to the requested language',
        summary: 'List of all editions for the requested language',
        tags: ['Edition'],
        parameters: [
            new OA\PathParameter(name: 'lang', description: 'Language of the editions', in: 'path',
                required: true, schema: new OA\Schema(type: 'string'), example: 'ar')
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns a list of all editions that belong to the requested language',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', type: 'array',
                                items: new OA\Items(
                                    oneOf: [
                                        new OA\Schema(ref: '#/components/schemas/200EditionQuranUthmaniResponse'),
                                        new OA\Schema(ref: '#/components/schemas/200EditionQuranSimpleResponse')
                                    ],
                                )
                            )
                        ]
                    )
                )
            ),
            new OA\Response(response: '404', description: 'Edition Language - Not Found',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 404),
                            new OA\Property(property: 'status', type: 'string', example: 'NOT FOUND'),
                            new OA\Property(property: 'data', type: 'string', example: 'Invalid language.')
                        ]
                    )
                )
            )
        ]
    )]

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