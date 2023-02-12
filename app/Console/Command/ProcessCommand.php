<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Utils\Process\Manage;
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
//        $keywords = $this->flags->getOpt('search', 23);
//        var_dump($keywords);
//
//        $name = $this->flags->getFirstArg();
//        if ( !$name && !$keywords) {
//            // env | grep XXX
//            $output->aList($_SERVER, 'ENV Information', ['ucFirst' => false]);
//            return;
//        }
//        ApplicationContext::getContainer()->get(TestRepository::class)->fromRepos();
//        $output->info("hello world ...");

        $manage = new Manage([
            'processNum' => 2,
            'heartTime' => 1,
            'name' => 'test process',
            'pre' => 'dcr',
            'logName' => base_path().'/runtime/console.log',
        ]);
        $manage->monitorStart();
    }
}
