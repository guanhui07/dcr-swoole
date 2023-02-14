## 中间件


中间件一般用于拦截请求或者响应。例如执行控制器前统一验证用户身份，如用户未登录时跳转到登录页面，例如响应中增加某个header头。例如统计某个uri请求占比等等。

## 中间件洋葱模型

```
                              
            ┌──────────────────────────────────────────────────────┐
            │                     middleware1                      │ 
            │     ┌──────────────────────────────────────────┐     │
            │     │               middleware2                │     │
            │     │     ┌──────────────────────────────┐     │     │
            │     │     │         middleware3          │     │     │        
            │     │     │     ┌──────────────────┐     │     │     │
            │     │     │     │                  │     │     │     │
 　── 请求 ───────────────────────> 控制器 ─ 响应 ───────────────────────────> 客户端
            │     │     │     │                  │     │     │     │
            │     │     │     └──────────────────┘     │     │     │
            │     │     │                              │     │     │
            │     │     └──────────────────────────────┘     │     │
            │     │                                          │     │
            │     └──────────────────────────────────────────┘     │
            │                                                      │
            └──────────────────────────────────────────────────────┘
```
中间件和控制器组成了一个经典的洋葱模型，中间件类似一层一层的洋葱表皮，控制器是洋葱芯。如果所示请求像箭一样穿越中间件1、2、3到达控制器，控制器返回了一个响应，然后响应又以3、2、1的顺序穿出中间件最终返回给客户端。也就是说在每个中间件里我们既可以拿到请求，也可以获得响应。

## 请求拦截
有时候我们不想某个请求到达控制器层，例如我们在某个身份验证中间件发现当前用户并没有登录，则我们可以直接拦截请求并返回一个登录响应。那么这个流程类似下面这样

```
                              
            ┌───────────────────────────────────────────────────────┐
            │                     middleware1                       │ 
            │     ┌───────────────────────────────────────────┐     │
            │     │          　 　 身份验证中间件                │     │
            │     │      ┌──────────────────────────────┐     │     │
            │     │      │         middleware3          │     │     │        
            │     │      │     ┌──────────────────┐     │     │     │
            │     │      │     │                  │     │     │     │
 　── 请求 ───────────┐   │     │       控制器      │     │     │     │
            │     │ 响应　│     │                  │     │     │     │
   <─────────────────┘   │     └──────────────────┘     │     │     │
            │     │      │                              │     │     │
            │     │      └──────────────────────────────┘     │     │
            │     │                                           │     │
            │     └───────────────────────────────────────────┘     │
            │                                                       │
            └───────────────────────────────────────────────────────┘
```

如图所示请求到达身份验证中间件后生成了一个登录响应，响应从身份验证中间穿越回中间件1然后返回给浏览器。


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
    use App\Middleware\AuthMiddleware;
    use App\Middleware\TestMiddleware;
    use DcrSwoole\Annotation\Mapping\Middlewares;
    use DcrSwoole\Annotation\Mapping\RequestMapping;

    #[RequestMapping(methods: "GET , POST", path:"/test/middleware")]
    #[Middlewares(AuthMiddleware::class, TestMiddleware::class)]
    public function test(): string
    {
        return 'ok';
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
