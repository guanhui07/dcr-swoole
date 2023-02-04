<?php

declare(strict_types=1);

namespace App\Console;

/**
 * 所有的命令类注册 类
 * Class Kernel
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
            \App\Console\Command\Test2::class,
            \App\Console\Command\Test2Consumer::class,
        ];
    }
}
