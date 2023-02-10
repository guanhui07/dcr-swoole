<?php
declare(strict_types=1);

use App\Crontab\TestCrontab;
use DcrSwooleCrontab\Crontab;

// @see https://github.com/guanhui07/dcr-crontab
// @todo 通过反射实现#Crontab注解
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