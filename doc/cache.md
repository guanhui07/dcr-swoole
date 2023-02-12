## cache

dcr暂时只支持redis做缓存，支持连接池

```php
use DcrSwoole\Utils\DataRedis;


#[Inject]
public DataRedis $redis;
```

```php
    $this->redis->setex('test',60,'val');
    $this->redis->get('test');
```
操作和predis一致

