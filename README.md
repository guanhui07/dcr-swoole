# 整合各种包，基于Swoole 实现的框架

- 集成 laravel orm , restful route, redis, guzzle monolog
- http websocket
- rabbitmq
- container
- event
- middleware  中间件注解
- validate
- monolog
- collection
- carbon
- dotenv
- 支持路由注解 中间件注解
- aop 切面

### 安装
```
composer create-project dcrswoole/framework skeleton
```

### 分层 (demo未按此方式)
controller->service ->repository->model

### http:

```
php ./bin/start.php http:start 
```

### websocket:

```
php ./bin/start.php ws:start 
```

### console:

```
php artisan test
```

### crontab:

```
/config/crontab.php  enable 改为 true 开启
```

### migrate:

```
php migrate.php  migrations:generate
php migrate.php migrations:migrate

```

### container

```
ApplicationContext::getContainer()
或 di()


```

## 路由注解和中间件注解
```php
    #[RequestMapping(methods: "GET , POST" , path:"/api/json")]
    #[Middlewares(AuthMiddleware::class , TestMiddleware::class)]
    public function test()
    {
        return 'hello';
    }
```


## 路由注解 和 中间件注解 以及Inject注解  使用
```php
<?php
declare(strict_types=1);
namespace App\Controller;

use App\Middleware\AuthMiddleware;
use App\Middleware\TestMiddleware;
use App\Service\TestService;
use DcrSwoole\Annotation\Mapping\Middlewares;
use DcrSwoole\Annotation\Mapping\RequestMapping;
use DI\Attribute\Inject;

class MiddlewareController extends Controller
{
    #[Inject]
    public TestService $testService;
    
    #[RequestMapping(methods: "GET , POST", path:"/test/middleware")]
    #[Middlewares(AuthMiddleware::class, TestMiddleware::class)]
    public function test()
    {
        return 'hello world';
    }
}
```

## 多个中间件注解
```php
use app\middleware\App;
use app\middleware\Log;
#[RequestMapping(methods: "GET , POST" , path:"/api/json") , Middlewares(App::class , Log::class)]
public function json(Request $request)
{
    return json(['code' => 0, 'msg' => 'ok']);
}
public function json(Request $request)
{
    return json(['code' => 0, 'msg' => 'ok']);
}
```


## 从容器 拿对象 获取 参数
```php
//->all()  ->get()  ->post() 等方法
ApplicationContext::getContainer()->get(Request::class)->all();
//di()->(Request::class)->all();
```

## redis 操作
```php
ApplicationContext::getContainer()->get(DataRedis::class);
//->setex ->get ->del ->setnx 等方法 和predis一致
```

## orm model ，使用和laravel orm一致
```php
<?php

declare(strict_types=1);

namespace App\Model;
use Guanhui07\SwooleDatabase\Adapter\Model;
class UserModel extends Model
{
    protected $table = 'user';
}

```

### 控制器validate
```php
   #[RequestMapping(methods: "GET , POST", path:"/test/test4")]
    public function test4($request, $response)
    {
        $validate = Validation::check($this->request->post ?? [], [
            // add rule
            ['title', 'min', 40],
            ['freeTime', 'number'],
        ]);

        if ($validate->isFail()) {
            var_dump($validate->getErrors());
            var_dump($validate->firstError());
        }

        // $postData = $v->all(); // 原始数据
        $safeData = $validate->getSafeData(); // 验证通过的安全数据

        return $safeData
    }
```

### 获取配置 需要`use DI\Attribute\Inject`
```php
    #[Inject]
    public Config $config;

    #[RequestMapping(methods: "GET , POST", path:"/test/config")]
    public function config()
    {
        //di()->get(Config::class)->get('app.debug');
        return $this->config->get('app.debug');
    }
```

## 中间件
```php
<?php
namespace App\Middleware;
use App\Middleware\Contract\MiddlewareInterface;
use DcrSwoole\Log\LogBase;
use DcrSwoole\Request\Request;
class TestMiddleware implements MiddlewareInterface
{
    public function handle()
    {
        return static function ($request, $next) {
            $data = Request::instance()->get;
//            throw new RuntimeException('test middlere error');
            return $next->handle($request);
        };
    }
}

```


### Console 命令应用 需要在`app/Console/Kernel.php` 配置 命令类
```php
<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Repository\TestRepository;
use DcrSwoole\Utils\ApplicationContext;
use Inhere\Console\IO\Input;
use Inhere\Console\IO\Output;
use Toolkit\PFlag\FlagsParser;

/**
 * Class Test2
 * @package app\Console\Command
 * php artisan test2
 */
class Test2 extends \Inhere\Console\Command
{
    protected static string $name = 'test2';

    protected static string $desc = 'print system ENV information';

    protected function configFlags(FlagsParser $fs): void
    {
        // 绑定选项
        $fs->addOptByRule('update, up', 'bool;update linux command docs to latest');
        $fs->addOptByRule('init, i', 'bool;update linux command docs to latest');
        $fs->addOptByRule('search, s', 'string;input keywords for search');

        // 绑定参数
        // - 这里没有设置必须 可以不传，获取到就是空string
        $fs->addArg('keywords', 'the keywords for search or show docs', 'string');
    }

    protected function execute(Input $input, Output $output)
    {
        $keywords = $this->flags->getOpt('search', 23);

        ApplicationContext::getContainer()->get(TestRepository::class)->fromRepos();
        $output->info("hello world ...");
    }
}


```


