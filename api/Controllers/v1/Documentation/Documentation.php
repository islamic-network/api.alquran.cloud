<?php

namespace Api\Controllers\v1\Documentation;

class Documentation
{
    public string $dir;

    public function __construct()
    {
        $this->dir = realpath(__DIR__ . '/../../../');
    }
}