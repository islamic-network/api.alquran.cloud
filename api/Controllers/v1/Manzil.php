<?php

namespace Api\Controllers\v1;

use Api\Controllers\AlQuranController;
use Mamluk\Kipchak\Components\Http;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Api\Models\ManzilResponse;
use OpenApi\Attributes as OA;

/**
 * All Controllers extending Controllers\Slim Contain the Service / DI Container as a protected property called $container.
 * Access it using $this->container in your controller.
 * Default objects bundled into a container are:
 * logger - which returns an instance of \Monolog\Logger. This is also a protected property on your controller. Access it using $this->logger.
 */


class Manzil extends AlQuranController
{
    #[OA\Get(
        path: '/manzil/{number}',
        description: 'Returns the Manzil for the requested number',
        summary: 'Manzil for the requested number',
        tags: ['Manzil'],
        parameters: [
            new OA\PathParameter(ref: '#/components/parameters/ManzilNumberParameter'),
            new OA\QueryParameter(ref: '#/components/parameters/ManzilOffsetQueryParameter'),
            new OA\QueryParameter(ref: '#/components/parameters/LimitQueryParameter')
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns the Manzil for the requested number',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200QuranUthmaniEntireResponse', type: 'object')
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404ManzilResponse', response: '404')
        ]
    )]

    public function getByNumber(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $number = Http\Request::getAttribute($request, 'number');
        $offset = Http\Request::getQueryParam($request, 'offset');
        $limit = Http\Request::getQueryParam($request, 'limit');
        $edition = 'quran-uthmani-quran-academy';

        $result = $this->mc->get('manzil_' . $number . '_'. $edition . '_' . $offset . '_' . $limit, function (ItemInterface $item) use ($number, $offset, $limit, $edition) {
            $item->expiresAfter(604800);
            $m = new ManzilResponse($this->em, $number, $edition, $offset, $limit);

            return [
                $m->get(),
                $m->getCode()
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
        path: '/manzil/{number}/{edition}',
        description: 'Returns the Manzil for the requested number and requested edition.',
        summary: 'Manzil for the requested number and edition',
        tags: ['Manzil'],
        parameters: [
            new OA\PathParameter(ref: '#/components/parameters/ManzilNumberParameter'),
            new OA\PathParameter(name: 'edition', description: 'Edition name',
                in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'quran-uthmani-quran-academy'),
            new OA\QueryParameter(ref: '#/components/parameters/ManzilOffsetQueryParameter'),
            new OA\QueryParameter(ref: '#/components/parameters/LimitQueryParameter')
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns the Manzil for the requested number and requested edition.',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200QuranUthmaniEntireResponse', type: 'object')
                        ]
                    )
                )
            ),
            new OA\Response(ref: '#/components/responses/404ManzilResponse', response: '404')
        ]
    )]

    public function getByEdition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $number = Http\Request::getAttribute($request, 'number');
        $edition = Http\Request::getAttribute($request, 'edition');
        $offset = Http\Request::getQueryParam($request, 'offset');
        $limit = Http\Request::getQueryParam($request, 'limit');

        $result = $this->mc->get('manzil_' . $number . '_' . $edition . '_' . $offset . '_' . $limit, function (ItemInterface $item) use ($number, $offset, $limit, $edition) {
            $item->expiresAfter(604800);
            $m = new ManzilResponse($this->em, $number, $edition, $offset, $limit);

            return [
                $m->get(),
                $m->getCode()
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