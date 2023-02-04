<?php

namespace DcrSwoole\Contract;

/**
 * Interface ConfigInterface
 * @package Raylin666\Contract
 */
interface ConfigInterface
{
    /**
     * @param string $key
     * @param null   $default
     * @return mixed
     */
    public function get(string $key, $default = null);

    /**
     * @param string $key
     * @return mixed
     */
    public function has(string $key);

    /**
     * @param string $key
     * @param        $value
     * @return mixed
     */
    public function set(string $key, $value);
}
