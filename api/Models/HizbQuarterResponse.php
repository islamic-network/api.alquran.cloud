<?php

namespace Api\Models;

use Api\Entities\Doctrine\Primary\HizbQuarter;
use Api\Entities\Doctrine\Primary\Edition;
use Api\Entities\Doctrine\Primary\Ayat;
use Api\Entities\Doctrine\Primary\Surat;
use Doctrine\ORM\EntityManager;

/**
 * Class HizbQuarterResponse
 * @package Api\Models
 */
class HizbQuarterResponse extends QuranResponse
{
    /**
     * @var
     */
    private $hizbQuarterEM;

    /**
     * @var
     */
    private $response;

    /**
     * @var
     */
    private $edition;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $limit;


    /**
     * @param null $number
     * @param string $edition
     */
    public function __construct(EntityManager $em, $number = null, $edition = 'quran-simple',  $offset = null, $limit = null)
    {
        parent::__construct($em);

        $this->edition = (new EditionResponse($this->entityManager, null, null, null, null, false))->getEditionByIdentifier($edition);

        $this->offset = $offset;

        $this->limit = $limit;

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

        if ($number === null || $number < 1 || $number > 240) {
            $this->response = 'HizbQuarter number should be betwen 1 and 240';
            $this->setCode(404);
            $this->setStatus('Not Found');
        } else  {
            $hq = $this->entityManager->getRepository('\Api\Entities\Doctrine\Primary\HizbQuarter')->find($number);
            $this->response = $this->prepare($hq);
            $this->setCode(200);
            $this->setStatus('OK');

        }
    }

    /**
     * @param $hizbQuarter
     * @return array
     */
    private function prepare($hizbQuarter)
    {
        $ayats = new AyatResponse($this->entityManager, null, $this->edition->getIdentifier(), false, false, true);
        if ($this->limit == null) {
            $this->limit = 2000; // No juz has this many ayahs, so this limit is high enough.
        }

        // Load juz ayahs first.
        $ayats->loadByHizbQuarter($hizbQuarter->getId(), $this->offset, $this->limit);
        $j = [
            'number' => $hizbQuarter->getId(),
            'ayahs' => $ayats->getResponse()
        ];

        // Now load juz surahs and add to the response.
        $ayats->loadAyahSurahsByHizbQuarter($hizbQuarter->getId(), $this->offset, $this->limit);
        $j['surahs'] = $ayats->getResponse();

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
