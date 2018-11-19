<?php
// doctrineBootstrap.php
require_once realpath(__DIR__) . '/../vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Cache;

$paths = array(realpath(__DIR__) . '/../src');

// When there is too much traffic, set this to true, enabled a memcached container and update the $dbConfig object accordingly.
$isDevMode = false;
if (!$isDevMode) {
    $cache = new \Doctrine\Common\Cache\MemcachedCache;
    $memcached = new \Memcached();
    $memcached->addServer(getenv('MEMCACHED_HOST'), getenv('MEMCACHED_PORT'));
    $cache->setMemcached($memcached);
}


// the connection configuration

$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => getenv('MYSQL_USER'),
    'password' => getenv('MYSQL_PASSWORD'),
    'dbname'   => getenv('MYSQL_DATABASE'),
    'host'     => getenv('MYSQL_HOST')
);
$dbConfig = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, null, $cache);
//$entityManager = EntityManager::create($dbParams, $config);
