<?php

declare(strict_types=1);

namespace App\Provider;

use App\Event\TestEvent;
use App\Listener\TestEventListener;

/**
 * 事件
 * @see  https://www.doctrine-project.org/projects/doctrine-event-manager/en/latest/reference/index.html#setup
 */
class EventServiceProvider
{
    /**
     * @return string[]
     */
    public static function getEventConfig()
    {
        return [
            // 事件 绑定 监听
            TestEvent::class => TestEventListener::class,

        ];
    }
}
