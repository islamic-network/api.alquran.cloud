<?php

namespace Quran\Api;

use Quran\Entity\Juz;
use Quran\Entity\Edition;
use Quran\Entity\Ayat;

/**
 * Class JuzResponse
 * @package Quran\Api
 */
class JuzResponse extends QuranResponse
{
    /**
     * @var
     */
    private $juzEM;

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
    public function __construct($number = null, $edition = 'quran-simple')
    {
        parent::__construct();

        $this->edition = (new EditionResponse())->getEditionByIdentifier($edition);

        $this->load(self::sanitizeNumber($number));


    }

    /**
     * @param $number
     * @return int
     */
    public static function sanitizeNumber($number)
    {
        if ($number === null) {
            return $number;
        }
        $number = (int) $number;

        return $number;
    }

    /**
     * @param $number
     */
    public function load($number)
    {

        if ($number === null || $number < 1 || $number > 30) {
            $this->response = 'Juz number should be betwen 1 and 30';
            $this->setCode(400);
            $this->setStatus('Bad Request');
        } else  {
            $juz = $this->entityManager->getRepository('\Quran\Entity\Juz')->find($number);
            $this->response = $this->prepare($juz);
            $this->setCode(200);
            $this->setStatus('OK');

        }
    }

    /**
     * @param $juz
     * @return array
     */
    private function prepare($juz)
    {
        $ayats = new AyatResponse(null, $this->edition->getIdentifier(), false, false, true);
        $ayats->loadByJuz($juz->getId());
        $j = [
            'number' => $juz->getId(),
            'ayahs' => $ayats->getResponse()
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

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

}