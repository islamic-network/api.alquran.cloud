<?php
namespace Quran\Helper;

/**
 * Class Log
 * @package Quran\Helper
 */

class Log
{
    public static function format($server, $request)
    {
        $l = [];
        // Request Params
        $l['request'] = $request;
        $l['server'] = [
            'ip' => $server['REMOTE_ADDR'],
            'url' => isset($server['SCRIPT_URL']) ? $server['SCRIPT_URL'] : $server['REDIRECT_URL'],
            'method' => $server['REQUEST_METHOD']
        ];
        if (isset($server['HTTP_USER_AGENT'])) {
            $l['server']['useragent'] = $server['HTTP_USER_AGENT'];
        }
        if (isset($server['HTTP_ORIGIN'])) {
            $l['server']['origin'] = $server['HTTP_ORIGIN'];
        }
        if (isset($server['HTTP_REFERER'])) {
            $l['server']['referer'] = $server['HTTP_REFERER'];
        }

        return $l;
    }
}
 
 
