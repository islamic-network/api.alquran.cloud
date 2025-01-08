<?php

namespace Api\Controllers\v1;

use Api\Controllers\AlQuranController;
use Mamluk\Kipchak\Components\Http;
use mysql_xdevapi\Schema;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Api\Models\MetaResponse;
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
        description: '<p><b />AlQuran API - Meta</p>
        Get metadata about Surahs, Pages, Hizbs and Juzs using the endpoint below.',
        title: 'بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ'
    ),
    servers: [
        new OA\Server(url: 'https://api.alquran.cloud/v1'),
        new OA\Server(url: 'http://api.alquran.cloud/v1')
    ],
    tags: [
        new OA\Tag(name: 'Meta')
    ]
)]
#[OA\Components(
    schemas: [
        new OA\Schema(
            schema: '200MetaReferenceResponse',
            properties: [
                new OA\Property(property: 'surah', type: 'integer', example: 1),
                new OA\Property(property: 'ayah', type: 'integer', example: 1)
            ], type: 'object'
        )
    ]
)]

class Meta extends AlQuranController
{

    #[OA\Get(
        path: '/meta',
        description: 'Returns all the available meta data about Surahs, Pages, Hizbs and Juzs.',
        summary: 'Get metadata',
        tags: ['Meta'],
        responses: [
            new OA\Response(response: '200', description: 'Returns all the available meta data about Surahs, Pages, Hizbs and Juzs.',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data',
                                properties: [
                                    new OA\Property(property: 'ayahs',
                                        properties: [
                                            new OA\Property(property: 'count', type: 'integer', example: 6236)
                                        ], type: 'object'
                                    ),
                                    new OA\Property(property: 'surahs',
                                        properties: [
                                            new OA\Property(property: 'count', type: 'integer', example: 114),
                                            new OA\Property(property: 'references', type: 'array',
                                                items: new OA\Items(
                                                    properties: [
                                                        new OA\Property(property: 'number', type: 'integer', example: 1),
                                                        new OA\Property(property: 'name', type: 'string', example: "سُورَةُ ٱلْفَاتِحَةِ"),
                                                        new OA\Property(property: 'englishName', type: 'string', example: "Al-Faatiha"),
                                                        new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Opening"),
                                                        new OA\Property(property: 'numberOfAyahs', type: 'integer', example: 7),
                                                        new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan')
                                                    ], type: 'object'
                                                )
                                            ),
                                        ], type: 'object'
                                    ),
                                    new OA\Property(property: 'sajdas',
                                        properties: [
                                            new OA\Property(property: 'count', type: 'integer', example: 15),
                                            new OA\Property(property: 'references', type: 'array',
                                                items: new OA\Items(
                                                    properties: [
                                                        new OA\Property(property: 'surah', type: 'integer', example: 7),
                                                        new OA\Property(property: 'ayah', type: 'integer', example: 206),
                                                        new OA\Property(property: 'recommended', type: 'boolean', example: true),
                                                        new OA\Property(property: 'obligatory', type: 'boolean', example: false)
                                                    ], type: 'object'
                                                )
                                            ),
                                        ], type: 'object'
                                    ),
                                    new OA\Property(property: 'rukus',
                                        properties: [
                                            new OA\Property(property: 'count', type: 'integer', example: 556),
                                            new OA\Property(property: 'references', type: 'array',
                                                items: new OA\Items(ref: '#/components/schemas/200MetaReferenceResponse')
                                            )
                                        ], type: 'object'
                                    ),
                                    new OA\Property(property: 'pages',
                                        properties: [
                                            new OA\Property(property: 'count', type: 'integer', example: 604),
                                            new OA\Property(property: 'references', type: 'array',
                                                items: new OA\Items(ref: '#/components/schemas/200MetaReferenceResponse')
                                            )
                                        ], type: 'object'
                                    ),
                                    new OA\Property(property: 'manzils',
                                        properties: [
                                            new OA\Property(property: 'count', type: 'integer', example: 7),
                                            new OA\Property(property: 'references', type: 'array',
                                                items: new OA\Items(ref: '#/components/schemas/200MetaReferenceResponse')
                                            )
                                        ], type: 'object'
                                    ),
                                    new OA\Property(property: 'hizbQuarters',
                                        properties: [
                                            new OA\Property(property: 'count', type: 'integer', example: 240),
                                            new OA\Property(property: 'references', type: 'array',
                                                items: new OA\Items(ref: '#/components/schemas/200MetaReferenceResponse')
                                            )
                                        ], type: 'object'
                                    ),
                                    new OA\Property(property: 'juzs',
                                        properties: [
                                            new OA\Property(property: 'count', type: 'integer', example: 30),
                                            new OA\Property(property: 'references', type: 'array',
                                                items: new OA\Items(ref: '#/components/schemas/200MetaReferenceResponse')
                                            )
                                        ], type: 'object'
                                    ),
                                ], type: 'object'
                            )
                        ]
                    )
                )
            )
        ]
    )]

    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $result = $this->mc->get('meta', function (ItemInterface $item) {
            $item->expiresAfter(604800);
            $meta = new MetaResponse($this->em);

            return $meta->get();
        });

        return Http\Response::json($response,
            $result,
            200,
            true,
            86400
        );

    }

}