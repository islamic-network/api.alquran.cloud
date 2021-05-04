<?php


namespace Quran\Helper;


class Config
{
    /**
     * The Parsyed Yaml config file
     * @var Object
     */
    private $config;

    /**
     * [__construct description]
     */
    public function __construct()
    {
        $this->config = [
            'connections' => [
                'database' => [
                    'host' => getenv('MYSQL_HOST_1'),
                    'username' => getenv('MYSQL_USER'),
                    'password' => getenv('MYSQL_PASSWORD'),
                    'dbname' => getenv('MYSQL_DATABASE'),
                    'port' => getenv('MYSQL_PORT_1')
                ],
                'memcache' => [
                    'host' => getenv('MEMCACHED_HOST'),
                    'port' => getenv('MEMCACHED_PORT')
                ]
            ]
        ];

    }

    /**
     * Gets a specific connection type, for example database or memcached
     * @param String $id Defined in the config.yml file
     * @return Object
     */
    public function connection($id = 'database')
    {
        return (object)$this->config['connections'][$id];
    }

    /**
     * Returns a particular Api key in the Yaml file
     * @param String $id
     * @return Mixed (most likely string)
     */
    public function apiKey($id)
    {
        return $this->config['apikeys'][$id];
    }

    /**
     * Returns the entire config array
     * @return Array The entire config array
     */
    public function getConfig()
    {
        return $this->config;
    }

}
