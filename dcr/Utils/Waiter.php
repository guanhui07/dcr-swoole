<?php

declare(strict_types=1);

namespace DcrSwoole\Utils;

use Closure;
use DcrSwoole\Engine\Channel;
use RuntimeException;
use Throwable;

class Waiter
{
    /**
     * @var float
     */
    protected $pushTimeout = 10.0;

    /**
     * @var float
     */
    protected $popTimeout = 10.0;

    public function __construct(float $timeout = 10.0)
    {
        $this->popTimeout = $timeout;
    }

    /**
     * @param null|float $timeout seconds
     */
    public function wait(Closure $closure, ?float $timeout = null)
    {
        if ($timeout === null) {
            $timeout = $this->popTimeout;
        }

        $channel = new Channel(1);
        Coroutine::create(function () use ($channel, $closure) {
            try {
                $result = $closure();
            } catch (Throwable $exception) {
                $result = new RuntimeException($exception);
            } finally {
                $channel->push($result ?? null, $this->pushTimeout);
            }
        });

        $result = $channel->pop($timeout);
        if ($result === false && $channel->isTimeout()) {
            throw new RuntimeException(sprintf('Channel wait failed, reason: Timed out for %s s', $timeout));
        }
        if ($result instanceof RuntimeException) {
            throw $result->getThrowable();
        }

        return $result;
    }
}
