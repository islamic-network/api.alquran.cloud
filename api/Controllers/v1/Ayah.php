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
        description: '<p><b />AlQuran API - Ayah</p>
        The Holy Quran contains 6236 divine verses. With this endpoint, you can retrieve any of those verses.',
        title: 'بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ'
    ),
    servers: [
        new OA\Server(url: 'https://api.alquran.cloud/v1'),
        new OA\Server(url: 'http://api.alquran.cloud/v1')
    ],
    tags: [
        new OA\Tag(name: 'Ayah')
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
        ),
        new OA\Schema(
            schema: '200AyahQuranUthmaniResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 5),
                new OA\Property(property: 'text', type: 'string', example: "إِیَّاكَ نَعۡبُدُ وَإِیَّاكَ نَسۡتَعِینُ"),
                new OA\Property(property: 'edition', ref: '#/components/schemas/200EditionQuranUthmaniResponse', type: 'object'),
                new OA\Property(property: 'surah',
                    properties: [
                        new OA\Property(property: 'number', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: "سُورَةُ ٱلْفَاتِحَةِ"),
                        new OA\Property(property: 'englishName', type: 'string', example: "Al-Faatiha"),
                        new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Opening"),
                        new OA\Property(property: 'numberOfAyahs', type: 'integer', example: 7),
                        new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan')
                    ], type: 'object'
                ),
                new OA\Property(property: 'numberInSurah', type: 'integer', example: 5),
                new OA\Property(property: 'juz', type: 'integer', example: 1),
                new OA\Property(property: 'manzil', type: 'integer', example: 1),
                new OA\Property(property: 'page', type: 'integer', example: 1),
                new OA\Property(property: 'ruku', type: 'integer', example: 1),
                new OA\Property(property: 'hizbQuarter', type: 'integer', example: 1),
                new OA\Property(property: 'sajda', type: 'boolean', example: false)
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200AyahQuranSimpleResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 5),
                new OA\Property(property: 'text', type: 'string', example: "إِیَّاكَ نَعۡبُدُ وَإِیَّاكَ نَسۡتَعِینُ"),
                new OA\Property(property: 'edition', ref: '#/components/schemas/200EditionQuranSimpleResponse', type: 'object'),
                new OA\Property(property: 'surah',
                    properties: [
                        new OA\Property(property: 'number', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: "سُورَةُ ٱلْفَاتِحَةِ"),
                        new OA\Property(property: 'englishName', type: 'string', example: "Al-Faatiha"),
                        new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Opening"),
                        new OA\Property(property: 'numberOfAyahs', type: 'integer', example: 7),
                        new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan')
                    ], type: 'object'
                ),
                new OA\Property(property: 'numberInSurah', type: 'integer', example: 5),
                new OA\Property(property: 'juz', type: 'integer', example: 1),
                new OA\Property(property: 'manzil', type: 'integer', example: 1),
                new OA\Property(property: 'page', type: 'integer', example: 1),
                new OA\Property(property: 'ruku', type: 'integer', example: 1),
                new OA\Property(property: 'hizbQuarter', type: 'integer', example: 1),
                new OA\Property(property: 'sajda', type: 'boolean', example: false)
            ], type: 'object'
        )
    ],
    responses:[
        new OA\Response(response: '404AyahResponse', description: 'Ayah - Not Found',content: new OA\MediaType(mediaType: 'application/json',
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: 'code', type: 'integer', example: 404),
                    new OA\Property(property: 'status', type: 'string', example: 'NOT FOUND'),
                    new OA\Property(property: 'data', type: 'string', example: "Please specify an Ayah number (1 to 6236).")
                ]
            )
        )),
        new OA\Response(response: '404AyahResourceResponse', description: 'Unable to find the requested resource',
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
    ],
    parameters: [
        new OA\PathParameter(parameter: 'AyahNumberParameter', name: 'number', description: 'Ayah Number',
            in: 'path', required: true, schema: new OA\Schema(type: 'integer'), example: 5),
        new OA\PathParameter(parameter: 'AyahEditionNameParameter', name: 'edition', description: 'Edition name',
            in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'quran-uthmani-quran-academy'),
        new OA\PathParameter(parameter: 'AyahEditionsListParameter', name: 'editions', description: 'Comma separated list of edition names',
            in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'quran-uthmani-quran-academy,quran-simple')
    ]
)]

