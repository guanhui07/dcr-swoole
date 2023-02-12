
### Command 命令脚本
脚本任务 是服务器开发必须得，本框架使用 `inhere/php-validate` composer包实现


### Console 命令应用 需要在`app/Console/Kernel.php` 配置 命令类
```php
<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Repository\TestRepository;
use DcrSwoole\Utils\ApplicationContext;
use Inhere\Console\IO\Input;
use Inhere\Console\IO\Output;
use Toolkit\PFlag\FlagsParser;

/**
 * @package app\Console\Command
 * php artisan test
 */
class TestCommand extends \Inhere\Console\Command
{
    protected static string $name = 'test';

    protected static string $desc = 'print system ENV information';

    protected function configFlags(FlagsParser $fs): void
    {
        // 绑定选项
        $fs->addOptByRule('update, up', 'bool;update linux command docs to latest');
        $fs->addOptByRule('init, i', 'bool;update linux command docs to latest');
        $fs->addOptByRule('search, s', 'string;input keywords for search');

        // 绑定参数
        // - 这里没有设置必须 可以不传，获取到就是空string
        $fs->addArg('keywords', 'the keywords for search or show docs', 'string');
    }

    protected function execute(Input $input, Output $output)
    {
        $keywords = $this->flags->getOpt('search', 23);

        ApplicationContext::getContainer()->get(TestRepository::class)->fromRepos();
        $output->info("hello world ...");
    }
}


```
