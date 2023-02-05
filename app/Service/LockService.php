<?php

declare(strict_types=1);

namespace App\Service;

use Exception;
use RuntimeException;
use DcrRedis\Redis;

/**
 * Class LockService
 * @package app\Service
 */
class LockService
{
    public function run(string $key, callable $callback, int $ttl = 3): void
    {
        $flag = $this->lock($key, $ttl);
        if (!$flag) {
            throw new RuntimeException("未获取到锁");
        }
        try {
            $callback();
        } catch (Exception $e) {
            throw $e;
        } finally {
            $this->unlock($key);
        }
    }

    /**
     * @param string $key
     * @param int $expire
     *
     * @return bool
     */
    public function lock(string $key, int $expire = 3): bool
    {
        return Redis::set($key, 1, ["NX", "EX" => $expire]);
    }

    /**
     * @param string $key
     *
     * @return int
     */
    public function unlock(string $key): int
    {
        return Redis::del($key);
    }
}
