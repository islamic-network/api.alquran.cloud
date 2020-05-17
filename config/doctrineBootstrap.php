<?php
// doctrineBootstrap.php
require_once realpath(__DIR__) . '/../vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Quran\Helper\Cacher;
use Quran\Helper\Config;

$paths = array(realpath(__DIR__) . '/../src');

$isDevMode = false;
if (!$isDevMode) {
    $cache = new \Doctrine\Common\Cache\MemcachedCache;
    $cacher = new Cacher();
    $cache->setMemcached($cacher->getMemcached());
}

// the connection configuration
$connection = $cacher->get('DB_CONNECTION');
if (!$connection) {
    $databaseConfig = (new Config())->connection();
} else {
    $databaseConfig = (new Config())->connection($cacher->get('DB_CONNECTION'));
}

$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => $databaseConfig->username,
    'password' => $databaseConfig->password,
    'dbname'   => $databaseConfig->dbname,
    'host'     => $databaseConfig->host,
    'port'     => $databaseConfig->port,
    'charset'  => 'utf8'
);
$dbConfig = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, $cache);
