<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Command\ProcessCommand;
use App\Console\Command\TestCommand;
use App\Console\Command\TestConsumer;

/**
 * 所有的命令类注册 类
 */
class Kernel
{
    /**
     * @todo 通过反射实现#Command注解
     * @see https://github.com/inhere/php-console/wiki
     */
    public static function getCommands(): array
    {
        return [
            TestCommand::class,
            TestConsumer::class,
            ProcessCommand::class,
        ];
    }
}
