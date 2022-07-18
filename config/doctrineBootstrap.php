<?php
// doctrineBootstrap.php
require_once realpath(__DIR__) . '/../vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Quran\Helper\Cacher;
use Quran\Helper\Config;


$paths = array(realpath(__DIR__) . '/../src');
$cacher = new Cacher();

// the connection configuration
$databaseConfig = (new Config())->connection('database');

$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => $databaseConfig->username,
    'password' => $databaseConfig->password,
    'dbname'   => $databaseConfig->dbname,
    'host'     => $databaseConfig->host,
    'port'     => $databaseConfig->port,
    'charset'  => 'utf8'
);

$isDevMode = false;

if (!$isDevMode) {

    $cache = new \Symfony\Component\Cache\Adapter\MemcachedAdapter($cacher->getMemcached());
    $dbConfig = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null);
    $dbConfig->setQueryCache($cache);
    $dbConfig->setResultCache($cache);
} else {
    $dbConfig = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, null);
}
