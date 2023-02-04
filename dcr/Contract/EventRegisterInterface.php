<?php

namespace DcrSwoole\Contract;

/**
 * Interface EventRegisterInterface
 * @package Raylin666\Contract
 */
interface EventRegisterInterface
{
    /**
     * EventRegisterInterface constructor.
     * @param string $event
     * @param        $listener
     * @param int    $priority
     */
    public function __construct(string $event, $listener, int $priority);

    /**
     * @return string
     */
    public function getEvent(): string;

    /**
     * @return mixed
     */
    public function getListener();

    /**
     * @return int
     */
    public function getPriority(): int;
}
