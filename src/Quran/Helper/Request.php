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

}
