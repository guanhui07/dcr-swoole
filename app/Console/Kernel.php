<?php

declare(strict_types=1);

namespace App\Console;

use App\Console\Command\Test2;
use App\Console\Command\Test2Consumer;

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
            Test2::class,
            Test2Consumer::class,
        ];
    }
}
