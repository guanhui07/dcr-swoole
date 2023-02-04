<?php

namespace DcrSwoole\Contract;

use Psr\Container\ContainerInterface;

/**
 * Interface ServiceProviderInterface
 * @package Raylin666\Contract
 */
interface ServiceProviderInterface
{
    /**
     * @param ContainerInterface $container
     * @return mixed
     */
    public function register(ContainerInterface $container);
}
