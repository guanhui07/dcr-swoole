## response

```php
    #[RequestMapping(methods: "GET , POST", path:"/index/test2")]
    #[Middlewares(AuthMiddleware::class, TestMiddleware::class)]
    public function test2()
    {
//        return 'test 1121';
        return [];
    }
```