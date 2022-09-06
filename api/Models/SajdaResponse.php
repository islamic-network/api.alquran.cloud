<?php

namespace Api\Models;

use Api\Entities\Doctrine\Primary\Sajda;
use Api\Entities\Doctrine\Primary\Edition;
use Api\Entities\Doctrine\Primary\Ayat;
use Api\Entities\Doctrine\Primary\Surat;
use Doctrine\ORM\EntityManager;

/**
 * Class SajdaResponse
 * @package Api\Models
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
    public function __construct(EntityManager $em, $edition = 'quran-simple')
    {
        parent::__construct($em);

        $this->edition = (new EditionResponse($this->entityManager, null, null, null, null, false))->getEditionByIdentifier($edition);

        $this->load();


    }

    /**
     * @param $number
     */
    public function load()
    {
            //$sajda = $this->entityManager->getRepository('\Api\Entities\Doctrine\Primary\Sajda')->findAll();
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
        $ayats = new AyatResponse($this->entityManager, null, $this->edition->getIdentifier(), false, false, true);

        // Load juz ayahs first.
        $ayats->loadBySajda();
        $j['ayahs'] = $ayats->getResponse();
        $j['edition'] = (new EditionResponse($this->entityManager, $this->edition->getIdentifier()))->getResponse();


        return $j;
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
