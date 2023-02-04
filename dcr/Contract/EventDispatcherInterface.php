<?php

namespace DcrSwoole\Contract;

use Psr\EventDispatcher\EventDispatcherInterface as PsrEventDispatcherInterface;

/**
 * Interface EventDispatcherInterface
 * @package Raylin666\Contract
 */
interface EventDispatcherInterface extends PsrEventDispatcherInterface
{
    /**
     * @param ListenerProviderInterface $listenerProvider
     */
    public function __invoke(ListenerProviderInterface $listenerProvider);

    /**
     * @return ListenerProviderInterface
     */
    public function getListenerProvider(): ListenerProviderInterface;
}
