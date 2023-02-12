
## Inject

## 方式一
```php
   #[Inject]
    public TestService $testService;
```

注意引入
`use DI\Attribute\Inject;`


## 方式二
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