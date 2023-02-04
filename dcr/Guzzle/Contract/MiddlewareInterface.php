<?php

namespace  DcrSwoole\Guzzle\Contract;

/**
 * Interface MiddlewareInterface
 * @package Raylin666\Guzzle\Contract
 */
interface MiddlewareInterface
{
    /**
     * @return callable
     */
    public function getMiddleware(): callable;
}
