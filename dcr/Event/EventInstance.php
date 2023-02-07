<?php

declare(strict_types=1);

namespace DcrSwoole\Event;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Exception;

/**
 * 事件 监听  底层观察者模式
 * @see https://code.tutsplus.com/tutorials/handling-events-in-your-php-applications-using-the-symfony-eventdispatcher-component--cms-31328
 */
class EventInstance
{

    public static $eventManager;

    /**
     * @throws Exception
     */
    public static function instance(): EventDispatcher
    {
        if (!self::$eventManager) {
            $ins = new EventDispatcher;
            self::$eventManager = $ins;
            return self::$eventManager;
        }

        return self::$eventManager;
    }
}
