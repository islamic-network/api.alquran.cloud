<?php
// doctrineBootstrap.php
require_once realpath(__DIR__) . '/../vendor/autoload.php';

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

$paths = array(realpath(__DIR__) . '/../src');
$isDevMode = false;

// the connection configuration

$dbParams = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'dev',
    'password' => 'dev',
    'dbname'   => 'database',
    'host'     => 'mysql'
);
$dbConfig = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
//$entityManager = EntityManager::create($dbParams, $config);
