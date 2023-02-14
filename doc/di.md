
## 依赖自动注入


## 依赖注入方式一 使用注解
```php
   #[Inject]
    public TestService $testService;
```

注意引入
`use DI\Attribute\Inject;`


## 方式二 构造方式
```php
    public TestService $testService;

    /**
     * @param TestService $testService
     */
    public function __construct(TestService $testService)
    {
        parent::__construct();
        $this->testService = $testService;
    }
```
