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
    
    private $keyword;
    
    private $surat = null;
    
    private $lang;
    

    /**
     * @param null $number
     * @param bool|false $ayats
     */
    public function __construct($keyword, $surat = 'all', $lang = 'en')
    {
        parent::__construct();
        
        $this->keyword = (string) strtolower($keyword);
        
        $this->surat = (int) $surat;
        
        $this->lang = (string) $lang;
        
        $this->load();

    }

    

    /**
     * @param $number
     */
    public function load()
    {
        $result = false;
        if ($this->surat > 0 && $this->surat < 115) {
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
        } else if ($this->surat == 'all') {
            $result = $this->entityManager->getRepository("\Quran\Entity\Ayat")->createQueryBuilder('a')
                ->leftJoin('a.edition', 'e')
                ->where('LOWER(a.text) LIKE :keyword')
                ->andWhere('e.language = :language OR e.identifier = :identifier')
                ->setParameter('keyword', '%' . $this->keyword . '%')
                ->setParameter('language', $this->lang)
                ->setParameter('identifier', $this->lang)
                ->getQuery()
                ->getResult();
        }
        
        if (!$result || $result === null) {
            $this->response = 'Nothing matching your search was found..';
            $this->setCode(400);
            $this->setStatus('Bad Request');
        } else {
            
            $this->response = $this->prepare($result);
            $this->setCode(200);
            $this->setStatus('OK');
        }

    }

    /**
     * @param $ayats
     * @return array

     */
    private function prepare($ayat)
    {
        $a['count'] = count($ayat);
        foreach($ayat as $ayah) {
            $ax['number'] = $ayah->getNumber();
            $ax['text'] = $ayah->getText();
            $ax['edition'] = $ayatEdtion = (new EditionResponse($ayah->getEdition()->getIdentifier()))->getResponse();
            $ax['surah'] = (new SuratResponse($ayah->getSurat()->getId()))->getResponse();;
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
    
    public function getResponse()
    {
        return $this->response;
    }

}
