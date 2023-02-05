<?php

declare(strict_types=1);

namespace DcrSwoole\Event;

use Doctrine\Common\EventManager;
use Exception;

/**
 * 事件 监听  底层观察者模式
 * @see  https://www.doctrine-project.org/projects/doctrine-event-manager/en/latest/reference/index.html#setup
 */
class EventInstance
{
    /**
     * @var EventManager
     */
    public static $eventManager;

    /**
     * @throws Exception
     */
    public static function instance(): EventManager
    {
        if (!self::$eventManager) {
            $ins = new EventManager();
            self::$eventManager = $ins;
            return self::$eventManager;
        }

        return self::$eventManager;
    }
}
