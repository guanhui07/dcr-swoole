<?php

namespace DcrSwoole\Contract;

use Closure;
use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * Interface ContainerInterface
 * @package Raylin666\Contract
 */
interface ContainerInterface extends PsrContainerInterface
{
    /**
     * 绑定到容器
     * @param string $abstract
     * @param Closure|string|null $concrete
     * @param bool $singleton
     * @return mixed
     */
    public function bind($abstract, $concrete = null, $singleton = false);

    /**
     * 类静态(将已实例的类保存到容器)
     * @param string $abstract
     * @param Closure|string|null $concrete
     * @return mixed
     */
    public function singleton($abstract, $concrete = null);

    /**
     * 实例化/解析容器内容
     * @param string|callable $abstract
     * @param array $parameters
     * @return mixed
     */
    public function make($abstract, array $parameters = []);
}
