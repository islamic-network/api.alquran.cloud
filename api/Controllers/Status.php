<?php

namespace Api\Controllers;

use Mamluk\Kipchak\Components\Controllers;
use Mamluk\Kipchak\Components\Http;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * All Controllers extending Controllers\Slim Contain the Service / DI Container as a protected property called $container.
 * Access it using $this->container in your controller.
 * Default objects bundled into a container are:
 * logger - which returns an instance of \Monolog\Logger. This is also a protected property on your controller. Access it using $this->logger.
 */

class Status extends Controllers\Slim
{

    public function get(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        $this->logger->debug('Checking Status...');

        /**
         * @var $db Connection
         */
        $db = $this->container->get('database.doctrine.dbal.primary');

        /**
         * @var $mc MemcachedAdapter
         */
        $mc = $this->container->get('cache.memcached.cache');

        $mc->get('status',  function (ItemInterface $item) {
            $item->expiresAfter(3);
            return 'up';
        });

        $mcStatus = $mc->getItem('status');

        try {
            $dbResult = $db->fetchAssociative("SELECT id FROM ayat WHERE id = ? ", [7]);
        } catch (Exception $e) {
            $dbResult = false;
        }

        $status = [
            'memcached' => $mcStatus->get() === 'up' ? 'OK' : 'NOT OK',
            'database' => $dbResult === false ? 'NOT OK' : 'OK (' . $dbResult['id']. ')',
        ];

        if ($mcStatus->get() !== 'up' || $dbResult === false) {
            return Http\Response::json($response,
                $status,
                500
            );

        }

        return Http\Response::json($response,
            $status,
            200
        );
    }
}
