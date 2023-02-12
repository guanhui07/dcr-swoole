
### crontab定时任务 需要在 `/config/crontab.php` 配置 定时任务
```php
<?php

declare(strict_types=1);

namespace App\Crontab;

use App\Crontab\Contract\CrontabInterface;
use App\Repository\TestRepository;
use DcrSwoole\Utils\ApplicationContext;

class TestCrontab implements CrontabInterface
{
    public function execute(): void
    {
        ApplicationContext::getContainer()->get(TestRepository::class)->test1();
    }
}

```