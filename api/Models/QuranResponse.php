<?php

namespace Api\Models;

use Doctrine\ORM\EntityManager;

/**
 * Class QuranResponse
 * @package Api\Models
 */
class QuranResponse
{
    /**
     * @var
     */
    protected $code;

    /**
     * @var
     */
    protected $status;

    /**
     * @var
     */
    public $data;

    /**
     * @var EntityManager
     */
    protected $entityManager;


    /**
     * @throws \Doctrine\ORM\ORMException
     */
    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param $status
     * @param $code
     * @param $data
     */
    public function set($status, $code, $data)
    {
        $this->setStatus($status);
        $this->setCode($code);
        $this->setData($data);
    }


}