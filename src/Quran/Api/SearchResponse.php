<?php

namespace Quran\Api;

use Quran\Entity\Complete;
use Quran\Entity\Edition;
use Quran\Entity\Ayat;

/**
 * Class SearchResponse
 * @package Quran\Api
 */
class SearchResponse extends QuranResponse
{
    /**
     * @var
     */
    private $response;

    /**
     * @var string
     */
    private $keyword;

    /**
     * @var null|string
     */
    private $surat = null;

    /**
     * @var string
     */
    private $lang;


    /**
     * @param $keyword
     * @param string $surat
     * @param string $lang
     */
    public function __construct($keyword, $surat = 'all', $lang = '')
    {
        parent::__construct();

        $this->keyword = (string) strtolower($keyword);

        $this->surat = $surat;

        $this->lang = (string) $lang;

        $this->load();

    }


    /**
     * 
     */
    public function load()
    {
        $result = false;
        if ((int) $this->surat > 0 && (int) $this->surat < 115) {
            if ($this->lang != '') {
                $result = $this->entityManager->getRepository("\Quran\Entity\Ayat")->createQueryBuilder('a')
                    ->leftJoin('a.edition', 'e')
                    ->where('LOWER(a.text) LIKE :keyword')
                    ->andWhere('a.surat = :surat')
                    ->andWhere('e.language = :language OR e.identifier = :identifier')
                    ->setParameter('language', $this->lang)
                    ->setParameter('identifier', $this->lang)
                    ->setParameter('keyword', '%' . $this->keyword . '%')
                    ->setParameter('surat', $this->surat)
                    ->getQuery()
                    ->getResult();
            } else {
                $result = $this->entityManager->getRepository("\Quran\Entity\Ayat")->createQueryBuilder('a')
                    ->leftJoin('a.edition', 'e')
                    ->where('LOWER(a.text) LIKE :keyword')
                    ->andWhere('a.surat = :surat')
                    ->setParameter('keyword', '%' . $this->keyword . '%')
                    ->setParameter('surat', $this->surat)
                    ->getQuery()
                    ->getResult();
            }
        } else if ($this->surat == 'all') {
            if ($this->lang != '') {
                $result = $this->entityManager->getRepository("\Quran\Entity\Ayat")->createQueryBuilder('a')
                    ->leftJoin('a.edition', 'e')
                    ->where('LOWER(a.text) LIKE :keyword')
                    ->andWhere('e.language = :language OR e.identifier = :identifier')
                    ->setParameter('keyword', '%' . $this->keyword . '%')
                    ->setParameter('language', $this->lang)
                    ->setParameter('identifier', $this->lang)
                    ->getQuery()
                    ->getResult();
            } else {
                $result = $this->entityManager->getRepository("\Quran\Entity\Ayat")->createQueryBuilder('a')
                    ->leftJoin('a.edition', 'e')
                    ->where('LOWER(a.text) LIKE :keyword')
                    ->setParameter('keyword', '%' . $this->keyword . '%')
                    ->getQuery()
                    ->getResult();
            }
        }

        if (!$result || $result === null) {
            $this->response = 'Nothing matching your search was found..';
            $this->setCode(204);
            $this->setStatus('Bad Request');
        } else {

            $this->response = $this->prepare($result);
            $this->setCode(200);
            $this->setStatus('OK');
        }

    }

    /**
     * @param $ayat
     * @return mixed
     */
    private function prepare($ayat)
    {
        $a['count'] = count($ayat);
        $surahs = [];
        $editions = [];
        foreach($ayat as $ayah) {
            $surahs[] = $ayah->getSurat();
            $ax['number'] = $ayah->getNumber();
            $ax['text'] = $ayah->getText();
            $ax['edition'] = [
                'identifier' => $ayah->getEdition()->getIdentifier(),
                'language' => $ayah->getEdition()->getLanguage(),
                'name' => $ayah->getEdition()->getName(),
                'englishName' => $ayah->getEdition()->getEnglishName(),
                //'format' => $ayah->getEdition()->getFormat(),
                'type' => $ayah->getEdition()->getType()
            ];
            $ax['surah'] = [
                'number' => $ayah->getSurat()->getId(),
                'name' => $ayah->getSurat()->getName(),
                'englishName' => $ayah->getSurat()->getEnglishName(),
                'englishNameTranslation' => $ayah->getSurat()->getEnglishTranslation(),
                'revelationType' => $ayah->getSurat()->getRevelationCity()
            ];
            $ax['numberInSurah'] = $ayah->getNumberInSurat();

            $a['matches'][] = $ax;
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
    public function getResponse()
    {
        return $this->response;
    }

}
