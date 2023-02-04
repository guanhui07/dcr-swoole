<?php

namespace DcrSwoole\Contract;

/**
 * Interface ConnectionPoolInterface
 * @package Raylin666\Contract
 */
interface ConnectionPoolInterface
{
    /**
     * 从连接池中获取真正的连接对象
     */
    public function getConnection();

    /**
     * 实现连接
     * @return mixed
     */
    public function connect();

    /**
     * 重新连接
     */
    public function reconnect();

    /**
     * 检查连接是否有效
     */
    public function check(): bool;

    /**
     * 关闭连接
     */
    public function close(): bool;

    /**
     * 重新发布到连接池
     */
    public function release(): void;
}
