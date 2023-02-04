<?php

declare(strict_types=1);

namespace DcrSwoole\Engine;

use Swoole\Coroutine\Http\Server as HttpServer;
use Swoole\Coroutine\Server;

class Constant
{
    public const ENGINE = 'Swoole';

    public static function isCoroutineServer($server): bool
    {
        return $server instanceof Server || $server instanceof HttpServer;
    }
}
