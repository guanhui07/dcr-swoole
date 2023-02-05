<?php

declare(strict_types=1);

namespace App\Utils;

use DcrRedis\Redis;

class Cache
{
    public static function set(string $key, $value, $ttl = null)
    {
        return Redis::setex($key, $ttl, serialize($value));
    }

    public static function get(string $key, $default = null)
    {
        $val = Redis::get($key);
        if ($val) {
            return unserialize($val);
        }
        return $default;
    }

    public static function delete($key)
    {
        return Redis::del($key);
    }
}
