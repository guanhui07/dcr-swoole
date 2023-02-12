
### event 事件


## 概念

事件模式是一种经过了充分测试的可靠机制，是一种非常适用于解耦的机制，分别存在以下 3 种角色：

- `事件(Event)` 是传递于应用代码与 `监听器(Listener)` 之间的通讯对象
- `监听器(Listener)` 是用于监听 `事件(Event)` 的发生的监听对象
- `事件调度器(EventDispatcher)` 是用于触发 `事件(Event)` 和管理 `监听器(Listener)` 与 `事件(Event)` 之间的关系的管理者对象

用通俗易懂的例子来说明就是，假设我们存在一个 `UserService::register()` 方法用于注册一个账号，在账号注册成功后我们可以通过事件调度器触发 `UserRegistered` 事件，由监听器监听该事件的发生，在触发时进行某些操作，比如发送用户注册成功短信，在业务发展的同时我们可能会希望在用户注册成功之后做更多的事情，比如发送用户注册成功的邮件等待，此时我们就可以通过再增加一个监听器监听 `UserRegistered` 事件即可，无需在 `UserService::register()` 方法内部增加与之无关的代码。

## 使用事件管理器

> 接下来我们会通过配置和注解两种方式介绍监听器，实际使用时，二者只需使用其一即可，如果既有注解又有配置，则会造成监听器被多次触发。

### 定义一个事件

一个事件其实就是一个用于管理状态数据的普通类，触发时将应用数据传递到事件里，然后监听器对事件类进行操作，一个事件可被多个监听器监听。


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
