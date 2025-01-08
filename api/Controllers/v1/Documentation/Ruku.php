<?php

namespace Api\Controllers\v1\Documentation;
use Api\Utils\Response;
use OpenApi as OApi;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Ruku extends Documentation
{
    public function generate(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $openapi = OApi\Generator::scan([$this->dir . '/Controllers/v1/Ruku.php']);

        return Response::raw($response, $openapi->toYaml(), 200, ['Content-Type' => 'text/yaml']);
    }
}