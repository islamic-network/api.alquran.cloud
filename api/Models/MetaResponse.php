<?php

namespace Api\Models;

use Api\Utils\Meta;
use Doctrine\ORM\EntityManager;

/**
 * Class MetaResponse
 * @package Api\Models
 */
class MetaResponse extends QuranResponse
{

    /**
     * @var
     */
    private $response;

    /**
     * @var Quran\Helper\Meta
     */
    private $meta;

    /**
     * @param null $number
     * @param string $edition
     */
    public function __construct(EntityManager $em)
    {
        parent::__construct($em);

        $this->meta = new Meta();

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
     * @param $sajda
     * @return array
     */
    private function prepare()
    {
        $r['ayahs'] = ['count' => 6236];;
        $r['surahs'] = ['count' => 114, 'references' => (new SuratResponse($this->entityManager))->getResponse()];
        $r['sajdas'] = ['count' => count($this->meta->getSajdas()), 'references' => $this->meta->getSajdas()];
        $r['rukus'] = ['count' => count($this->meta->getRukus()), 'references' => $this->meta->getRukus()];
        $r['pages'] = ['count' => count($this->meta->getPages()), 'references' => $this->meta->getPages()];
        $r['manzils'] = ['count' => count($this->meta->getManzils()), 'references' => $this->meta->getManzils()];
        $r['hizbQuarters'] = ['count' => count($this->meta->getHizbQuarters()), 'references' => $this->meta->getHizbQuarters()];
        $r['juzs'] = ['count' => count($this->meta->getJuzs()), 'references' => $this->meta->getJuzs()];

        return $r;
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
