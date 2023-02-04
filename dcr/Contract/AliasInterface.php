<?php

namespace DcrSwoole\Contract;

/**
 * Interface AliasInterface
 * @package Raylin666\Contract
 */
interface AliasInterface
{
    /**
     * 获取别名访问器名称
     * @return string
     */
    public function getAliasAccessor(): string;
}
