<?php

namespace Api\Models;

use Api\Entities\Doctrine\Primary\Surat;
use Api\Entities\Doctrine\Primary\Edition;
use Api\Entities\Doctrine\Primary\Ayat;
use Doctrine\ORM\EntityManager;

/**
 * Class SuratResponse
 * @package Api\Models
 */
class SuratResponse extends QuranResponse
{
    /**
     * @var
     */
    private $suratEM;

    /**
     * @var
     */
    private $response;

    /**
     * @var bool
     */
    private $ayats = false;

    /**
     * @var
     */
    private $edition;

    /**
     * @var bool
     */
    private $loadEdition;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $limit;

    /**
     * @param null $number
     * @param bool|false $ayats
     * @param string $edition
     * @param bool|false $loadEdition
     */
    public function __construct(EntityManager $em, $number = null, $ayats = false, $edition = 'quran-simple', $loadEdition = false, $offset = null, $limit = null)
    {
        parent::__construct($em);

        $this->ayats = $ayats;

        $this->edition = (new EditionResponse($this->entityManager, null, null, null, false))->getEditionByIdentifier($edition);

        $this->loadEdition = $loadEdition;

        $this->offset = $offset;

        $this->limit = $limit;

        $this->load(self::sanitizeNumber($number));


    }

    /**
     * @param $number
     * @return int
     */
    public static function sanitizeNumber($number)
    {
        if ($number === null) {
            return $number;
        }
        $number = (int) $number;

        return $number;
    }

    /**
     * @param $number
     */
    public function load($number)
    {

        if ($number === null) {
            $this->suratEM = $this->entityManager->getRepository('\Api\Entities\Doctrine\Primary\Surat')->findAll();
            if ($this->suratEM) {
                $this->response = $this->prepareAll();
                $this->setCode(200);
                $this->setStatus('OK');
            }
        } else  {
            $this->suratEM = $this->entityManager->getRepository('\Api\Entities\Doctrine\Primary\Surat')->find($number);
            if ($this->suratEM) {
                $this->response = $this->prepare($this->suratEM);
                $this->setCode(200);
                $this->setStatus('OK');
            } else {
                $this->setCode(404);
                $this->setStatus('Not Found');
                $this->response ='Surat number should be between 1 and 114.';
            }
        }
    }

    /**
     * @return array
     */
    private function prepareAll()
    {
        $response = [];
        foreach ($this->suratEM as $surat) {
            $response[] = $this->prepare($surat);
        }

        return $response;
    }

    /**
     * @param $surat
     * @return array
     */
    private function prepare($surat)
    {
        if ($this->ayats) {
            $ayats = new AyatResponse($this->entityManager, null, $this->edition->getIdentifier(), false, false, false);
            if ($this->limit == null) {
                $this->limit = $surat->getNumberOfAyats();
            }
            $ayats->loadBySurat($surat->getId(), $this->offset, $this->limit);
            $s = [
                'number' => $surat->getId(),
                'name' => $surat->getName(),
                'englishName' => $surat->getEnglishName(),
                'englishNameTranslation' => $surat->getEnglishTranslation(),
                'revelationType' => $surat->getRevelationCity(),
                'numberOfAyahs' => $surat->getNumberOfAyats(),
                'ayahs' => $ayats->getResponse()
            ];
        } else {
            $s = [
                'number' => $surat->getId(),
                'name' => $surat->getName(),
                'englishName' => $surat->getEnglishName(),
                'englishNameTranslation' => $surat->getEnglishTranslation(),
                'numberOfAyahs' => $surat->getNumberOfAyats(),
                'revelationType' => $surat->getRevelationCity()
            ];
        }
        if ($this->loadEdition) {
            $s['edition'] = (new EditionResponse($this->entityManager, $this->edition->getIdentifier()))->getResponse();
        }

        return $s;
    }

    /**
     * @return $this
     */
    public function get() {

        $this->set($this->status, $this->code, $this->response);

        return $this->data;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

}
