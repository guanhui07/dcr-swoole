<?php

declare(strict_types=1);

namespace DcrSwoole\Server;

use DcrSwoole\Singleton;
use DcrSwoole\Utils\ApplicationContext;
use Raylin666\Guzzle\Client;
use Raylin666\Pool\PoolOption;
use Swoole\Database\RedisConfig;
use DcrRedis\Redis;

class WorkerStart
{
    use Singleton;

    public function workerStart($server, $workerId): void
    {
        $container = ApplicationContext::getContainer();
        $config = config('db', []);
        if (!empty($config)) {
//            PDO::getInstance($config);
        }

        $config = config('redis', []);
        if (!empty($config)) {
            Redis::initialize(
                (new RedisConfig())
                ->withHost($config['host'])
                ->withPort($config['port'])
                ->withAuth($config['auth'])
                ->withDbIndex(1)
                ->withTimeout(1),
                $config['size'],// pool
            );
        }

        $client = new Client();
        $client->withPoolOption(
            (new PoolOption())->withMinConnections(1)
                ->withMaxConnections(10)
                ->withWaitTimeout(10)
        );
        $container->make(\GuzzleHttp\Client::class, [function () use ($client) {
            return $client->create();
        }]);
    }
}