### crontab定时任务 需要在 `/config/crontab.php` 配置 定时任务
```php
<?php

declare(strict_types=1);

namespace App\Crontab;

use App\Crontab\Contract\CrontabInterface;
use App\Repository\TestRepository;
use DcrSwoole\Utils\ApplicationContext;

class TestCrontab implements CrontabInterface
{
    public function execute(): void
    {
        ApplicationContext::getContainer()->get(TestRepository::class)->test1();
    }
}

```

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

## 切面 aop
```php
<?php
namespace App\Aspect;

use App\Service\UserService;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;

class DebugAspect
{
    //要切入的方法 
    public array $classes = [
        UserService::class . '::first',
    ];

    /**
     * 测试切面
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        var_dump(11);
        try {
            return $proceedingJoinPoint->process();
        } catch (Exception $e) {
        }
    }
}

```

## 文档
[https://github.com/guanhui07/dcr-swoole/wiki](https://github.com/guanhui07/dcr-swoole/wiki)

### 更多例子查看 代码demo

### ab 本机macbook pro 压测 Requests per second:    58832.04 [#/sec] (mean)
```
→ ab -k  -n 100000  -c 30 'http://127.0.0.1:9501/test/request'
This is ApacheBench, Version 2.3 <$Revision: 1879490 $>
Copyright 1996 Adam Twiss, Zeus Technology Ltd, http://www.zeustech.net/
Licensed to The Apache Software Foundation, http://www.apache.org/

Benchmarking 127.0.0.1 (be patient)
Completed 10000 requests
Completed 20000 requests
Completed 30000 requests
Completed 40000 requests
Completed 50000 requests
Completed 60000 requests
Completed 70000 requests
Completed 80000 requests
Completed 90000 requests
Completed 100000 requests
Finished 100000 requests


Server Software:        swoole-http-server
Server Hostname:        127.0.0.1
Server Port:            9501

Document Path:          /test/request
Document Length:        4 bytes

Concurrency Level:      30
Time taken for tests:   1.700 seconds
Complete requests:      100000
Failed requests:        0
Keep-Alive requests:    100000
Total transferred:      15600000 bytes
HTML transferred:       400000 bytes
Requests per second:    58832.04 [#/sec] (mean)
Time per request:       0.510 [ms] (mean)
Time per request:       0.017 [ms] (mean, across all concurrent requests)
Transfer rate:          8962.69 [Kbytes/sec] received

Connection Times (ms)
              min  mean[+/-sd] median   max
Connect:        0    0   0.0      0       1
Processing:     0    1   0.3      0      15
Waiting:        0    0   0.3      0      15
Total:          0    1   0.3      0      15
ERROR: The median and mean for the processing time are more than twice the standard
       deviation apart. These results are NOT reliable.
ERROR: The median and mean for the total time are more than twice the standard
       deviation apart. These results are NOT reliable.

Percentage of the requests served within a certain time (ms)
  50%      0
  66%      1
  75%      1
  80%      1
  90%      1
  95%      1
  98%      1
  99%      2
 100%     15 (longest request)
```

## wrk 压测  Requests/sec:  79540.12
```
→ wrk -t20 -c300 -d 30s --latency   http://127.0.0.1:9501/test/request
Running 30s test @ http://127.0.0.1:9501/test/request
  20 threads and 300 connections
  Thread Stats   Avg      Stdev     Max   +/- Stdev
    Latency     7.56ms   14.05ms 154.35ms   88.96%
    Req/Sec     4.71k     1.25k   18.48k    82.37%
  Latency Distribution
     50%    1.43ms
     75%    6.30ms
     90%   23.63ms
     99%   68.04ms
  2394042 requests in 30.10s, 356.17MB read
  Socket errors: connect 67, read 86, write 0, timeout 0
Requests/sec:  79540.12
Transfer/sec:     11.83MB
```

### composer依赖组件

```
     "symfony/event-dispatcher": "^6.2",  事件监听 观察者模式
    "doctrine/migrations": "^3.5",  migrate
    "elasticsearch/elasticsearch": "7.16",  es
    "firebase/php-jwt": "^6.3",   jwt token 
    "gregwar/captcha": "^1.1",  captcha 
    "guanhui07/database": "^1.0",   laravel orm 改
    "guanhui07/dcr-swoole-crontab": "^1.0",  crontab
    "guanhui07/guzzle": "^1.0",   guzzle client 
    "guanhui07/redis": "^1.0",   redis pool
    "inhere/console": "^4.1",    console command 
    "inhere/php-validate": "^2.8",   validate 验证器
    "intervention/image": "^2.7",   image操作
    "middlewares/utils": "^3.0",    middleware中间件
    "monolog/monolog": "^2.8",     monolog  
    "mwangithegreat/faker": "^1.9",   faker造数据
    "nesbot/carbon": "^2.6",     carbon time
    "nikic/fast-route": "^1.3",   nikic的 resful route
    "opis/closure": "^3.6",      闭包序列化
    "php-amqplib/php-amqplib": "dev-master",   rabbitmq
    "php-di/php-di": "^7.0",   依赖注入 di container 
    "qiniu/php-sdk": "^7.7",  七牛cdn
    "spatie/image": "^2.2",   
    "symfony/finder": "^5.0",   symfony finder
    "vlucas/phpdotenv": "^5.4"  dotenv读取 
```

## 关联

参考 hyperf laravel webman 项目

https://github.com/guanhui07/dcr  fpm以及workerman实现websocket

https://github1s.com/walkor/webman-framework

https://github1s.com/hyperf/hyperf

https://github1s.com/laravel/laravel

https://github.com/SerendipitySwow/Serendipity-job

https://github.com/sunsgneayo/annotation 路由注解参考


### todo:
类似`hyperf`实现 Command Crontab AutoController Cacheable 等注解

