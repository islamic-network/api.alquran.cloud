<?php

namespace Api\Controllers\v1;

use Api\Controllers\AlQuranController;
use Mamluk\Kipchak\Components\Http;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Api\Models\PageResponse;
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
        description: '<p><b />AlQuran API - Page</p>
        The Holy Quran is traditionally printed / written on 604 pages. You can get the text for each page using the endpoints below.',
        title: 'بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ'
    ),
    servers: [
        new OA\Server(url: 'https://api.alquran.cloud/v1'),
        new OA\Server(url: 'http://api.alquran.cloud/v1')
    ],
    tags: [
        new OA\Tag(name: 'Page')
    ]
)]
#[OA\Components(
    schemas: [
        new OA\Schema(
            schema: '200PageEditionQuranUthmaniResponse',
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
            schema: '200PageQuranUthmaniPartialSurahResponse',
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
            schema: '200PageQuranUthmaniResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 1),
                new OA\Property(property: 'ayahs', type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'number', type: 'integer', example: 5),
                            new OA\Property(property: 'text', type: 'string', example: "إِیَّاكَ نَعۡبُدُ وَإِیَّاكَ نَسۡتَعِینُ"),
                            new OA\Property(property: 'surah', ref: '#/components/schemas/200PageQuranUthmaniPartialSurahResponse', type: 'object'),
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
                        new OA\Property(property: '1', ref: '#/components/schemas/200PageQuranUthmaniPartialSurahResponse', type: 'object')
                    ], type: 'object'
                ),
                new OA\Property(property: 'edition', ref: '#/components/schemas/200PageEditionQuranUthmaniResponse', type: 'object')
            ], type: 'object'
        ),
    ],
    parameters: [
        new OA\PathParameter(parameter: 'PageNumberParameter', name: 'number', description: 'Page Number',
            in: 'path', required: true, schema: new OA\Schema(type: 'integer'), example: 1),
        new OA\QueryParameter(parameter: 'PageOffsetQueryParameter', name: 'offset', description: 'Offset ayahs in a Page by the given number', in: 'query',
            required: false, schema: new OA\Schema(type: 'integer'), example: 4),
        new OA\QueryParameter(parameter: 'PageLimitQueryParameter', name: 'limit', description: 'This is the number of ayahs that the response will be limited to', in: 'query',
            required: false, schema: new OA\Schema(type: 'integer'), example: 1),
    ]
)]

class Page extends AlQuranController
{

    #[OA\Get(
        path: '/page/{number}',
        description: 'Returns the Page for the requested number',
        summary: 'Page for the requested number',
        tags: ['Page'],
        parameters: [
            new OA\PathParameter(ref: '#/components/parameters/PageNumberParameter'),
            new OA\QueryParameter(ref: '#/components/parameters/PageOffsetQueryParameter'),
            new OA\QueryParameter(ref: '#/components/parameters/PageLimitQueryParameter')
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns the Page for the requested number',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200PageQuranUthmaniResponse', type: 'object')
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

        $result = $this->mc->get('page_' . $number . '_'. $edition . '_' . $offset . '_' . $limit, function (ItemInterface $item) use ($number, $offset, $limit, $edition) {
            $item->expiresAfter(604800);
            $p = new PageResponse($this->em, $number, $edition, $offset, $limit);

            return [
                $p->get(),
                $p->getCode()
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
        path: '/page/{number}/{edition}',
        description: 'Returns the Page for the requested number and requested edition.',
        summary: 'Page for the requested number and edition',
        tags: ['Page'],
        parameters: [
            new OA\PathParameter(ref: '#/components/parameters/PageNumberParameter'),
            new OA\PathParameter(name: 'edition', description: 'Edition name',
                in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'quran-uthmani-quran-academy'),
            new OA\QueryParameter(ref: '#/components/parameters/PageOffsetQueryParameter'),
            new OA\QueryParameter(ref: '#/components/parameters/PageLimitQueryParameter')
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns the Page for the requested number and requested edition.',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200PageQuranUthmaniResponse', type: 'object')
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

        $result = $this->mc->get('page_' . $number . '_' . $edition . '_' . $offset . '_' . $limit, function (ItemInterface $item) use ($number, $offset, $limit, $edition) {
            $item->expiresAfter(604800);
            $p = new PageResponse($this->em, $number, $edition, $offset, $limit);

            return [
                $p->get(),
                $p->getCode()
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