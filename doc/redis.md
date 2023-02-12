
## redis 操作
```php
ApplicationContext::getContainer()->get(DataRedis::class);
//->setex ->get ->del ->setnx 等方法 和predis一致
```

