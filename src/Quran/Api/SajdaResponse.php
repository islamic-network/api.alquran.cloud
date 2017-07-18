<?php

namespace Quran\Api;

use Quran\Entity\Sajda;
use Quran\Entity\Edition;
use Quran\Entity\Ayat;
use Quran\Entity\Surat;

/**
 * Class SajdaResponse
 * @package Quran\Api
 */
class SajdaResponse extends QuranResponse
{
    /**
     * @var
     */
    private $sajdaEM;

    /**
     * @var
     */
    private $response;

    /**
     * @var
     */
    private $edition;


    /**
     * @param null $number
     * @param string $edition
     */
    public function __construct($edition = 'quran-simple')
    {
        parent::__construct();

        $this->edition = (new EditionResponse(null, null, null, null, false))->getEditionByIdentifier($edition);

        $this->load();


    }

    /**
     * @param $number
     */
    public function load()
    {
            //$sajda = $this->entityManager->getRepository('\Quran\Entity\Sajda')->findAll();
            $this->response = $this->prepare();
            $this->setCode(200);
            $this->setStatus('OK');

    }

    /**
     * @param $sajda
     * @return array
     */
    private function prepare()
    {
        $ayats = new AyatResponse(null, $this->edition->getIdentifier(), false, false, true);

        // Load juz ayahs first.
        $ayats->loadBySajda();
        $j['ayahs'] = $ayats->getResponse();
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

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

}
