## model 模型

和laravel orm 一致 ，使用的`guanhui07/database` composer包 ,基础于 `illuminate/database` 实现, 基于Swoole 适配封装的`连接池`
使用的 `Swoole\ConnectionPool` 
源码 https://github.com/guanhui07/database/blob/HEAD/src/PDOPool.php#L27-L28


## orm model ，使用和laravel orm一致
```php
<?php

declare(strict_types=1);

namespace App\Model;

use DcrSwoole\DbConnection\Model;
/**
 * Class UserModel
 * @see https://github.com/illuminate/database
 * @property int $id
 * @property string $created_at
 */
class UserModel extends Model
{
    protected $table = 'user';
}


```

