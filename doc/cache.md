## cache

dcr暂时只支持redis做缓存，支持连接池
连接池原理 使用`\Swoole\Database\RedisPool `,创建好连接放入池子，使用时候从池子拿连接，使用完毕丢回连接池，循环之。


## 使用
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

