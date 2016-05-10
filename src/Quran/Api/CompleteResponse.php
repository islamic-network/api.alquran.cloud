<?php

namespace Quran\Api;

use Quran\Entity\Complete;
use Quran\Entity\Edition;
use Quran\Entity\Ayat;

/**
 * Class CompleteResponse
 * @package Quran\Api
 */
class CompleteResponse extends QuranResponse
{
    /**
     * @var
     */
    private $CompleteEM;

    /**
     * @var
     */
    private $response;
    
    private $edition;
    

    /**
     * @param null $number
     * @param bool|false $ayats
     */
    public function __construct($edition = 'quran-simple')
    {
        parent::__construct();
        
        $this->edition = (new EditionResponse())->getEditionByIdentifier($edition);
        
        $this->load();

    }


    /**
     * @param $number
     */
    public function load()
    {
        $this->response = $this->prepare();
        $this->setCode(200);
        $this->setStatus('OK');

    }

    /**
     * @param $surat
     * @return array
     */
    private function prepare()
    {
        foreach ($this->entityManager->getRepository('\Quran\Entity\Surat')->findAll() as $surat) {
            $s[] = (new SuratResponse($surat->getId(), true, $this->edition->getIdentifier(), false))->getResponse();
        }
        $j = [
            'surahs' => $s
        ];
        
        $j['edition'] = (new EditionResponse($this->edition->getIdentifier()))->getResponse();


        return $j;
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