<?php

declare(strict_types=1);

namespace DcrSwoole\Cache;

use DcrRedis\Redis;

class Cache
{
    /**
     * @var Cache
     */
    public static $cache;

    public static function instance(): Cache
    {
        if (!self::$cache) {
            $cache = new self();
            self::$cache = $cache;
            return self::$cache;
        }

        return self::$cache;
    }
    public function set(string $key, $value, $ttl = null): bool
    {
        return Redis::setex($key, $ttl, serialize($value));
    }

    public function get(string $key, $default = null)
    {
        $val = Redis::get($key);
        if ($val) {
            return unserialize($val);
        }
        return $default;
    }

    public function del($key): int
    {
        return Redis::del($key);
    }
}
