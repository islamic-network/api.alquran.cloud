<?php

namespace Api\Controllers\v1;

use Api\Controllers\AlQuranController;
use Mamluk\Kipchak\Components\Http;
use PharIo\Version\Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Api\Models\CompleteResponse;
use Api\Entities\Doctrine\Primary\Edition;
use Api\Entities\Doctrine\Primary\Ayat;
use Symfony\Component\Yaml\Yaml;
use OpenApi\Attributes as OA;

/**
 * All Controllers extending Controllers\Slim Contain the Service / DI Container as a protected property called $container.
 * Access it using $this->container in your controller.
 * Default objects bundled into a container are:
 * logger - which returns an instance of \Monolog\Logger. This is also a protected property on your controller. Access it using $this->logger.
 */

//#[OA\OpenApi(
//    openapi: '3.1.0',
//    info: new OA\Info(
//        version: 'v1',
//        description: '<p><b />AlQuran API - Quran
//        <br />The Holy Quran is a divine book from Allah SWT.</p>
//        You can get the text for complete edition using the endpoints below.',
//        title: 'بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ'
//    ),
//    servers: [
//        new OA\Server(url: 'https://api.alquran.cloud/v1'),
//        new OA\Server(url: 'http://api.alquran.cloud/v1')
//    ],
//    tags: [
//        new OA\Tag(name: 'Quran')
//    ]
//)]
//#[OA\Components(
//    schemas: [
//        new OA\Schema(
//            schema: '200QuranUthmaniEditionResponse',
//            properties: [
//                new OA\Property(property: 'identifier', type: 'string', example: 'quran-uthmani-quran-academy'),
//                new OA\Property(property: 'language', type: 'string', example: 'ar'),
//                new OA\Property(property: 'name', type: 'string', example: "القرآن الكريم برسم العثماني (quran-academy)" ),
//                new OA\Property(property: 'englishName', type: 'string', example: "Modified Quran Uthmani Text from the Quran Academy to work with the Kitab font"),
//                new OA\Property(property: 'format', type: 'string', example: 'text'),
//                new OA\Property(property: 'type', type: 'string', example: 'quran')
//            ]
//        ),
//        new OA\Schema(
//            schema: '200QuranUthmaniResponse',
//            properties: [
//                new OA\Property(property: 'surahs', type: 'array',
//                    items: new OA\Items(
//                        properties: [
//                            new OA\Property(property: 'number', type: 'integer', example: 1),
//                            new OA\Property(property: 'name', type: 'string', example: "سُورَةُ ٱلْفَاتِحَةِ"),
//                            new OA\Property(property: 'englishName', type: 'string', example: "Al-Faatiha"),
//                            new OA\Property(property: 'englishNameTranslation', type: 'string', example: "The Opening"),
//                            new OA\Property(property: 'revelationType', type: 'string', example: 'Meccan'),
//                            new OA\Property(property: 'ayahs', type: 'array',
//                                items: new OA\Items(
//                                    properties: [
//                                        new OA\Property(property: 'number', type: 'integer', example: 1),
//                                        new OA\Property(property: 'text', type: 'string', example: "بِسۡمِ ٱللَّهِ ٱلرَّحۡمَـٰنِ ٱلرَّحِیمِ"),
//                                        new OA\Property(property: 'numberInSurah', type: 'integer', example: 1),
//                                        new OA\Property(property: 'juz', type: 'integer', example: 1),
//                                        new OA\Property(property: 'manzil', type: 'integer', example: 1),
//                                        new OA\Property(property: 'page', type: 'integer', example: 1),
//                                        new OA\Property(property: 'ruku', type: 'integer', example: 1),
//                                        new OA\Property(property: 'hizbQuarter', type: 'integer', example: 1),
//                                        new OA\Property(property: 'sajda', type: 'boolean', example: false)
//                                    ], type: 'object'
//                                )
//                            )
//                        ], type: 'object'
//                    )
//                ),
//                new OA\Property(property: 'edition', ref: '#/components/schemas/200QuranUthmaniEditionResponse', type: 'object')
//            ], type: 'object'
//        )
//    ],
//    responses: [
//        new OA\Response(response: '404QuranResponse', description: 'Unable to find the requested resource',
//            content: new OA\MediaType(mediaType: 'application/json',
//                schema: new OA\Schema(
//                    properties: [
//                        new OA\Property(property: 'code', type: 'integer', example: 404),
//                        new OA\Property(property: 'status', type: 'string', example: 'RESOURCE_NOT_FOUND'),
//                        new OA\Property(property: 'data', type: 'string', example: 'Not found.')
//                    ]
//                )
//            )
//        )
//    ]
//)]

class Quran extends AlQuranController
{

