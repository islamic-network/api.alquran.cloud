<?php

namespace Api\Models;

use Api\Entities\Doctrine\Primary\Complete;
use Api\Entities\Doctrine\Primary\Edition;
use Api\Entities\Doctrine\Primary\Ayat;
use Doctrine\ORM\EntityManager;

/**
 * Class CompleteResponse
 * @package Api\Models
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

    /**
     * @var
     */
    private $edition;


    /**
     * @param string $edition
     */
    public function __construct(EntityManager $em, $edition = 'quran-simple')
    {
        parent::__construct($em);

        $this->edition = (new EditionResponse($this->entityManager, null, null, null, null, false))->getEditionByIdentifier($edition);

        $this->load();

    }

    /**
     *
     */
    public function load()
    {
        $this->response = $this->prepare();
        $this->setCode(200);
        $this->setStatus('OK');

    }

    /**
     * @return array
     */
    private function prepare()
    {
        foreach ($this->entityManager->getRepository('\Api\Entities\Doctrine\Primary\Surat')->findAll() as $surat) {
            $ayats = new AyatResponse($this->entityManager, null, $this->edition->getIdentifier(), false, false, false);
            $ayats->loadBySurat($surat->getId());
            $s[] = [
                'number' => $surat->getId(),
                'name' => $surat->getName(),
                'englishName' => $surat->getEnglishName(),
                'englishNameTranslation' => $surat->getEnglishTranslation(),
                'revelationType' => $surat->getRevelationCity(),
                'ayahs' => $ayats->getResponse()
            ];
        }
        $j = [
            'surahs' => $s
        ];

        $j['edition'] = [
            'identifier' => $this->edition->getIdentifier(),
            'language' => $this->edition->getLanguage(),
            'name' => $this->edition->getName(),
            'englishName' => $this->edition->getEnglishName(),
            'format' => $this->edition->getFormat(),
            'type' => $this->edition->getType()
        ];


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
