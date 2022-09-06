<?php

namespace Api\Models;

use Api\Entities\Doctrine\Primary\Juz;
use Api\Entities\Doctrine\Primary\Edition;
use Api\Entities\Doctrine\Primary\Ayat;
use Api\Entities\Doctrine\Primary\Surat;
use Doctrine\ORM\EntityManager;

/**
 * Class JuzResponse
 * @package Api\Models
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

        if ($number === null || $number < 1 || $number > 30) {
            $this->response = 'Juz number should be betwen 1 and 30';
            $this->setCode(404);
            $this->setStatus('Not Found');
        } else  {
            $juz = $this->entityManager->getRepository('\Api\Entities\Doctrine\Primary\Juz')->find($number);
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
        $ayats = new AyatResponse($this->entityManager, null, $this->edition->getIdentifier(), false, false, true);
        if ($this->limit == null) {
            $this->limit = 1000; // No juz has this many ayahs, so this limit is high enough.
        }

        // Load juz ayahs first.
        $ayats->loadByJuz($juz->getId(), $this->offset, $this->limit);
        $j = [
            'number' => $juz->getId(),
            'ayahs' => $ayats->getResponse()
        ];

        // Now load juz surahs and add to the response.
        $ayats->loadAyahSurahsByJuz($juz->getId(), $this->offset, $this->limit);
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
