<?php

namespace Quran\Api;

use Quran\Entity\Page;
use Quran\Entity\Edition;
use Quran\Entity\Ayat;
use Quran\Entity\Surat;

/**
 * Class PageResponse
 * @package Quran\Api
 */
class PageResponse extends QuranResponse
{
    /**
     * @var
     */
    private $pageEM;

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
    public function __construct($number = null, $edition = 'quran-simple',  $offset = null, $limit = null)
    {
        parent::__construct();

        $this->edition = (new EditionResponse(null, null, null, null, false))->getEditionByIdentifier($edition);

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

        if ($number === null || $number < 1 || $number > 604) {
            $this->response = 'Page number should be betwen 1 and 604';
            $this->setCode(404);
            $this->setStatus('Not Found');
        } else  {
            $page = $this->entityManager->getRepository('\Quran\Entity\Page')->find($number);
            $this->response = $this->prepare($page);
            $this->setCode(200);
            $this->setStatus('OK');

        }
    }

    /**
     * @param $page
     * @return array
     */
    private function prepare($page)
    {
        $ayats = new AyatResponse(null, $this->edition->getIdentifier(), false, false, true);
        if ($this->limit == null) {
            $this->limit = 2000; // No juz has this many ayahs, so this limit is high enough.
        }

        // Load juz ayahs first.
        $ayats->loadByPage($page->getId(), $this->offset, $this->limit);
        $j = [
            'number' => $page->getId(),
            'ayahs' => $ayats->getResponse()
        ];

        // Now load juz surahs and add to the response.
        $ayats->loadAyahSurahsByPage($page->getId(), $this->offset, $this->limit);
        $j['surahs'] = $ayats->getResponse();

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
