<?php

namespace Quran\Api;

use Doctrine\ORM\EntityManager;

/**
 * Class QuranResponse
 * @package Quran\Api
 */
class QuranResponse
{
    /**
     * @var
     */
    public $code;

    /**
     * @var
     */
    public $status;

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
    public function __construct() {
        global $dbParams, $dbConfig;
        $this->entityManager = EntityManager::create($dbParams, $dbConfig);
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