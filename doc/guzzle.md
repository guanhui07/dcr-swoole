### guzzle client

本框架使用 `guanhui07/guzzle` composer包，基础于 `guzzle/guzzle` 实现 Guzzle HTTP 协程处理器 ,异步非阻塞型客户端


```php
    #[RequestMapping(methods: "GET , POST", path:"/test/guzzle")]
    public function guzzle($request, $response): string
    {
//        $client = di()->get(\GuzzleHttp\Client::class);
        $client = $this->guzzleClient;
        $result = $client->get('http://127.0.0.1:9501/test1');
        $ret = $result->getBody()->getContents();
        $result->getBody()->close();

        return 'test '.$ret;
    }
```


