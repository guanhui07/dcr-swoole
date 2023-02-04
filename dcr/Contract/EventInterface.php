<?php

namespace DcrSwoole\Contract;

/**
 * Interface EventInterface
 * @package Raylin666\Contract
 */
interface EventInterface
{
    /**
     * 获取事件访问器名称
     * @return string
     */
    public function getEventAccessor(): string;
}
