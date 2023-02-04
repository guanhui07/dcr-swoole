<?php

namespace DcrSwoole\Contract;

use Closure;

/**
 * Interface SubscriberInterface
 * @package Raylin666\Contract
 */
interface SubscriberInterface
{
    /**
     * Register the subscriber for each event.
     * @param Closure $subscriber   [eventId => [mathod, priority]][]
     * @return mixed
     */
    public function subscribe(Closure $subscriber);
}
