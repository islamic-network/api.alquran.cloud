<?php

namespace Api\Controllers;

use Doctrine\ORM\EntityManager;
use Mamluk\Kipchak\Components\Controllers;
use Psr\Container\ContainerInterface;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;

/**
 * All Controllers extending Controllers\Slim Contain the Service / DI Container as a protected property called $container.
 * Access it using $this->container in your controller.
 * Default objects bundled into a container are:
 * logger - which returns an instance of \Monolog\Logger. This is also a protected property on your controller. Access it using $this->logger.
 */

class AlQuranController extends Controllers\Slim
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var MemcachedAdapter
     */
    protected $mc;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);

        $this->em = $this->container->get('database.doctrine.entitymanager.primary');

        $this->mc = $this->container->get('cache.memcached.cache');
    }
}