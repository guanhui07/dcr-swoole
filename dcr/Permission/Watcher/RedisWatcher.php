<?php
/**
 * @desc RedisWatcher.php 描述信息
 */

declare(strict_types=1);

namespace DcrSwoole\Permission\Watcher;


use Casbin\Persist\Watcher;
use Closure;
use DcrRedis\Redis;

class RedisWatcher implements Watcher
{
    public Closure $callback;

    public $channel;

    /**
     * The config of Watcher.
     *
     * @param array $config
     * [
     *     'channel' => '/casbin',
     * ]
     */
    public function __construct(array $config)
    {

        $this->channel = $config['channel'] ?? '/casbin';

        Redis::subscribe([$this->channel], function ($channel, $message) {
            if ($this->callback) {
                call_user_func($this->callback);
            }
        });
    }

    /**
     * Sets the callback function that the watcher will call when the policy in DB has been changed by other instances.
     * A classic callback is loadPolicy() method of Enforcer class.
     *
     * @param Closure $func
     */
    public function setUpdateCallback(Closure $func): void
    {
        $this->callback = $func;
    }

    /**
     * Update calls the update callback of other instances to synchronize their policy.
     * It is usually called after changing the policy in DB, like savePolicy() method of Enforcer class,
     * addPolicy(), removePolicy(), etc.
     */
    public function update(): void
    {
        Redis::publish($this->channel, 'casbin rules updated');
    }


    public function close(): void
    {
        // TODO: Implement close() method.
    }
}