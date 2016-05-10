<?php
require_once 'doctrineBootstrap.php';

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\EntityManager;

// replace with mechanism to retrieve EntityManager in your app
$entityManager = EntityManager::create($dbParams, $dbConfig);

return ConsoleRunner::createHelperSet($entityManager);