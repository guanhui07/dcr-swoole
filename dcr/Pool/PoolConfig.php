<?php

namespace  DcrSwoole\Pool;

use DcrSwoole\Contract\PoolOptionInterface;

/**
 * Class PoolConfig
 * @package Raylin666\Pool
 */
class PoolConfig
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var callable
     */
    protected $connectionCallback;

    /**
     * @var PoolOptionInterface|null
     */
    protected $poolOption;

    /**
     * PoolConfig constructor.
     * @param string                     $name
     * @param callable                   $connectionCallback
     * @param PoolOptionInterface|null   $poolOption
     */
    public function __construct(string $name, callable $connectionCallback, ?PoolOptionInterface $poolOption = null)
    {
        $this->name = $name;
        $this->poolOption = $poolOption;
        $this->connectionCallback = $connectionCallback;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return callable
     */
    public function getConnectionCallback(): callable
    {
        return $this->connectionCallback;
    }

    /**
     * @return PoolOptionInterface|null
     */
    public function getPoolOption(): ?PoolOptionInterface
    {
        return $this->poolOption;
    }
}
