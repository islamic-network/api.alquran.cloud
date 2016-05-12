<?php

namespace Quran\Api;

use Quran\Entity\Ayat;
use Quran\Entity\Edition;

/**
 * Class AyatResponse
 * @package Quran\Api
 */
class AyatResponse extends QuranResponse
{

    /**
     * @var
     */
    private $response;

    /**
     * @var
     */
    private $edition;

    /**
     * @var bool
     */
    private $includeEdition;

    /**
     * @var bool
     */
    private $includeSurat;

    /**
     * @var bool
     */
    private $all;

    /**
     * @param null $number
     * @param string $edition
     * @param bool|false $all
     * @param bool|true $includeEdition
     * @param bool|true $includeSurat
     */
    public function __construct($number = null, $edition = 'quran-simple', $all = false, $includeEdition = true, $includeSurat = true)
    {
        parent::__construct();

        $this->edition = (new EditionResponse())->getEditionByIdentifier($edition);

        $this->all = $all;

        $this->includeEdition = $includeEdition;
        $this->includeSurat = $includeSurat;

        $this->load(self::sanitizeNumber($number));


    }


    /**
     * @param $number
     * @return array|int
     */
    public static function sanitizeNumber($number)
    {
        if ($number === null) {

            return $number;
        }

        if (is_numeric($number)) {

            return (int) $number;
        }

        $parts = explode(':', $number);
        if (count($parts) === 2) {

            return [$parts[0], $parts[1]];
        }
    }

    /**
     * @param $number
     */
    public function load($number)
    {
        if ($this->all) {
            // Load everything
            $this->loadAll();

            return $number;
        }
        if ($number === null) {
            $this->setCode(400);
            $this->setStatus('Bad Request');
            $this->response = 'Please specify an Ayah number (1 to 6326) or a reference in the format Surah:Ayat (2:255).';

            return $number;
        }

        if (is_array($number)) {
            return $this->loadByReference($number[0], $number[1]);
        } else {
            if ($number < 1 || $number > 6326) {
                $this->setCode(400);
                $this->setStatus('Bad Request');
                $this->response = 'Please specify an Ayah number (1 to 6326).';
            } else {
                return $this->loadByNumber($number);
            }
        }
    }

    /**
     * Loads All Ayahs
     */
    public function loadAll()
    {
        $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['edition' => $this->edition]);
        $this->response = $this->prepare($ayat);
        $this->setCode(200);
        $this->setStatus('OK');
    }

    /**
     * @param $number
     */
    public function loadByNumber($number)
    {
        $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findOneBy(['number' => $number, 'edition' => $this->edition]);
        if ($ayat) {
            $this->response = $this->prepare($ayat);
            $this->setCode(200);
            $this->setStatus('OK');
        } else {
            $this->setCode(400);
            $this->setStatus('Bad Request');
            $this->response = 'Please specify an Ayah number (1 to 6326)';
        }
    }

    /**
     * @param $surat
     * @param $ayat
     */
    public function loadByReference($surat, $ayat)
    {
        $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findOneBy(['surat' => $surat, 'numberInSurat' => $ayat, 'edition' => $this->edition]);
        if ($ayat) {
            $this->response = $this->prepare($ayat);
            $this->setCode(200);
            $this->setStatus('OK');
        } else {
            $this->setCode(400);
            $this->setStatus('Bad Request');
            $this->response = 'Please specify a valid surah reference in the format Surah:Ayat (2:255).';
        }
    }

    /**
     * @param $surat
     */
    public function loadBySurat($surat)
    {
        $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['surat' => $surat, 'edition' => $this->edition]);
        if ($ayat) {
            $this->response = $this->prepare($ayat);
            $this->setCode(200);
            $this->setStatus('OK');
        } else {
            $this->setCode(400);
            $this->setStatus('Bad Request');
            $this->response = 'Please specify a valid surah (1 to 114).';
        }
    }

    /**
     * @param $juz
     */
    public function loadByJuz($juz)
    {
        $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['juz' => $juz, 'edition' => $this->edition]);
        $this->response = $this->prepare($ayat);
        $this->setCode(200);
        $this->setStatus('OK');
    }


    /**
     * @param $ayat
     * @return array
     */
    private function prepare($ayat)
    {
        if (is_array($ayat)) {
            foreach($ayat as $ayah) {
                $ax['number'] = $ayah->getNumber();
                $ax['text'] = $ayah->getText();
                if ($this->includeEdition) {
                    $ax['edition'] = [
                        'identifier' => $ayah->getEdition()->getIdentifier(),
                        'language' => $ayah->getEdition()->getLanguage(),
                        'name' => $ayah->getEdition()->getName(),
                        'englishName' => $ayah->getEdition()->getEnglishName(),
                        //'format' => $ayah->getEdition()->getFormat(),
                        'type' => $ayah->getEdition()->getType()
                    ];
                }
                if ($this->includeSurat) {
                    $ax['surah'] = [
                        'number' => $ayah->getSurat()->getId(),
                        'name' => $ayah->getSurat()->getName(),
                        'englishName' => $ayah->getSurat()->getEnglishName(),
                        'englishNameTranslation' => $ayah->getSurat()->getEnglishTranslation(),
                        'revelationType' => $ayah->getSurat()->getRevelationCity()
                    ];
                }
                $ax['numberInSurah'] = $ayah->getNumberInSurat();

                $a[] = $ax;
            }

        } else {
            $a = [
                'number' => $ayat->getNumber(),
                'text' => $ayat->getText(),
                'edition' => (new EditionResponse($ayat->getEdition()->getIdentifier()))->getResponse(),
                'surah' => (new SuratResponse($ayat->getSurat()->getId()))->getResponse(),
                'numberInSurah' => $ayat->getNumberInSurat()
            ];
        }

        return $a;
    }

    /**
     * @return $this
     */
    public function get() {

        $this->set($this->status, $this->code, $this->response);

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResponse() {

        return $this->response;
    }

}