<?php

declare(strict_types=1);

namespace DcrSwoole\Utils;

use Swoole\Coroutine\Channel;

class ChannelPool extends \SplQueue
{
    /**
     * @var ChannelPool
     */
    private static $instance;

    public static function getInstance(): self
    {
        return static::$instance ?? (static::$instance = new self());
    }

    public function get(): Channel
    {
        return $this->isEmpty() ? new Channel(1) : $this->pop();
    }

    public function release(Channel $channel)
    {
        $channel->errCode = 0;
        $this->push($channel);
    }
}
