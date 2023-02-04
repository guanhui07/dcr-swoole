<?php

namespace DcrSwoole\Guzzle\Pool;

use DcrSwoole\Contract\ConnectionPoolInterface;
use DcrSwoole\Guzzle\Contract\GuzzlePoolInterface;
use DcrSwoole\Pool\Pool;

/**
 * Class GuzzlePool
 * @package Raylin666\Database\Pool
 */
class GuzzlePool extends Pool implements GuzzlePoolInterface
{
    /**
     * 创建连接
     * @return ConnectionPoolInterface
     */
    protected function createConnection(): ConnectionPoolInterface
    {
        // TODO: Implement createConnection() method.

        return make(
            Connection::class,
            [
                'pool'      =>   $this,
                'callback'  =>   $this->getConnectionCallback()
            ]
        );
    }
}
