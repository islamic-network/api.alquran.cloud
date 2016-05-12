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

    /**
     * @var
     */
    private $edition;


    /**
     * @param string $edition
     */
    public function __construct($edition = 'quran-simple')
    {
        parent::__construct();

        $this->edition = (new EditionResponse())->getEditionByIdentifier($edition);

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
        foreach ($this->entityManager->getRepository('\Quran\Entity\Surat')->findAll() as $surat) {
            $ayats = new AyatResponse(null, $this->edition->getIdentifier(), false, false, false);
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
            //'format' => $edition->getFormat(),
            'type' => $this->edition->getType()
        ];


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