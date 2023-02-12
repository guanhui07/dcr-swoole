
### event 事件
```php
<?php

namespace App\Event;
use Symfony\Contracts\EventDispatcher\Event;

class TestEvent extends Event
{
    public const NAME = 'order.placed';
    //推荐使用对象 比如模型对象或dto object
    protected $params;
    public function __construct($params)
    {
        $this->params = $params;
    }
    public function getParams()
    {
        return $this->params;
    }
}


```
### listener 监听者
```php
<?php
namespace App\Listener;
use App\Event\TestEvent;
use App\Listener\Contract\BaseListenerInterface;
class TestEventListener implements BaseListenerInterface
{
    /**
     * @param TestEvent $event
     */
    public function process(object $event)
    {
        echo '打印参数'.PHP_EOL;
        var_dump($event->getParams());
    }
}


```

### 触发事件
```php
    #[RequestMapping(methods: "GET , POST", path:"/test/event")]
    public function event($request, $response): array
    {
        $params = [
            'test' => 23,
        ];
        event(new TestEvent($params),TestEvent::NAME);
        // 初始化事件分发器
        return [];
    }
```

或者
```php
    #[RequestMapping(methods: "GET , POST", path:"/test/event")]
    public function event($request, $response): array
    {
        $params = [
            'test' => 23,
        ];
        $dispatcher = EventInstance::instance();
        $dispatcher->dispatch(new TestEvent($params), TestEvent::NAME);
    }
```
