<?php

namespace Quran\Api;

use Quran\Entity\Edition;

/**
 * Class EditionResponse
 * @package Quran\Api
 */
class EditionResponse extends QuranResponse
{
    /**
     * @var
     */
    private $editionEM;

    /**
     * @var
     */
    private $response;

    /**
     * @param null $number
     * @param bool|false $surats
     */
    public function __construct($identifier = null, $type = null, $language = null)
    {
        parent::__construct();

        $this->load($identifier, $type, $language);


    }

    /**
     * @param $number
     */
    public function load($identifier, $type, $language)
    {

        if ($identifier === null) {
            $filter['format'] = 'text';
            if ($type !== null) {
                $filter['type'] = $type;
                $filtertype = 'type';
            }
            if ($language !== null) {
                $filter['language'] = $language;
                $filtertype = 'language';
                
            }

            $this->editionEM = $this->entityManager->getRepository('\Quran\Entity\Edition')->findBy($filter);
            if ($this->editionEM) {
                $this->response = $this->prepareAll();
                $this->setCode(200);
                $this->setStatus('OK');
            } else {
                $this->setCode(400);
                $this->setStatus('Bad Request');
                $this->response ='Invalid ' . $filtertype . '.';
            }
        } else  {
            $this->editionEM = $this->entityManager->getRepository('\Quran\Entity\Edition')->findOneBy(['identifier' => $identifier]);
            if ($this->editionEM) {
                $this->response = $this->prepare($this->editionEM);
                $this->setCode(200);
                $this->setStatus('OK');
            } else {
                $this->setCode(400);
                $this->setStatus('Bad Request');
                $this->response ='Invalid identifier - such an edition does not exist.';
            }
        }
    }

    /**
     * @return array
     */
    private function prepareAll()
    {
        $response = [];
        foreach ($this->editionEM as $edition) {
            $response[] = $this->prepare($edition);
        }

        return $response;
    }

    /**
     * @param $surat
     * @return array
     */
    private function prepare($edition)
    {
            $e = [
                'identifier' => $edition->getIdentifier(),
                'language' => $edition->getLanguage(),
                'name' => $edition->getName(),
                'englishName' => $edition->getEnglishName(),
                //'format' => $edition->getFormat(),
                'type' => $edition->getType()
            ];

        return $e;
    }

    /**
     * @return $this
     */
    public function get() {

        $this->set($this->status, $this->code, $this->response);

        return $this;
    }
    
    /**
     * Defaults to quran-simple
     **/
    
    public function getEditionByIdentifier($identifier)
    {
        $edition = $this->entityManager->getRepository('\Quran\Entity\Edition')->findOneBy(['identifier' => $identifier]);
        if (!$edition || $edition === null) {
            return $this->entityManager->getRepository('\Quran\Entity\Edition')->findOneBy(['identifier' => 'quran-simple']);
        } else {
            return $edition;
        }
                                                                                            
    }
    
    public function getResponse()
    {
        return $this->response;
    }
}