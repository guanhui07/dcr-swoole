<?php

namespace  DcrSwoole\Pool;

use DcrSwoole\Contract\PoolOptionInterface;
use PhpOption\Option;

/**
 * Class PoolOption
 * @package Raylin666\Pool
 */
class PoolOption implements PoolOptionInterface
{
    /**
     * Min connections of pool.
     * This means the pool will create $minConnections connections when
     * pool initialization.
     *
     * @var int
     */
    private $minConnections = 1;

    /**
     * Max connections of pool.
     *
     * @var int
     */
    private $maxConnections = 10;

    /**
     * The timeout of connect the connection.
     * Default value is 10 seconds.
     *
     * @var float
     */
    private $connectTimeout = 10.0;

    /**
     * The timeout of pop a connection.
     * Default value is 3 seconds.
     *
     * @var float
     */
    private $waitTimeout = 3.0;

    /**
     * Heartbeat of connection.
     * If the value is 10, then means 10 seconds.
     * If the value is -1, then means does not need the heartbeat.
     * Default value is -1.
     *
     * @var float
     */
    private $heartbeat = -1;

    /**
     * The max idle time for connection.
     * @var float
     */
    private $maxIdleTime = 60.0;

    /**
     * @return int
     */
    public function getMaxConnections(): int
    {
        // TODO: Implement getMaxConnections() method.

        return $this->maxConnections;
    }

    /**
     * @param int $maxConnections
     * @return Option
     */
    public function withMaxConnections(int $maxConnections): self
    {
        $this->maxConnections = $maxConnections;
        return $this;
    }

    /**
     * @return int
     */
    public function getMinConnections(): int
    {
        // TODO: Implement getMinConnections() method.

        return $this->minConnections;
    }

    /**
     * @param int $minConnections
     * @return Option
     */
    public function withMinConnections(int $minConnections): self
    {
        $this->minConnections = $minConnections;
        return $this;
    }

    /**
     * @return float
     */
    public function getConnectTimeout(): float
    {
        // TODO: Implement getConnectTimeout() method.

        return $this->connectTimeout;
    }

    /**
     * @param float $connectTimeout
     * @return Option
     */
    public function withConnectTimeout(float $connectTimeout): self
    {
        $this->connectTimeout = $connectTimeout;
        return $this;
    }

    /**
     * @return float
     */
    public function getHeartbeat(): float
    {
        // TODO: Implement getHeartbeat() method.

        return $this->heartbeat;
    }

    /**
     * @param float $heartbeat
     * @return Option
     */
    public function withHeartbeat(float $heartbeat): self
    {
        $this->heartbeat = $heartbeat;
        return $this;
    }

    /**
     * @return float
     */
    public function getWaitTimeout(): float
    {
        // TODO: Implement getWaitTimeout() method.

        return $this->waitTimeout;
    }

    /**
     * @param float $waitTimeout
     * @return Option
     */
    public function withWaitTimeout(float $waitTimeout): self
    {
        $this->waitTimeout = $waitTimeout;
        return $this;
    }

    /**
     * @return float
     */
    public function getMaxIdleTime(): float
    {
        // TODO: Implement getMaxIdleTime() method.

        return $this->maxIdleTime;
    }

    /**
     * @param float $maxIdleTime
     * @return Option
     */
    public function withMaxIdleTime(float $maxIdleTime): self
    {
        $this->maxIdleTime = $maxIdleTime;
        return $this;
    }
}
