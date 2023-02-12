## request

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