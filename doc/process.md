
## process

多进程处理任务，消费队列是软件开发必不可少

### 原理
基础于 `Swoole\Process` 实现

### 定义启动命令 
```php
<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Process\TestProcess;
use DcrSwoole\Process\Manage;
use Inhere\Console\Command;
use Inhere\Console\IO\Input;
use Inhere\Console\IO\Output;
use Toolkit\PFlag\FlagsParser;

/**
 * php artisan process
 * 需要配置app/Console/Kernel.php
 */
class ProcessCommand extends Command
{
    protected static string $name = 'process';

    protected static string $desc = 'print system ENV information';

    protected function configFlags(FlagsParser $fs): void
    {
    }

    protected function execute(Input $input, Output $output)
    {
        $manage = new TestProcess([
            'processNum' => 8,// 开启的进程数
            'heartTime' => 1,
            'name' => 'test process',
            'pre' => 'dcr',
            'logName' => base_path().'/runtime/console.log',
        ]);
        $manage->monitorStart();
    }
}

```

### 定义Process类
```php
<?php
declare(strict_types=1);


namespace App\Process;


use DcrSwoole\Process\Manage;

class TestProcess extends Manage implements ProcessInterface
{
    public function hook(): void
    {
        while (true) {
            echo 'process ts1';
            sleep(1);
        }
    }
}
```


