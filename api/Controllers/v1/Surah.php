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
            new OA\Response(ref: '#/components/responses/404NotFoundResourceResponse', response: '404')
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
            new OA\QueryParameter(ref: '#/components/parameters/LimitQueryParameter'),
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
            new OA\QueryParameter(ref: '#/components/parameters/LimitQueryParameter'),
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
            new OA\QueryParameter(ref: '#/components/parameters/LimitQueryParameter'),
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
            new OA\Response(ref: '#/components/responses/404NotFoundResourceResponse', response: '404')
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