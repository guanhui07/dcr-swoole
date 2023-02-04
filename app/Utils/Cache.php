<?php

declare(strict_types=1);

namespace App\Utils;

use YiTin\TinRedis;

class Cache
{
    public static function set(string $key, $value, $ttl = null)
    {
        return TinRedis::setex($key, $ttl, serialize($value));
    }

    public static function get(string $key, $default = null)
    {
        $val = TinRedis::get($key);
        if ($val) {
            return unserialize($val);
        }
        return $default;
    }

    public static function delete($key)
    {
        return TinRedis::del($key);
    }
}
