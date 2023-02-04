<?php

namespace  DcrSwoole\Pool;

use DcrSwoole\Contract\ConnectionPoolInterface;
use DcrSwoole\Contract\PoolInterface;

/**
 * Class Connection
 * @package Raylin666\Pool
 */
abstract class Connection implements ConnectionPoolInterface
{
    /**
     * 连接池
     * @var PoolInterface
     */
    protected $pool;

    /**
     * 闭包回调具体连接对象的实现
     * @var callable
     */
    protected $callback;

    /**
     * 具体连接对象
     * @var
     */
    protected $connection;

    /**
     * 连接创建时间
     * @var float
     */
    protected $lastUseTime = 0.0;

    /**
     * Connection constructor.
     * @param PoolInterface $pool
     * @param callable     $callback    返回连接对象，例如: return new PDO(...);
     */
    public function __construct(PoolInterface $pool, callable $callback)
    {
        $this->pool = $pool;
        $this->callback = $callback;
    }

    /**
     * @return mixed
     */
    public function connect()
    {
        $this->connection = ($this->callback)();
        return $this->connection;
    }

    /**
     * @return mixed
     */
    public function reconnect()
    {
        // TODO: Implement reconnect() method.

        if (empty($this->connection) || (! $this->check())) {
            $this->close();
            $this->lastUseTime = microtime(true);
            $this->connect();
        }

        return $this->connection;
    }

    /**
     * 发布连接
     * release connection
     */
    public function release(): void
    {
        $this->pool->release($this);
    }

    /**
     * 检查空闲连接,更新最新连接时间
     * @return bool
     */
    public function check(): bool
    {
        $maxIdleTime = $this->pool->getOption()->getMaxIdleTime();
        $now = microtime(true);
        if ($now > $maxIdleTime + $this->lastUseTime) {
            return false;
        }

        $this->lastUseTime = $now;
        return true;
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        // TODO: Implement close() method.

        $this->connection = null;
        return true;
    }

    /**
     * 获取连接
     * @return mixed
     */
    public function getConnection()
    {
        return $this->getActiveConnection();
    }

    /**
     * 连接并返回活跃连接
     * @return mixed
     */
    abstract protected function getActiveConnection();
}
