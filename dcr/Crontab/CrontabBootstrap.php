<?php

declare(strict_types=1);

namespace DcrSwoole\Crontab;

use DcrSwooleCrontab\Process\CrontabDispatcherProcess;
use DI\DependencyException;
use DI\NotFoundException;

/**
 * 秒级定时任务调度 -参考的hyperf 配置在config/crontab.php
 * Class CrontabBootstrap
 * @package DcrSwoole\Crontab
 * @see  https://github.com/guanhui07/dcr-swoole-crontab
 */
class CrontabBootstrap
{
    /**
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function run()
    {
        if (di()->get(\DcrSwoole\Config\Config::class)->get('crontab.enable') === true) {
            $crontab = new CrontabDispatcherProcess();
            $crontab->handle();
        }
    }
}
