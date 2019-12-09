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
     * @param null $identifier
     * @param null $type
     * @param null $language
     * @param null $format
     */
    public function __construct($identifier = null, $type = null, $language = null, $format = null, $load = true)
    {
        parent::__construct();

        if ($load) {
            $this->load($identifier, $type, $language, $format);
        }

    }

    /**
     * @param $identifier
     * @param $type
     * @param $language
     * @param $format
     */
    public function load($identifier, $type, $language, $format)
    {

        if ($identifier === null) {
            $filter = [];
            if ($type !== null) {
                $filter['type'] = $type;
                $filtertype = 'type';
            }
            if ($language !== null) {
                $filter['language'] = $language;
                $filtertype = 'language';

            }
            if ($format !== null) {
                $filter['format'] = $format;
                $filtertype = 'format';

            }

            $this->editionEM = $this->entityManager->getRepository('\Quran\Entity\Edition')->findBy($filter);
            if ($this->editionEM) {
                $this->response = $this->prepareAll();
                $this->setCode(200);
                $this->setStatus('OK');
            } else {
                $this->setCode(404);
                $this->setStatus('Not Found');
                $this->response ='Invalid ' . $filtertype . '.';
            }
        } else  {
            $this->editionEM = $this->entityManager->getRepository('\Quran\Entity\Edition')->findOneBy(['identifier' => $identifier]);
            if ($this->editionEM) {
                $this->response = $this->prepare($this->editionEM);
                $this->setCode(200);
                $this->setStatus('OK');
            } else {
                $this->setCode(404);
                $this->setStatus('Not Found');
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
     * @param Edition $edition
     * @return array
     */
    private function prepare($edition)
    {
        $e = [
            'identifier' => $edition->getIdentifier(),
            'language' => $edition->getLanguage(),
            'name' => $edition->getName(),
            'englishName' => $edition->getEnglishName(),
            'format' => $edition->getFormat(),
            'type' => $edition->getType(),
            'direction' => $edition->getDirection()
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
     * @param $identifier
     * @return mixed
     */
    public function getEditionByIdentifier($identifier)
    {
        $edition = $this->entityManager->getRepository('\Quran\Entity\Edition')->findOneBy(['identifier' => $identifier]);
        if (!$edition || $edition === null) {
            return $this->entityManager->getRepository('\Quran\Entity\Edition')->findOneBy(['identifier' => 'quran-simple']);
        } else {
            return $edition;
        }

    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}
