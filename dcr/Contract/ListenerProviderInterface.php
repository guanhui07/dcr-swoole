<?php

namespace DcrSwoole\Contract;

use Psr\EventDispatcher\ListenerProviderInterface as PsrListenerProviderInterface;

/**
 * Interface ListenerProviderInterface
 * @package Raylin666\Contract
 */
interface ListenerProviderInterface extends PsrListenerProviderInterface
{
    /**
     * @param string $event
     * @param        $listener
     * @param int    $priority
     * @return mixed
     */
    public function addListener(string $event, $listener, int $priority = 1);

    /**
     * @param SubscriberInterface $subscriber
     * @return mixed
     */
    public function addSubscriber(SubscriberInterface $subscriber);
}
