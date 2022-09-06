<?php
namespace Api\Utils;

/**
 * Class Request
 * @package Quran\Helper
 */

class Request
{
    public static function editions($editions): array
    {
        $e = [];
        $parts = explode(',', $editions);
        if (count($parts) > 0) {
            foreach ($parts as $part) {
                if (trim($part) != '') {
                    $e[] = trim($part);
                }
            }

            return $e;
        } else {
            return $e;
        }
    }

    public static function isHttps()
    {
        return true; // Always force https - we can't really tell on a Swarm unless we get HA proxy to pass a parameter.
        // But https is safe for this.

        if ( isset($_SERVER) ) {
            if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
                return true;
            }
            if ( isset($_SERVER['HTTPS']) ) {
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
}
