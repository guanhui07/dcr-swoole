## validator 

验证器可以有效减少 if else 判断，本框架基础`inhere/php-validate` composer包 实现验证器
[https://github.com/inhere/php-validate](https://github.com/inhere/php-validate)

### 控制器validate
```php
    #[RequestMapping(methods: "GET , POST", path:"/test/test4")]
    public function test4($request, $response)
    {
        $validate = Validation::check($this->request->post ?? [], [
            // add rule
            ['title', 'min', 40],
            ['freeTime', 'number'],
        ]);

        if ($validate->isFail()) {
            var_dump($validate->getErrors());
            var_dump($validate->firstError());
        }

        // $postData = $v->all(); // 原始数据
        $safeData = $validate->getSafeData(); // 验证通过的安全数据

        return $safeData;
    }
```
