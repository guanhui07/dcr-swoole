<?php
namespace App\Listener;
use App\Event\TestEvent;
use App\Listener\Contract\BaseListenerInterface;

/**
 * Class TestEventListener
 * @package App\Listener
 * @see https://code.tutsplus.com/tutorials/handling-events-in-your-php-applications-using-the-symfony-eventdispatcher-component--cms-31328
 */
class TestEventListener implements BaseListenerInterface
{
    /**
     * @param TestEvent $event
     */
    public function process(object $event)
    {
        echo '打印参数'.PHP_EOL;
        var_dump($event->getParams());
        // do sth...
    }
}