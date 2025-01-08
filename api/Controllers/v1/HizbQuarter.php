<?php

namespace Api\Controllers\v1;

use Api\Controllers\AlQuranController;
use Mamluk\Kipchak\Components\Http;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Api\Models\HizbQuarterResponse;
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
        description: '<p><b />AlQuran API - HizbQuarter</p>
        The Holy Quran comprises 240 Hizb Quarters. One Hizb is half a Juz.',
        title: 'بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ'
    ),
    servers: [
        new OA\Server(url: 'https://api.alquran.cloud/v1'),
        new OA\Server(url: 'http://api.alquran.cloud/v1')
    ],
    tags: [
        new OA\Tag(name: 'HizbQuarter')
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
            schema: '200HizbQuarterQuranUthmaniPartialSurahResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 1),
                new OA\Property(property: 'name', type: 'string', example: "سُورَةُ ٱلْفَاتِحَةِ"),
                new OA\Property(property: 'englishName', type: 'string', example: "Al-Faatiha"),
                new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Opening"),
                new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan'),
                new OA\Property(property: 'numberOfAyahs', type: 'integer', example: 7)
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200HizbQuarterQuranUthmaniResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 1),
                new OA\Property(property: 'ayahs', type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'number', type: 'integer', example: 5),
                            new OA\Property(property: 'text', type: 'string', example: "إِیَّاكَ نَعۡبُدُ وَإِیَّاكَ نَسۡتَعِینُ"),
                            new OA\Property(property: 'surah', ref: '#/components/schemas/200HizbQuarterQuranUthmaniPartialSurahResponse', type: 'object'),
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
                new OA\Property(property: 'surahs',
                    properties: [
                        new OA\Property(property: '1', ref: '#/components/schemas/200HizbQuarterQuranUthmaniPartialSurahResponse', type: 'object')
                    ], type: 'object'
                ),
                new OA\Property(property: 'edition', ref: '#/components/schemas/200EditionQuranUthmaniResponse', type: 'object')
            ], type: 'object'
        ),
    ],
    parameters: [
        new OA\PathParameter(parameter: 'HizbQuarterNumberParameter', name: 'number', description: 'Hizb Quarter Number',
            in: 'path', required: true, schema: new OA\Schema(type: 'integer'), example: 1),
        new OA\QueryParameter(parameter: 'HizbQuarterOffsetQueryParameter', name: 'offset', description: 'Offset ayahs in a Hizb Quarter by the given number',
            in: 'query', required: false, schema: new OA\Schema(type: 'integer'), example: 4),
        new OA\QueryParameter(parameter: 'HizbQuarterLimitQueryParameter', name: 'limit', description: 'This is the number of ayahs that the response will be limited to',
            in: 'query', required: false, schema: new OA\Schema(type: 'integer'), example: 1),
    ]
)]

class HizbQuarter extends AlQuranController
{

    #[OA\Get(
        path: '/hizbQuarter/{number}',
        description: 'Returns the Hizb Quarter for the requested number',
        summary: 'Hizb Quarter for the requested number',
        tags: ['HizbQuarter'],
        parameters: [
            new OA\PathParameter(ref: '#/components/parameters/HizbQuarterNumberParameter'),
            new OA\QueryParameter(ref: '#/components/parameters/HizbQuarterOffsetQueryParameter'),
            new OA\QueryParameter(ref: '#/components/parameters/HizbQuarterLimitQueryParameter')
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns the Hizb Quarter for the requested number',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200HizbQuarterQuranUthmaniResponse', type: 'object')
                        ]
                    )
                )
            )
        ]
    )]

    public function getByNumber(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $number = Http\Request::getAttribute($request, 'number');
        $offset = Http\Request::getQueryParam($request, 'offset');
        $limit = Http\Request::getQueryParam($request, 'limit');
        $edition = 'quran-uthmani-quran-academy';

        $result = $this->mc->get('hq_' . $number . '_'. $edition . '_' . $offset . '_' . $limit, function (ItemInterface $item) use ($number, $offset, $limit, $edition) {
            $item->expiresAfter(604800);
            $hq = new HizbQuarterResponse($this->em, $number, $edition, $offset, $limit);

            return [
                $hq->get(),
                $hq->getCode()
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
        path: '/hizbQuarter/{number}/{edition}',
        description: 'Returns the Hizb Quarter for the requested number and requested edition',
        summary: 'Hizb Quarter for the requested number and requested edition',
        tags: ['HizbQuarter'],
        parameters: [
            new OA\PathParameter(ref: '#/components/parameters/HizbQuarterNumberParameter'),
            new OA\PathParameter(name: 'edition', description: 'Edition name',
                in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'quran-uthmani-quran-academy'),
            new OA\QueryParameter(ref: '#/components/parameters/HizbQuarterOffsetQueryParameter'),
            new OA\QueryParameter(ref: '#/components/parameters/HizbQuarterLimitQueryParameter')
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns the Hizb Quarter for the requested number and requested edition',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200HizbQuarterQuranUthmaniResponse', type: 'object')
                        ]
                    )
                )
            )
        ]
    )]

    public function getByEdition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $number = Http\Request::getAttribute($request, 'number');
        $edition = Http\Request::getAttribute($request, 'edition');
        $offset = Http\Request::getQueryParam($request, 'offset');
        $limit = Http\Request::getQueryParam($request, 'limit');

        $result = $this->mc->get('hq_' . $number . '_' . $edition . '_' . $offset . '_' . $limit, function (ItemInterface $item) use ($number, $offset, $limit, $edition) {
            $item->expiresAfter(604800);
            $hq = new HizbQuarterResponse($this->em, $number, $edition, $offset, $limit);

            return [
                $hq->get(),
                $hq->getCode()
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