<?php

namespace Api\Controllers\v1;

use Api\Controllers\AlQuranController;
use Mamluk\Kipchak\Components\Http;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Api\Models\SajdaResponse;
use OpenApi\Attributes as OA;

/**
 * All Controllers extending Controllers\Slim Contain the Service / DI Container as a protected property called $container.
 * Access it using $this->container in your controller.
 * Default objects bundled into a container are:
 * logger - which returns an instance of \Monolog\Logger. This is also a protected property on your controller. Access it using $this->logger.
 */



class Sajda extends AlQuranController
{

    #[OA\Get(
        path: '/sajda',
        description: 'Returns all the verses of the Holy Quran that require Sajda / Prostration.',
        summary: 'Verses that require Sajda / Prostration',
        tags: ['Sajda'],
        responses: [
            new OA\Response(response: '200', description: 'Returns all the verses of the Holy Quran that require Sajda / Prostration.',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200SajdaQuranUthmaniResponse', type: 'object')
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404NotFoundResourceResponse', response: '404')
        ]
    )]

    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $edition = 'quran-uthmani-quran-academy';

        $result = $this->mc->get('sajda_' . $edition, function (ItemInterface $item) use ($edition) {
            $item->expiresAfter(604800);
            $s = new SajdaResponse($this->em, $edition);

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
        path: '/sajda/{edition}',
        description: 'Returns all the verses of the Holy Quran for the requested edition that require Sajda / Prostration.',
        summary: 'Verses that require Sajda / Prostration for the requested edition',
        tags: ['Sajda'],
        parameters: [
            new OA\PathParameter(name: 'edition', description: 'Edition name',
                in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'quran-uthmani-quran-academy')
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns all the verses of the Holy Quran for the requested edition that require Sajda / Prostration.',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200SajdaQuranUthmaniResponse', type: 'object')
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404NotFoundResourceResponse', response: '404')
        ]
    )]

    public function getByEdition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $edition = Http\Request::getAttribute($request, 'edition');

        $result = $this->mc->get('sajda_' . $edition, function (ItemInterface $item) use ($edition) {
            $item->expiresAfter(604800);
            $s = new SajdaResponse($this->em, $edition);

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