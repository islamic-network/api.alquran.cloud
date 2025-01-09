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

#[OA\OpenApi(
    openapi: '3.1.0',
    info: new OA\Info(
        version: 'v1',
        description: '<p><b />AlQuran API - Sajda</p>
        You can get all verses requiring Sajda / Prostration in the Holy Quran using the endpoints below.',
        title: 'بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ'
    ),
    servers: [
        new OA\Server(url: 'https://api.alquran.cloud/v1'),
        new OA\Server(url: 'http://api.alquran.cloud/v1')
    ],
    tags: [
        new OA\Tag(name: 'Sajda')
    ]
)]
#[OA\Components(
    schemas: [
        new OA\Schema(
            schema: '200SajdaEditionQuranUthmaniResponse',
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
            schema: '200SajdaQuranUthmaniPartialSurahResponse',
            properties: [
                new OA\Property(property: 'number', type: 'integer', example: 7),
                new OA\Property(property: 'name', type: 'string', example:  "سُورَةُ الأَعۡرَافِ"),
                new OA\Property(property: 'englishName', type: 'string', example: "Al-A'raaf"),
                new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Heights"),
                new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan'),
                new OA\Property(property: 'numberOfAyahs', type: 'integer', example: 206)
            ], type: 'object'
        ),
        new OA\Schema(
            schema: '200SajdaQuranUthmaniResponse',
            properties: [
                new OA\Property(property: 'ayahs', type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'number', type: 'integer', example: 1160),
                            new OA\Property(property: 'text', type: 'string', example: "إِنَّ ٱلَّذِینَ عِندَ رَبِّكَ لَا یَسۡتَكۡبِرُونَ عَنۡ عِبَادَتِهِۦ وَیُسَبِّحُونَهُۥ وَلَهُۥ یَسۡجُدُونَ ۩"),
                            new OA\Property(property: 'surah', ref: '#/components/schemas/200SajdaQuranUthmaniPartialSurahResponse', type: 'object'),
                            new OA\Property(property: 'numberInSurah', type: 'integer', example: 206),
                            new OA\Property(property: 'juz', type: 'integer', example: 9),
                            new OA\Property(property: 'manzil', type: 'integer', example: 2),
                            new OA\Property(property: 'page', type: 'integer', example: 176),
                            new OA\Property(property: 'ruku', type: 'integer', example: 145),
                            new OA\Property(property: 'hizbQuarter', type: 'integer', example: 70),
                            new OA\Property(property: 'sajda',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'recommended', type: 'boolean', example: true),
                                    new OA\Property(property: 'obligatory', type: 'boolean', example: false)
                                ], type: 'object'
                            )
                        ], type: 'object'
                    )
                ),
                new OA\Property(property: 'edition', ref: '#/components/schemas/200SajdaEditionQuranUthmaniResponse', type: 'object')
            ], type: 'object'
        ),
    ]
)]


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
            )
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
            )
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