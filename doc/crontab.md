## crontab

秒级别定时任务，基础于swoole实现  `guanhui07/dcr-swoole-crontab` composer 包,参考的hyperf 的`crontab`包
底层为 `Swoole\Timer`  



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


`/config/crontab.php`

```php

<?php

use App\Crontab\TestCrontab;
use DcrSwooleCrontab\Crontab;

return [
    'enable' => $_ENV['crontab_enable'] ?? true,

    'crontab' => [
        (new Crontab())->setName('test-1')
            ->setRule('* * * * * *')
            ->setCallback([TestCrontab::class, 'run'])
            ->setMemo('just a test crontab'),
//        (new Crontab())->setName('test-2')
//            ->setRule('* * * * * *')
//            ->setCallback([TestCrontab::class, 'run'])
//            ->setMemo('just another test crontab'),
    ],
];
```
