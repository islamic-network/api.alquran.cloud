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

/**
 * All Controllers extending Controllers\Slim Contain the Service / DI Container as a protected property called $container.
 * Access it using $this->container in your controller.
 * Default objects bundled into a container are:
 * logger - which returns an instance of \Monolog\Logger. This is also a protected property on your controller. Access it using $this->logger.
 */

class Quran extends AlQuranController
{

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
