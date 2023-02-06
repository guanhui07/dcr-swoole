<?php
declare(strict_types = 1);

namespace App\Listener;

use App\Event\TestEvent;
use Symfony\Contracts\EventDispatcher\Event;

class TestEventListener implements BaseListenerInterface
{
    public function process(object $event)
    {
        echo '打印参数'.PHP_EOL;
        var_dump($event->getParams());
    }
}