<?php

namespace  DcrSwoole\Guzzle\Contract;

use DcrSwoole\Contract\PoolInterface;

/**
 * Interface GuzzlePoolInterface
 * @package Raylin666\Guzzle\Contract
 */
interface GuzzlePoolInterface extends PoolInterface
{
    /**
     * @return string
     */
    public function getName(): string;
}
