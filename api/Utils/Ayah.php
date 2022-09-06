<?php

namespace Api\Utils;

use Api\Models\AyatResponse;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Mamluk\Kipchak\Components\Http;

class Ayah
{
    /**
     * @param ServerRequestInterface $request
     * @param mixed $number
     * @param ResponseInterface $response
     * @return ResponseInterface
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public static function getMultipleEditions(ServerRequestInterface $request, mixed $number, ResponseInterface $response, MemcachedAdapter $mc, EntityManager $em): ResponseInterface
    {
        $editions = Request::editions(Http\Request::getAttribute($request, 'editions'));

        if (!empty($editions)) {
            $result = $mc->get('ayah_' . $number . '_' . md5(json_encode($editions)), function (ItemInterface $item) use ($number, $editions, $em) {
                $item->expiresAfter(604800);
                $ayats = [];
                foreach ($editions as $edition) {
                    $ayat = new AyatResponse($em, $number, $edition);
                    $ayats[] = $ayat->get();
                }

                return [
                    $ayats,
                    $ayat->getCode()
                ];
            });
        }

        return Http\Response::json($response,
            $result[0],
            $result[1],
            true,
            86400
        );
    }
}