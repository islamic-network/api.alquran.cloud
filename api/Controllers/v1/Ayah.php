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
            new OA\Response(ref: '#/components/responses/404NotFoundResourceResponse', response: '404')
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
            new OA\Response(ref: '#/components/responses/404NotFoundResourceResponse', response: '404')
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
            new OA\Response(ref: '#/components/responses/404NotFoundResourceResponse', response: '404')
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
            new OA\Response(response: '404', description: 'Ayah - Not Found', content: new OA\MediaType(mediaType: 'application/json',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'code', type: 'integer', example: 404),
                        new OA\Property(property: 'status', type: 'string', example: 'NOT FOUND'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'string'),
                            example: [
                                "Please specify an Ayah number (1 to 6236).",
                                "Please specify an Ayah number (1 to 6236)."
                            ]
                        )
                    ]
                )
            ))
        ]
    )]

    public function getManyByNumberAndEditions(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $number = Http\Request::getAttribute($request, 'number');

        return AyahUtil::getMultipleEditions($request, $number, $response, $this->mc, $this->em);
    }



}