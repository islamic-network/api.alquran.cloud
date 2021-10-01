<?php

namespace Quran\Api;

use Quran\Entity\Ayat;
use Quran\Entity\Edition;
use Quran\Helper\Meta;
use Quran\Helper\Request as RequestHelper;

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

    private $meta;

    /**
     * @var bool
     */
    private $all;

    /**
     * @var bool
     */
    private $protocol = 'http';

    /**
     * Make some changes to font that makes it easy to render the uthmanic text - but without changing the grammatical arabic
     * @var bool
     */
    private $fontHack = false;
    
    private $cache = [];

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

        if (RequestHelper::isHttps()) {
            $this->protocol = 'https';
        }

        // This is very unclean - but it is true to being a hack and does not touch any other part of the API.
        if (isset($_GET['fontHack']) && $_GET['fontHack'] == 'true') {
            $this->fontHack = true;
        }

        $this->meta = new Meta();

        $this->edition = (new EditionResponse(null, null, null, null, false))->getEditionByIdentifier($edition);

        if ($this->edition->getFormat() == 'audio') {
            $this->edition = (new EditionResponse(null, null, null, null, false))->getEditionByIdentifier('quran-uthmani');
            $this->audioEdition = (new EditionResponse(null, null, null, null, false))->getEditionByIdentifier($edition);
        }

        $this->all = $all;

        $this->includeEdition = $includeEdition;
        $this->includeSurat = $includeSurat;

        if ($number !== null) {
            $this->load(self::sanitizeNumber($number));
        }


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
            $this->setCode(404);
            $this->setStatus('Not Found');
            $this->response = 'Please specify an Ayah number (1 to 6236) or a reference in the format Surah:Ayat (2:255).';

            return $number;
        }

        if (is_array($number)) {
            return $this->loadByReference($number[0], $number[1]);
        } else {
            if ($number < 1 || $number > 6236) {
                $this->setCode(404);
                $this->setStatus('Not Found');
                $this->response = 'Please specify an Ayah number (1 to 6236).';
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
            $this->setCode(404);
            $this->setStatus('Not Found');
            $this->response = 'Please specify an Ayah number (1 to 6236)';
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
            $this->setCode(404);
            $this->setStatus('Not Found');
            $this->response = 'Please specify a valid surah reference in the format Surah:Ayat (2:255).';
        }
    }

    /**
     * @param $surat
     */
    public function loadBySurat($surat, $offset = null, $limit = null)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;

        if ($offset == 0 && $limit > 0) {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['surat' => $surat, 'edition' => $this->edition], ['numberInSurat' => 'ASC'], $limit);
        } elseif ($offset ==0 && $limit == 0)  {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['surat' => $surat, 'edition' => $this->edition], ['numberInSurat' => 'ASC']);
        } else {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['surat' => $surat, 'edition' => $this->edition], ['numberInSurat' => 'ASC'], $limit, $offset);
        }
        if ($ayat) {
            $this->response = $this->prepare($ayat);
            $this->setCode(200);
            $this->setStatus('OK');
        } else {
            $this->setCode(404);
            $this->setStatus('Not Found');
            $this->response = 'Please specify a valid surah (1 to 114). If you have specified an offset or a limit, please ensure they are valid integers and fall within the number of ayahs the surah has.';
        }
    }

    /**
     * @param $juz
     */
    public function loadByJuz($juz, $offset = null, $limit = null)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;

        if ($offset == 0) {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['juz' => $juz, 'edition' => $this->edition], ['number' => 'ASC'], $limit);
        } else {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['juz' => $juz, 'edition' => $this->edition], ['number' => 'ASC'], $limit, $offset);
        }
        //$ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['juz' => $juz, 'edition' => $this->edition]);
        $this->response = $this->prepare($ayat);
        $this->setCode(200);
        $this->setStatus('OK');
    }

    /**
     * @param $manzil
     */
    public function loadByManzil($manzil, $offset = null, $limit = null)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;

        if ($offset == 0) {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['manzil' => $manzil, 'edition' => $this->edition], ['number' => 'ASC'], $limit);
        } else {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['manzil' => $manzil, 'edition' => $this->edition], ['number' => 'ASC'], $limit, $offset);
        }
        //$ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['juz' => $juz, 'edition' => $this->edition]);
        $this->response = $this->prepare($ayat);
        $this->setCode(200);
        $this->setStatus('OK');
    }

    /**
     * @param $page
     */
    public function loadByPage($page, $offset = null, $limit = null)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;

        if ($offset == 0) {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['page' => $page, 'edition' => $this->edition], ['number' => 'ASC'], $limit);
        } else {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['page' => $page, 'edition' => $this->edition], ['number' => 'ASC'], $limit, $offset);
        }
        //$ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['juz' => $juz, 'edition' => $this->edition]);
        $this->response = $this->prepare($ayat);
        $this->setCode(200);
        $this->setStatus('OK');
    }

    /**
     * @param $ruku
     */
    public function loadByRuku($ruku, $offset = null, $limit = null)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;

        if ($offset == 0) {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['ruku' => $ruku, 'edition' => $this->edition], ['number' => 'ASC'], $limit);
        } else {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['ruku' => $ruku, 'edition' => $this->edition], ['number' => 'ASC'], $limit, $offset);
        }
        //$ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['juz' => $juz, 'edition' => $this->edition]);
        $this->response = $this->prepare($ayat);
        $this->setCode(200);
        $this->setStatus('OK');
    }

    public function loadBySajda()
    {
        $q = $this->entityManager->createQuery("SELECT a FROM \Quran\Entity\Ayat a WHERE a.sajda IS NOT NULL AND a.edition = {$this->edition->getId()}");
        $ayat = $q->getResult();
        $this->response = $this->prepare($ayat);
        $this->setCode(200);
        $this->setStatus('OK');
    }

    /**
     * @param $hizbQuarter
     */
    public function loadByHizbQuarter($hizbQuarter, $offset = null, $limit = null)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;

        if ($offset == 0) {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['hizbQuarter' => $hizbQuarter, 'edition' => $this->edition], ['number' => 'ASC'], $limit);
        } else {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['hizbQuarter' => $hizbQuarter, 'edition' => $this->edition], ['number' => 'ASC'], $limit, $offset);
        }
        //$ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['juz' => $juz, 'edition' => $this->edition]);
        $this->response = $this->prepare($ayat);
        $this->setCode(200);
        $this->setStatus('OK');
    }


    /**
     * @param $juz
     */
    public function loadAyahSurahsByJuz($juz, $offset = null, $limit = null)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;

        if ($offset == 0) {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['juz' => $juz, 'edition' => $this->edition], ['number' => 'ASC'], $limit);
        } else {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['juz' => $juz, 'edition' => $this->edition], ['number' => 'ASC'], $limit, $offset);
        }

        foreach($ayat as $ayah) {
            $this->cacheSurah($ayah);
        }

        $this->response = $this->cache['surah'];
        $this->setCode(200);
        $this->setStatus('OK');
    }

    /**
     * @param $manzil
     */
    public function loadAyahSurahsByManzil($manzil, $offset = null, $limit = null)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;

        if ($offset == 0) {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['manzil' => $manzil, 'edition' => $this->edition], ['number' => 'ASC'], $limit);
        } else {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['manzil' => $manzil, 'edition' => $this->edition], ['number' => 'ASC'], $limit, $offset);
        }

        foreach($ayat as $ayah) {
            $this->cacheSurah($ayah);
        }

        $this->response = $this->cache['surah'];
        $this->setCode(200);
        $this->setStatus('OK');
    }

    /**
     * @param $page
     */
    public function loadAyahSurahsByPage($page, $offset = null, $limit = null)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;

        if ($offset == 0) {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['page' => $page, 'edition' => $this->edition], ['number' => 'ASC'], $limit);
        } else {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['page' => $page, 'edition' => $this->edition], ['number' => 'ASC'], $limit, $offset);
        }

        foreach($ayat as $ayah) {
            $this->cacheSurah($ayah);
        }

        $this->response = $this->cache['surah'];
        $this->setCode(200);
        $this->setStatus('OK');
    }

    /**
     * @param $ruku
     */
    public function loadAyahSurahsByRuku($ruku, $offset = null, $limit = null)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;

        if ($offset == 0) {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['ruku' => $ruku, 'edition' => $this->edition], ['number' => 'ASC'], $limit);
        } else {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['ruku' => $ruku, 'edition' => $this->edition], ['number' => 'ASC'], $limit, $offset);
        }

        foreach($ayat as $ayah) {
            $this->cacheSurah($ayah);
        }

        $this->response = $this->cache['surah'];
        $this->setCode(200);
        $this->setStatus('OK');
    }

    /**
     * @param $hizbQuarter
     */
    public function loadAyahSurahsByHizbQuarter($hizbQuarter, $offset = null, $limit = null)
    {
        $limit = (int) $limit;
        $offset = (int) $offset;

        if ($offset == 0) {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['hizbQuarter' => $hizbQuarter, 'edition' => $this->edition], ['number' => 'ASC'], $limit);
        } else {
            $ayat = $this->entityManager->getRepository('\Quran\Entity\Ayat')->findBy(['hizbQuarter' => $hizbQuarter, 'edition' => $this->edition], ['number' => 'ASC'], $limit, $offset);
        }

        foreach($ayat as $ayah) {
            $this->cacheSurah($ayah);
        }

        $this->response = $this->cache['surah'];
        $this->setCode(200);
        $this->setStatus('OK');
    }

    /**
     * @param $ayat
     * @return array
     */
    private function prepare($ayat)
    {
        if (isset($this->audioEdition)) {
        // Reset edition back properly.
            $this->edition = $this->audioEdition;
        }
        if (is_array($ayat)) {
            foreach($ayat as $ayah) {
                $ax['number'] = $ayah->getNumber();
                if (isset($this->audioEdition)) {
                    $audioUrls = $this->meta->getAudioUrlsByReciter($this->audioEdition->getIdentifier(), $ayah->getNumber(), $this->protocol);
                    $ax['audio'] = $audioUrls[0];
                    $ax['audioSecondary'] = $audioUrls;
                    
                }
                $ax['text'] = $this->fontHack ? str_replace(['لْءَا', 'لۡءَا'], ['لْآ', 'لْآ'], $ayah->getText()) : $ayah->getText();
                if ($this->includeEdition) {
                    $this->cacheEdition($ayah);
                    $ax['edition'] = $this->cache['edition'][$ayah->getEdition()->getId()];
                }
                if ($this->includeSurat) {
                    $this->cacheSurah($ayah);
                    $ax['surah'] = $this->cache['surah'][$ayah->getSurat()->getId()];
                }
                $ax['numberInSurah'] = $ayah->getNumberInSurat();
                $ax['juz'] = $ayah->getJuz()->getId();
                $ax['manzil'] = $ayah->getManzil()->getId();
                $ax['page'] = $ayah->getPage()->getId();
                $ax['ruku'] = $ayah->getRuku()->getId();
                $ax['hizbQuarter'] = $ayah->getHizbQuarter()->getId();
                $sajda = $ayah->getSajda();
                if ($sajda !== null) {
                    $ax['sajda'] = [
                        'id' => $sajda->getId(),
                        'recommended' => $sajda->getRecommended(),
                        'obligatory' => $sajda->getObligatory(),
                    ];
                } else {
                    $ax['sajda'] = false;
                }

                $a[] = $ax;
            }

        } else {
                $a['number'] = $ayat->getNumber();
                if (isset($this->audioEdition)) {
                    $audioUrls = $this->meta->getAudioUrlsByReciter($this->audioEdition->getIdentifier(), $ayat->getNumber(), $this->protocol);
                    $a['audio'] = $audioUrls[0];
                    $secondaryAudioUrls = $audioUrls;
                    unset($secondaryAudioUrls[0]);
                    $a['audioSecondary'] = $secondaryAudioUrls;
                }
                $a['text'] = $this->fontHack ? str_replace(['لْءَا', 'لۡءَا'], ['لْآ', 'لْآ'], $ayat->getText()) : $ayat->getText();
                $a['edition'] = (new EditionResponse($this->edition->getIdentifier()))->getResponse();
                $a['surah'] = (new SuratResponse($ayat->getSurat()->getId()))->getResponse();
                $a['numberInSurah'] = $ayat->getNumberInSurat();
                $a['juz'] = $ayat->getJuz()->getId();
                $a['manzil'] = $ayat->getManzil()->getId();
                $a['page'] = $ayat->getPage()->getId();
                $a['ruku'] = $ayat->getRuku()->getId();
                $a['hizbQuarter'] = $ayat->getHizbQuarter()->getId();
                $sajda = $ayat->getSajda();
                if ($sajda !== null) {
                    $a['sajda'] = [
                        'id' => $sajda->getId(),
                        'recommended' => $sajda->getRecommended(),
                        'obligatory' => $sajda->getObligatory(),
                    ];
                } else {
                    $a['sajda'] = false;
                }
        }

        unset($this->audioEdition);
        return $a;
    }

    /**
     * @param $ayah
     */
    private function cacheSurah($ayah) {
        if (!isset($this->cache['surah'][$ayah->getSurat()->getId()])) {
            $this->cache['surah'][$ayah->getSurat()->getId()] = [
                        'number' => $ayah->getSurat()->getId(),
                        'name' => $ayah->getSurat()->getName(),
                        'englishName' => $ayah->getSurat()->getEnglishName(),
                        'englishNameTranslation' => $ayah->getSurat()->getEnglishTranslation(),
                        'revelationType' => $ayah->getSurat()->getRevelationCity(),
                        'numberOfAyahs' => $ayah->getSurat()->getNumberOfAyats()
                    ];
        }
    }

    /**
     * @param $ayah
     */
    private function cacheEdition($ayah) {
        if(!isset($this->cache['surah'][$ayah->getEdition()->getId()])) {
            $this->cache['surah'][$ayah->getEdition()->getId()] = [
                        'identifier' => $ayah->getEdition()->getIdentifier(),
                        'language' => $ayah->getEdition()->getLanguage(),
                        'name' => $ayah->getEdition()->getName(),
                        'englishName' => $ayah->getEdition()->getEnglishName(),
                        'format' => $ayah->getEdition()->getFormat(),
                        'type' => $ayah->getEdition()->getType(),
                        'numberOfAyahs' => $ayah->getSurat()->getNumberOfAyats()
                    ];
        }
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