    #[OA\Get(
        path: '/quran',
        description: 'Returns a complete Holy Quran edition.',
        summary: 'Complete Holy Quran edition',
        tags: ['Quran'],
        responses: [
            new OA\Response(response: '200', description: 'Returns a complete Holy Quran edition.',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200QuranUthmaniResponse', type: 'object')
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

        $result = $this->mc->get('quran_' . $edition, function (ItemInterface $item) use ($edition) {
            $item->expiresAfter(604800);
            $q = new CompleteResponse($this->em, $edition);

            return [
                $q->get(),
                $q->getCode()
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
        path: '/quran/{edition}',
        description: 'Returns a complete Holy Quran edition as per the given edition.',
        summary: 'Complete Holy Quran edition as per the given edition.',
        tags: ['Quran'],
        parameters: [
            new OA\PathParameter(name: 'edition', description: 'Edition name',
                in: 'path', required: true, schema: new OA\Schema(type: 'string'), example: 'quran-uthmani-quran-academy'),
        ],
        responses: [
            new OA\Response(response: '200', description: 'Returns a complete Holy Quran edition as per the given edition.',
                content: new OA\MediaType(mediaType: 'application/json',
                    schema: new OA\Schema(
                        properties: [
                            new OA\Property(property: 'code', type: 'integer', example: 200),
                            new OA\Property(property: 'status', type: 'string', example: 'OK'),
                            new OA\Property(property: 'data', ref: '#/components/schemas/200QuranUthmaniResponse', type: 'object')
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

        $result = $this->mc->get('quran_' . $edition, function (ItemInterface $item) use ($edition) {
            $item->expiresAfter(604800);
            $q = new CompleteResponse($this->em, $edition);

            return [
                $q->get(),
                $q->getCode()
            ];

        });

        return Http\Response::json($response,
            $result[0],
            $result[1],
            true,
            86400
        );

    }

    public function createEdition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $secretkey = $this->container->get('config')['kipchak.api']['importerkey'];
        $apikey = Http\Request::getQueryParam($request, 'apikey');

        if ($secretkey !== $apikey) {
            return Http\Response::json($response,
                "Unable to verify key.",
                401
            );
        }

        $files = $request->getUploadedFiles();

        if (count($files) < 2) {
            return Http\Response::json($response, 'Please provide the 2 files', 400);
        }


        foreach ($files as $file) {
            /**
             * @var $file UploadedFileInterface
             */
            if ($file->getClientFilename() === 'edition.txt') {
                $text = (string) $file->getStream();
            }
            if ($file->getClientFilename() === 'edition.yml') {
                $info = (string) $file->getStream();
            }
        }

        $yml = (object) Yaml::parse($info);
        $ayahs = explode("\n", $text);

        // TODO: Add duplicate check in addition to number of ayahs
        if (count($ayahs) !== 6236) {
            return Http\Response::json($response,
                'edition.txt must contain 6236 lines. It currently contains ' . count($ayahs) . '.',
                400
            );
        }

        try {
            // Add Edition
            $edition = new Edition();
            $edition->setIdentifier($yml->identifier);
            $edition->setName($yml->name);
            $edition->setLanguage($yml->language);
            $edition->setEnglishName($yml->englishname);
            $edition->setFormat($yml->format);
            $edition->setType($yml->type);
            $edition->setLastUpdated($yml->lastupdated);
            $edition->setDirection($yml->direction);
            $edition->setSource($yml->source);
            $this->em->persist($edition);

            $count = 0;
            foreach ($ayahs as $ayah) {
                $count++;
                /**
                 * @var Ayat $refAyat
                 */
                $refAyat = $this->em->getRepository(Ayat::class)->findOneBy(['edition' => '1', 'number' => $count]);
                $ayat = new Ayat();
                $ayat->setEdition($edition);
                $ayat->setSurat($refAyat->getSurat());
                $ayat->setJuz($refAyat->getJuz());
                $ayat->setNumber($refAyat->getNumber());
                $ayat->setNumberInSurat($refAyat->getNumberInSurat());
                $ayat->setManzil($refAyat->getManzil());
                $ayat->setPage($refAyat->getPage());
                $ayat->setRuku($refAyat->getRuku());
                $ayat->setSajda($refAyat->getSajda());
                $ayat->setHizbQuarter($refAyat->getHizbQuarter());
                $ayat->setText($ayah);
                $this->em->persist($ayat);
            }

            $this->em->flush();

            return Http\Response::json($response,
                $yml->identifier . ' imported.',
                201
            );

        } catch (Exception $e) {
            $this->logger->error('Quran Importer: ' . $e->getMessage());

            return Http\Response::json($response,
                "Import failed: " . $e->getMessage(),
                400
            );
        }

    }

    public function updateEdition(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $secretkey = $this->container->get('config')['kipchak.api']['importerkey'];
        $apikey = Http\Request::getQueryParam($request, 'apikey');
        $editionId = Http\Request::getAttribute($request, 'edition');

        if ($secretkey !== $apikey) {
            return Http\Response::json($response,
                "Unable to verify key.",
                401
            );
        }

        $files = $request->getUploadedFiles();

        if (count($files) < 1) {
            return Http\Response::json($response, 'Please provide a file with 6236 lines', 400);
        }

        foreach ($files as $file) {
            /**
             * @var $file UploadedFileInterface
             */
            if ($file->getClientFilename() === 'edition.txt') {
                $text = (string) $file->getStream();
            }
        }

        $ayahs = explode("\n", $text);

        if (count($ayahs) !== 6236) {
            return Http\Response::json($response,
                'edition.txt must contain 6236 lines. It currently contains ' . count($ayahs) . '.',
                400
            );
        }

        try {
            // Get Edition
            $edition = $this->em->getRepository(Edition::class)->findOneBy(['identifier' => $editionId]);
            if ($edition === null) {
                return Http\Response::json($response, 'Unable to find edition with ID: ' . $editionId, 404);
            }

            // Get all Ayahs
            $ayahEntities = $this->em->getRepository(Ayat::class)->findBy(['edition' => $edition]);
            if (count($ayahEntities) !== 6236) {
                return Http\Response::json($response,
                    'This edition does not contain the right number of ayahs: ' . count($ayahEntities) . '.',
                    400
                );
            }
            $count = 0;
            foreach ($ayahEntities as $ayat) {
                $count++;
                if ($ayat->getNumber() !== $count) {
                    $ayat->setText($ayahs[$count]);
                    $this->em->persist($ayat);
                }
            }

            $this->em->flush();

            return Http\Response::json($response,
                $edition->getIdentifier() . ' updated.',
                200
            );

        } catch (Exception $e) {
            $this->logger->error('Quran Importer: ' . $e->getMessage());

            return Http\Response::json($response,
                "Import failed: " . $e->getMessage(),
                400
            );
        }
    }

}
