<?php
/**
use Psr\Container\ContainerInterface;

$container->set('some_dependency', function(ContainerInterface $c) {
    $dep = new \Path\To\Dependency();

    return $dep;
});
*/