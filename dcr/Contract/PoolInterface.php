<?php

namespace DcrSwoole\Contract;

/**
 * Interface PoolInterface
 * @package Raylin666\Contract
 */
interface PoolInterface
{
    /**
     * 从连接池获取连接
     * @return ConnectionPoolInterface
     */
    public function get(): ConnectionPoolInterface;

    /**
     * 释放回连接池的连接(发布连接)
     * @param ConnectionPoolInterface $connectionPool
     */
    public function release(ConnectionPoolInterface $connectionPool): void;

    /**
     * 获取连接池配置
     * @return PoolOptionInterface
     */
    public function getOption(): PoolOptionInterface;

    /**
     * 关闭并清除连接池
     */
    public function flush(): void;
}
