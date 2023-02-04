<?php

namespace DcrSwoole\Contract;

/**
 * Interface PoolOptionInterface
 * @package Raylin666\Contract
 */
interface PoolOptionInterface
{
    /**
     * 获取连接池最大连接数
     * @return int
     */
    public function getMaxConnections(): int;

    /**
     * 获取连接池最小连接数
     * @return int
     */
    public function getMinConnections(): int;

    /**
     * 获取连接超时时间
     * @return float
     */
    public function getConnectTimeout(): float;

    /**
     * 获取连接池的等待/超时时间
     * @return float
     */
    public function getWaitTimeout(): float;

    /**
     * 心跳检测
     * @return float
     */
    public function getHeartbeat(): float;

    /**
     * 获取连接的最大空闲时间
     * @return float
     */
    public function getMaxIdleTime(): float;
}
