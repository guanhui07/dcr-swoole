<?php

declare(strict_types=1);

namespace App\Service;

use App\Utils\Json;
use DcrRedis\Redis;

/**
 * @package app\Service
 */
class RedisQueueService
{
    public static function push(string $key, $data = [])
    {
        if (is_array($data)) {
            $data = Json::encode($data);
        }
        return Redis::lPush($key, $data);
    }

    public static function pop(string $key, int $ttl = 10): array
    {
        $data = Redis::brPop($key, $ttl);
        if (empty($data)) {
            return [];
        }
        return Json::decode($data[1], true);
    }
}
