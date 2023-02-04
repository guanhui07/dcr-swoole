<?php

namespace DcrSwoole\Utils;

use DcrSwoole\Engine\Channel;
use SplQueue;

/**
 * Class Queue
 */
class Queue
{
    /**
     * 总容量(总数量)
     * @var int
     */
    protected $capacity = 1;

    /**
     * 通道对象
     * @var Channel
     */
    protected $channel;

    /**
     * 队列
     * @var SplQueue
     */
    protected $queue;

    /**
     * Queue constructor.
     * @param int $capacity
     */
    public function __construct(int $capacity = 1)
    {
        $this->capacity = $capacity;
        $this->queue = new SplQueue();
        $this->channel = new Channel($capacity);
    }

    /**
     * @param float $timeout
     * @return mixed|void
     */
    public function pop(float $timeout)
    {
        if ($this->isCoroutine()) {
            return $this->channel->pop($timeout);
        }

        return $this->queue->shift();
    }

    /**
     * @param $data
     * @return bool
     */
    public function push($data)
    {
        if ($this->isCoroutine()) {
            return $this->channel->push($data);
        }

        $this->queue->push($data);
        return true;
    }

    /**
     * @return int
     */
    public function length(): int
    {
        if ($this->isCoroutine()) {
            return $this->channel->length();
        }

        return $this->queue->count();
    }

    /**
     * @return bool
     */
    protected function isCoroutine(): bool
    {
        return Coroutine::id() > 0;
    }
}
