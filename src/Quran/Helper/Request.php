<?php
namespace Quran\Helper;

/**
 * Class Request
 * @package Quran\Helper
 */

class Request
{
    public static function editions($editions)
    {
        $e = [];
        $parts = explode(',', $editions);
        if (count($parts > 0)) {
            foreach ($parts as $part) {
                if (trim($part) != '') {
                    $e[] = trim($part);
                }
            }

            return $e;
        } else {
            return false;
        }
    }

    public static function isHttps()
    {
        if ( isset($_SERVER) && isset($_SERVER['HTTPS']) ) {
            if ( 'on' == strtolower($_SERVER['HTTPS']) ) {
                return true;
            }
            if ( '1' == $_SERVER['HTTPS'] ) {
                return true;
            }
        } elseif ( isset($_SERVER['SERVER_PORT']) && ( '443' == $_SERVER['SERVER_PORT'] ) ) {
            return true;
        }

        return false;
    }

}
