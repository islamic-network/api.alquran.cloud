<?php


namespace Quran\Helper;

class Database
{
    private $config;
    private $cacher;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->config = new Config();
        $this->cacher = new Cacher();
    }

    /**
     * Returns a connection to the database
     * @return DriveManager
     */
    public function getConnection($connection = 'database')
    {
        $config = new \Doctrine\DBAL\Configuration();

        $c = $this->config->connection($connection);

        $connectionParams = array(
            'dbname' => $c->dbname,
            'user' => $c->username,
            'password' => $c->password,
            'host' => $c->host,
            'port' => $c->port,
            'driver' => 'pdo_mysql',
        );

        return \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
    }

}
