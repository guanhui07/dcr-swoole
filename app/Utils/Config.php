<?php
declare(strict_types = 1);
/**
 * The file is part of Dcr/framework
 *
 *
 */

namespace App\Utils;

use Illuminate\Support\Arr;

class Config
{
    public static function get($str = 'app')
    {
        // config('app.debug');
        if (str_contains($str, '.')) {
            $tmpArr = explode('.', $str, 2);

            $arr = require(PROJECT_ROOT.'config/'.$tmpArr[0].'.php');
            $end = end($tmpArr);
            $arr = Arr::get($arr, $end, '');
            return self::dealArr($arr);
        }

        // config('app')
        $arr = require(PROJECT_ROOT.'config/'.$str.'.php');
        $arr = self::dealArr($arr);

        return $arr;
    }

    /**
     * @param  mixed  $arr
     *
     * @return bool|mixed
     */
    protected static function dealArr(mixed $arr): mixed
    {
        if (!is_array($arr)) {
            switch (strtolower($arr)) {
                case 'true':
                case '(true)':
                    return true;
                case 'false':
                case '(false)':
                    return false;
                case 'empty':
                case '(empty)':
                    return '';
                case 'null':
                case '(null)':
                    return null;
                default:
                    return $arr;
            }
        }
        $arr = self::processConfigArr($arr);
        return $arr;
    }

    /**
     * @param  mixed  $arr
     *
     * @return bool
     */
    protected static function isTrue(mixed $arr): bool
    {
        return is_string($arr) && $arr === 'true';
    }

    /**
     * @param  mixed  $arr
     *
     * @return bool
     */
    protected static function isFalse(mixed $arr): bool
    {
        return is_string($arr) && $arr === 'false';
    }

    /**
     * @param  array  $arr
     *
     * @return bool|mixed
     */
    protected static function processConfigArr(array $arr)
    {
        return array_map(function ($value) {
            if (!is_string($value)) {
                return $value;
            }
            switch (strtolower($value)) {
                case 'true':
                case '(true)':
                    return true;
                case 'false':
                case '(false)':
                    return false;
                case 'empty':
                case '(empty)':
                    return '';
                case 'null':
                case '(null)':
                    return null;
                default:
                    return $value;
            }
        }, $arr);
    }
}
