<?php

namespace Api\Controllers\v1\Documentation;
use Api\Utils\Response;
use OpenApi as OApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Juz extends Documentation
{
    public function generate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $openapi = OApi\Generator::scan([$this->dir . '/Controllers/v1/Juz.php']);

        return Response::raw($response, $openapi->toYaml(), 200, ['Content-Type' => 'text/yaml']);
    }
}