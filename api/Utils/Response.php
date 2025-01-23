<?php

namespace Api\Utils;

use Psr\Http\Message\ResponseInterface;

class Response
{
    public static function raw(ResponseInterface $response, mixed $data, int $code, array $headers = [], bool $cache = false, int $cacheTTL = 3600, array $cacheControlHeaders = []): ResponseInterface
    {
        $response->getBody()->write($data);

        if (empty($cacheControlHeaders)) {
            $cacheHeadersString = '';
        } else {
            $cacheHeadersString = implode(',', $cacheControlHeaders) . ',';
        }

        if (empty($headers)) {
            $headersString = '';
        } else {
            foreach ($headers as $key => $value) {
                $response = $response->withAddedHeader($key, $value);
            }
        }
        if ($cache) {
            return $response
                ->withAddedHeader('Cache-Control', $cacheHeadersString . 'max-age=' . $cacheTTL)
                ->withAddedHeader('ETag', md5($data))
                ->withAddedHeader('X-Powered-By', 'Kipchak by Mamluk')
                ->withStatus($code);
        }

        return $response
            ->withAddedHeader('X-Powered-By', 'Kipchak by Mamluk')
            ->withStatus($code);
    }
}