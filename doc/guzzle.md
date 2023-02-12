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


