
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


