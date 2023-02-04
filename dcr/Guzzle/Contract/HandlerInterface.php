<?php

namespace  DcrSwoole\Guzzle\Contract;

use DcrSwoole\Contract\PoolOptionInterface;

/**
 * Interface HandlerInterface
 * @package Raylin666\Guzzle\Contract
 */
interface HandlerInterface
{
    /**
     * 设置连接池
     * @param bool  $isOpen
     * @param PoolOptionInterface|null $poolOption
     * @return $this
     */
    public function withPool(bool $isOpen, ?PoolOptionInterface $poolOption = null): self;

    /**
     * 是否已开启连接池
     * @return bool
     */
    public function isOpenPool(): bool;

    /**
     * 获取连接池配置
     * @return PoolOptionInterface|null
     */
    public function getPoolOption(): ?PoolOptionInterface;
}
