## 中间件

本框架使用 `middlewares/utils` composer包实现 中间件,管道模式，洋葱模型

 

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


## 中间件定义
```php
<?php
namespace App\Middleware;
use App\Middleware\Contract\MiddlewareInterface;
use DcrSwoole\Request\Request;
class TestMiddleware implements MiddlewareInterface
{
    public function handle()
    {
        return static function ($request, $next) {
            $data = di()->get(Request::class)->get();
//            throw new RuntimeException('test middlere error');
            return $next->handle($request);
        };
    }
}

```
