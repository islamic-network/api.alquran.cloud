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
    public function getConnection($connection = 'database_pxc_2')
    {
        $config = new \Doctrine\DBAL\Configuration();

        if ($this->cacher !== false &&
            in_array($this->cacher->get('DB_CONNECTION'), ['database_pxc_1', 'database_pxc_2', 'database_pxc_3'])) {
            //$connection = $this->cacher->get('DB_CONNECTION');
            $connection = $connection;
        }

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
