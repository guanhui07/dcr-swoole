<?php

declare(strict_types=1);

namespace DcrSwoole\Engine;

use RuntimeException;

class Channel extends \Swoole\Coroutine\Channel
{
    /**
     * @var bool
     */
    protected $closed = false;

    public function getCapacity()
    {
        return $this->capacity;
    }

    public function getLength()
    {
        return $this->length();
    }

    public function isAvailable()
    {
        return !$this->isClosing();
    }

    public function close(): bool
    {
        $this->closed = true;
        parent::close();
        return true;
    }

    public function hasProducers()
    {
        throw new RuntimeException('Not supported.');
    }

    public function hasConsumers()
    {
        throw new RuntimeException('Not supported.');
    }

    public function isReadable()
    {
        throw new RuntimeException('Not supported.');
    }

    public function isWritable()
    {
        throw new RuntimeException('Not supported.');
    }

    public function isClosing()
    {
        return $this->closed || $this->errCode === SWOOLE_CHANNEL_CLOSED;
    }

    public function isTimeout()
    {
        return !$this->closed && $this->errCode === SWOOLE_CHANNEL_TIMEOUT;
    }
}
