<?php

namespace  DcrSwoole\Pool;

use DcrSwoole\Contract\ConnectionPoolInterface;

/**
 * Class SimplePool
 * @package Raylin666\Pool
 */
class SimplePool extends Pool
{
    /**
     * @return ConnectionPoolInterface
     */
    protected function createConnection(): ConnectionPoolInterface
    {
        // TODO: Implement createConnection() method.

        return make(
            SimpleConnection::class,
            [
                'pool' => $this,
                'callback' => $this->getConnectionCallback(),
            ]
        );
    }
}
