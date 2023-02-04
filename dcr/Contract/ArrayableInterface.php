<?php

namespace DcrSwoole\Contract;

/**
 * Interface ArrayableInterface
 * @package Raylin666\Contract
 */
interface ArrayableInterface
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array;
}
