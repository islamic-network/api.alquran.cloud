<?php

namespace Api\Controllers\v1;

use Api\Controllers\AlQuranController;
use Api\Models\SuratResponse;
use Api\Utils\Request;
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
        description: '<p><b />AlQuran API - Surah</p>
        The Holy Quran has 114 Surahs. You can get a list of all of them or all the ayahs for a particular surah using the endpoints below.',
        title: 'بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ'
    ),
    servers: [
        new OA\Server(url: 'https://api.alquran.cloud/v1'),
        new OA\Server(url: 'http://api.alquran.cloud/v1')
    ],
    tags: [
        new OA\Tag(name: 'Surah')
    ]
)]
#[OA\Components(
    schemas: [
        new OA\Schema(
            schema: '200SurahQuranUthmaniResponse',
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
            schema: '200SurahEditionQuranSimpleResponse',
            properties: [
                new OA\Property(property: 'identifier', type: 'string', example: "quran-simple"),
                new OA\Property(property: 'language', type: 'string', example: 'ar'),
                new OA\Property(property: 'name', type: 'string', example: "القرآن الكريم المبسط (تشكيل بسيط) (simple)" ),
                new OA\Property(property: 'englishName', type: 'string', example: "Simple"),
                new OA\Property(property: 'format', type: 'string', example: 'text'),
                new OA\Property(property: 'type', type: 'string', example: 'quran'),
                new OA\Property(property: 'direction', type: 'string', example: 'rtl')
            ]
        ),
        new OA\Schema(
            schema: '200SurahPartialSurahResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: "سُورَةُ ٱلْفَاتِحَةِ"),
                new OA\Property(property: 'englishName', type: 'string', example: "Al-Faatiha"),
                new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Opening"),
                new OA\Property(property: 'numberOfAyahs', type: 'integer', example: 7),
                new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan')
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200SurahResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: "سُورَةُ ٱلْفَاتِحَةِ"),
                new OA\Property(property: 'englishName', type: 'string', example: "Al-Faatiha"),
                new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Opening"),
                new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan'),
                new OA\Property(property: 'numberOfAyahs', type: 'integer', example: 7),
                new OA\Property(property: 'ayahs', type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'number', type: 'integer', example: 5),
                            new OA\Property(property: 'text', type: 'string', example: "إِیَّاكَ نَعۡبُدُ وَإِیَّاكَ نَسۡتَعِینُ"),
                            new OA\Property(property: 'numberInSurah', type: 'integer', example: 5),
                            new OA\Property(property: 'juz', type: 'integer', example: 1),
                            new OA\Property(property: 'manzil', type: 'integer', example: 1),
                            new OA\Property(property: 'page', type: 'integer', example: 1),
                            new OA\Property(property: 'ruku', type: 'integer', example: 1),
                            new OA\Property(property: 'hizbQuarter', type: 'integer', example: 1),
                            new OA\Property(property: 'sajda', type: 'boolean', example: false)
                        ], type: 'object'
                    )
                ),
                new OA\Property(property: 'edition', ref: '#/components/schemas/200SurahQuranUthmaniResponse', type: 'object'),
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200SurahQuranSimpleResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: "سُورَةُ ٱلْفَاتِحَةِ"),
                new OA\Property(property: 'englishName', type: 'string', example: "Al-Faatiha"),
                new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Opening"),
                new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan'),
                new OA\Property(property: 'numberOfAyahs', type: 'integer', example: 7),
                new OA\Property(property: 'ayahs', type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'number', type: 'integer', example: 5),
                            new OA\Property(property: 'text', type: 'string', example: "إِيَّاكَ نَعْبُدُ وَإِيَّاكَ نَسْتَعِينُ"),
                            new OA\Property(property: 'numberInSurah', type: 'integer', example: 5),
                            new OA\Property(property: 'juz', type: 'integer', example: 1),
                            new OA\Property(property: 'manzil', type: 'integer', example: 1),
                            new OA\Property(property: 'page', type: 'integer', example: 1),
                            new OA\Property(property: 'ruku', type: 'integer', example: 1),
                            new OA\Property(property: 'hizbQuarter', type: 'integer', example: 1),
                            new OA\Property(property: 'sajda', type: 'boolean', example: false)
                        ], type: 'object'
                    )
                ),
                new OA\Property(property: 'edition', ref: '#/components/schemas/200SurahEditionQuranSimpleResponse', type: 'object'),
            ], type: 'object'
        )
    ],
    responses: [
        new OA\Response(response: '404SurahResponse', description: 'Surah - Not Found',content: new OA\MediaType(mediaType: 'application/json',
            schema: new OA\Schema(
                properties: [
                    new OA\Property(property: 'code', type: 'integer', example: 404),
                    new OA\Property(property: 'status', type: 'string', example: 'NOT FOUND'),
                    new OA\Property(property: 'data', type: 'string', example: "Surat number should be between 1 and 114.")
                ]
            )
        )),
        new OA\Response(response: '404SurahResourceResponse', description: 'Unable to find the requested resource',
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
        new OA\PathParameter(parameter: 'SurahNumberParameter', name: 'number', description: 'Surah Number',
            in: 'path', required: true, schema: new OA\Schema(type: 'integer'), example: 1),
        new OA\QueryParameter(parameter: 'SurahOffsetQueryParameter', name: 'offset', description: 'Offset ayahs in a Surah by the given number', in: 'query',
            required: false, schema: new OA\Schema(type: 'integer'), example: 4),
        new OA\QueryParameter(parameter: 'SurahLimitQueryParameter', name: 'limit', description: 'This is the number of ayahs that the response will be limited to', in: 'query',
            required: false, schema: new OA\Schema(type: 'integer'), example: 1),
    ]
)]


