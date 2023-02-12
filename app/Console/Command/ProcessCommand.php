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
            'processNum' => 2,
            'heartTime' => 1,
            'name' => 'test process',
            'pre' => 'dcr',
            'logName' => base_path().'/runtime/console.log',
        ]);
        $manage->monitorStart();
    }
}