class Ayah extends AlQuranController
{

    #[OA\Get(
        path: '/ayah/random',
        description: 'Returns a single Ayah and its details randomly',
        summary: 'Single Ayah randomly',
        tags: ['Ayah'],
        responses: [
            new OA\Response(response: '200', description: 'Returns a single Ayah and its details randomly',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200AyahQuranUthmaniResponse', type: 'object')
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404AyahResourceResponse', response: '404')
        ]
    )]

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

    #[OA\Get(
        path: '/ayah/random/{edition}',
        description: 'Returns a single Ayah and its details randomly based on the edition',
        summary: 'Single Ayah randomly based on edition',
        tags: ['Ayah'],
        parameters: [
            new OA\PathParameter(ref: '#/components/parameters/AyahEditionNameParameter')
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns a single Ayah and its details randomly based on the edition',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200AyahQuranUthmaniResponse', type: 'object')
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404AyahResourceResponse', response: '404')
        ]
    )]

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

    #[OA\Get(
        path: '/ayah/random/editions/{editions}',
        description: 'Returns a single Ayah and its details randomly based on the given list of editions',
        summary: 'Single Ayah randomly based on list of editions',
        tags: ['Ayah'],
        parameters: [
            new OA\PathParameter(ref: '#/components/parameters/AyahEditionsListParameter')
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns a single Ayah and its details randomly based on the given list of editions',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', type: 'array',
                                items: new OA\Items(
                                    oneOf: [
                                        new OA\Schema(ref: '#/components/schemas/200AyahQuranUthmaniResponse'),
                                        new OA\Schema(ref: '#/components/schemas/200AyahQuranSimpleResponse')
                                    ],
                                )
                            )
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404AyahResourceResponse', response: '404')
        ]
    )]

    public function getRandomEditions(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $number = rand(1, 6326);

        return AyahUtil::getMultipleEditions($request, $number, $response, $this->mc, $this->em);
    }

    #[OA\Get(
        path: '/ayah/{number}',
        description: 'Returns a single Ayah and its details based on the given number',
        summary: 'Single Ayah',
        tags: ['Ayah'],
        parameters: [
            new OA\PathParameter(ref: '#/components/parameters/AyahNumberParameter')
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns a single Ayah and its details based on the given number',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200AyahQuranUthmaniResponse', type: 'object')
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404AyahResponse', response: '404')
        ]
    )]

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

    #[OA\Get(
        path: '/ayah/{number}/{edition}',
        description: 'Returns a single Ayah and its details based on the given number and edition',
        summary: 'Single Ayah based on number and edition',
        tags: ['Ayah'],
        parameters: [
            new OA\PathParameter(ref: '#/components/parameters/AyahNumberParameter'),
            new OA\PathParameter(ref: '#/components/parameters/AyahEditionNameParameter')
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns a single Ayah and its details based on the given number and edition',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200AyahQuranUthmaniResponse', type: 'object')
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404AyahResponse', response: '404')
        ]
    )]

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

    #[OA\Get(
        path: '/ayah/{number}/editions/{editions}',
        description: 'Returns a single Ayah and its details based on the given number and based on the given list of editions',
        summary: 'Single Ayah based on the number and list of editions',
        tags: ['Ayah'],
        parameters: [
            new OA\PathParameter(ref: '#/components/parameters/AyahNumberParameter'),
            new OA\PathParameter(ref: '#/components/parameters/AyahEditionsListParameter')
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns a single Ayah and its details based on the given number and based on the given list of editions',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', type: 'array',
                                items: new OA\Items(
                                    oneOf: [
                                        new OA\Schema(ref: '#/components/schemas/200AyahQuranUthmaniResponse'),
                                        new OA\Schema(ref: '#/components/schemas/200AyahQuranSimpleResponse')
                                    ],
                                )
                            )
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404AyahResponse', response: '404')
        ]
    )]

    public function getManyByNumberAndEditions(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $number = Http\Request::getAttribute($request, 'number');

        return AyahUtil::getMultipleEditions($request, $number, $response, $this->mc, $this->em);
    }



}