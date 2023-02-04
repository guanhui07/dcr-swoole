<?php

namespace DcrSwoole\Contract;

/**
 * Interface JsonableInterface
 * @package Raylin666\Contract
 */
interface JsonableInterface
{
    /**
     * Convert the object to its JSON representation.
     *
     * @param  int $options
     *
     * @return string
     */
    public function toJson(int $options = 0): string;
}
