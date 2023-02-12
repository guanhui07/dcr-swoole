## request

### 实现原理

Swoole接收到请求在onRequest 回调到此方法，调用有 $request,$response 2个参数
```php
   public function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        Context::set('SwRequest', $request);
        Context::set('SwResponse', $response);
        Request::setRequest();
        Response::setResponse();
        $this->_route->dispatch($request, $response);
    }
```


## 示例代码

```php
    #[RequestMapping(methods: "GET , POST", path:"/index/test2")]
    #[Middlewares(AuthMiddleware::class, TestMiddleware::class)]
    public function test2(): string
    {
        di()->get(Request::class)->get();
        di()->get(Request::class)->get('name');
        di()->get(Request::class)->post();
        di()->get(Request::class)->post('name');
        
        di()->get(Request::class)->getRawContent();
    }
```
