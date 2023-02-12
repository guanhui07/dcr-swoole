## config

配置是软件开发必不可少的，获取`config`目录配置


### 获取配置 需要`use DI\Attribute\Inject`
```php
    #[Inject]
    public Config $config;

    #[RequestMapping(methods: "GET , POST", path:"/test/config")]
    public function config()
    {
        //di()->get(Config::class)->get('app.debug');
        // config('app.debug');
        return $this->config->get('app.debug');
    }
```


