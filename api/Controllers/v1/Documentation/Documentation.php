<?php

namespace Api\Controllers\v1\Documentation;

use Mamluk\Kipchak\Components\Controllers\Slim;
use Psr\Container\ContainerInterface;

class Documentation extends Slim
{
    public string $dir;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->dir = realpath(__DIR__ . '/../../../');
    }
}