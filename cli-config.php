<?php

require_once (realpath(__DIR__ . '/vendor/autoload.php'));

use Mamluk\Kipchak\Api;

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Console\EntityManagerProvider\SingleManagerProvider;
use Psr\Container\ContainerInterface;

$app = Api::bootCli();
/**
 * ContainerInterface
 */
$container = $app->getContainer();

$ems = $container->get('config')['kipchak.doctrine']['orm']['entity_managers'];

$entityManagers = [];

$commands = [
    // If you want to add your own custom console commands,
    // you can do so here.
];

foreach ($ems as $name => $em) {
    ConsoleRunner::run(
        new SingleManagerProvider($container->get('database.doctrine.entitymanager.' . $name), $name),
        $commands
    );
}

// Now entity managers can be run on the CLI via:
// php vendor/bin/doctrine orm:info --em primary (primary here being defined in kipchak.doctrine as an entity_manager in the orm section



