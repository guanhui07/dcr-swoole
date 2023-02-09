<?php
declare(strict_types=1);


namespace DcrSwoole\RateLimit;
use DcrRedis\Redis;
use DcrSwoole\Cache\Cache;

/**
 * @purpose 限流处理
 */
class RateLimitHandler
{
    /**
     * Container for throttle counters.
     *
     * @var Cache
     */
    protected $redis;

    /**
     * The number of seconds until the next token is available.
     *
     * @var int
     */
    protected int $tokenTime = 0;

    /**
     * The prefix applied to all keys to
     * minimize potential conflicts.
     *
     * @var string
     */
    protected string $prefix = 'rateLimit:';


    protected string $numSuffix  = ":Lave";
    protected string $timeSuffix = ":Time";

    /**
     * Timestamp to use (during testing)
     *
     * @var int
     */
    protected int $testTime;

    public function __construct()
    {
        $this->redis = Cache::instance();
    }

    /**
     * @param string $key The name of the bucket
     * @return $this
     */
    public function remove(string $key): self
    {
        $tokenName = $this->prefix . $key;

        $this->redis->del($tokenName . $this->numSuffix);
        $this->redis->del($tokenName . $this->timeSuffix);

        return $this;
    }

    /**
     * Return the test time, defaulting to current.
     */
    private function time(): int
    {
        return $this->testTime ?? time();
    }

    /**
     * Used during testing to set the current timestamp to use.
     *
     * @param int $time
     * @return $this
     */
    public function setTestTime(int $time): self
    {
        $this->testTime = $time;

        return $this;
    }

    /**
     * Restricts the number of requests made by a single IP address within
     * a set number of seconds.
     *
     * Example:
     *
     *  if (! $throttler->check($request->getRemoteIp(), 60, MINUTE)) {
     *      die('You submitted over 60 requests within a minute.');
     *  }
     *
     * @param string $key The name to use as the "bucket" name.
     * @param int $capacity The number of requests the "bucket" can hold
     * @param int $seconds The time it takes the "bucket" to completely refill
     * @param int $cost The number of tokens this action uses.
     *
     * @internal param int $maxRequests
     */
    public function handle(string $key, int $capacity, int $seconds, int $cost): bool
    {
        $lave = $this->prefix . $key . $this->numSuffix;
        $time = $this->prefix . $key . $this->timeSuffix;

        $rate    = $capacity / $seconds;
        $refresh = 1 / $rate;

        if (($tokens = $this->redis->get($lave)) === null) {
            $tokens = $capacity - $cost;
            $this->redis->set($lave, $tokens, $seconds);
            $this->redis->set($time, $this->time(), $seconds);
            $this->tokenTime = 0;
            return true;
        }

        // based on how long it's been since the last update.
        $throttleTime = $this->redis->get($time);
        $elapsed      = $this->time() - $throttleTime;

        // to be sure the bucket didn't overflow.
        $tokens += $rate * $elapsed;
        $tokens = min($tokens, $capacity);

        // If $tokens >= 1, then we are safe to perform the action, but
        // we need to decrement the number of available tokens.
        if ($tokens >= 1) {
            $tokens -= $cost;
            $this->redis->set($lave, $tokens, $seconds);
            $this->redis->set($time, $this->time(), $seconds);

            $this->tokenTime = 0;

            return true;
        }
        $newTokenAvailable = (int)($refresh - $elapsed - $refresh * $tokens);
        $this->tokenTime   = max(1, $newTokenAvailable);

        return false;
    }

}