<?php

declare(strict_types=1);

namespace DcrSwoole\Engine;

use RuntimeException;

class Channel extends \Swoole\Coroutine\Channel
{
    /**
     * @var bool
     */
    protected bool $closed = false;

    public function getCapacity()
    {
        return $this->capacity;
    }

    public function getLength(): int
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

    public function hasProducers(): void
    {
        throw new RuntimeException('Not supported.');
    }

    public function hasConsumers(): void
    {
        throw new RuntimeException('Not supported.');
    }

    public function isReadable(): void
    {
        throw new RuntimeException('Not supported.');
    }

    public function isWritable(): void
    {
        throw new RuntimeException('Not supported.');
    }

    public function isClosing(): bool
    {
        return $this->closed || $this->errCode === SWOOLE_CHANNEL_CLOSED;
    }

    public function isTimeout(): bool
    {
        return !$this->closed && $this->errCode === SWOOLE_CHANNEL_TIMEOUT;
    }
}