class Surah extends AlQuranController
{

    #[OA\Get(
        path: '/surah',
        description: 'Returns the list of all Surahs in the Holy Quran',
        summary: 'List of Surahs',
        tags: ['Surah'],
        responses: [
            new OA\Response(response: '200', description: 'Returns the list of all Surahs in the Holy Quran',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', type: 'array',
                                items: new OA\Items(ref: '#/components/schemas/200SurahPartialSurahResponse')
                            )
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404SurahResourceResponse', response: '404')
        ]
    )]

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

    #[OA\Get(
        path: '/surah/{number}',
        description: 'Returns a Surah as per the specified number along with all the Ayahs and details',
        summary: 'A Surah along with all Ayahs and details',
        tags: ['Surah'],
        parameters: [
            new OA\PathParameter(ref: '#/components/parameters/SurahNumberParameter'),
            new OA\QueryParameter(ref: '#/components/parameters/SurahOffsetQueryParameter'),
            new OA\QueryParameter(ref: '#/components/parameters/SurahLimitQueryParameter'),
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns a Surah as per the specified number along with all the Ayahs and details',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200SurahResponse', type: 'object')
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404SurahResponse', response: '404')
        ]
    )]

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

    #[OA\Get(
        path: '/surah/{number}/{edition}',
        description: 'Returns a Surah as per the specified number and specified edition along with all the Ayahs and details',
        summary: 'A Surah as per the specified number and specified edition along with all Ayahs and details',
        tags: ['Surah'],
        parameters: [
            new OA\PathParameter(ref: '#/components/parameters/SurahNumberParameter'),
            new OA\PathParameter(name: 'edition', description: 'Edition name',
                in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'quran-uthmani-quran-academy'),
            new OA\QueryParameter(ref: '#/components/parameters/SurahOffsetQueryParameter'),
            new OA\QueryParameter(ref: '#/components/parameters/SurahLimitQueryParameter'),
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns a Surah as per the specified number and specified edition along with all the Ayahs and details',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200SurahResponse', type: 'object')
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404SurahResponse', response: '404')
        ]
    )]

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

    #[OA\Get(
        path: '/surah/{number}/editions/{editions}',
        description: 'Returns a Surah as per the specified number and specified list of editions along with all the Ayahs and details',
        summary: 'A Surah as per the specified number and specified list of editions along with all Ayahs and details',
        tags: ['Surah'],
        parameters: [
            new OA\PathParameter(ref: '#/components/parameters/SurahNumberParameter'),
            new OA\PathParameter(name: 'editions', description: 'Comma separated list of edition names',
                in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'quran-uthmani-quran-academy,quran-simple'),
            new OA\QueryParameter(ref: '#/components/parameters/SurahOffsetQueryParameter'),
            new OA\QueryParameter(ref: '#/components/parameters/SurahLimitQueryParameter'),
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns a Surah as per the specified number and specified list of editions along with all the Ayahs and details',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', type: 'array',
                                items: new OA\Items(
                                    oneOf: [
                                        new OA\Schema(ref: '#/components/schemas/200SurahResponse'),
                                        new OA\Schema(ref: '#/components/schemas/200SurahQuranSimpleResponse')
                                    ]
                                )
                            )
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404SurahResourceResponse', response: '404')
        ]
    )]

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