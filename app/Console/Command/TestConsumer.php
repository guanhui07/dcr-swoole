<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Service\Consumer\BalancePayConsumer;
use Inhere\Console\Command;
use Inhere\Console\IO\Input;
use Inhere\Console\IO\Output;

/**
 * 测试使用 rabbitmq 消费者
 * @package app\Console\Command
 * php artisan test_consumer
 */
class TestConsumer extends Command
{
    protected static string $name = 'test_consumer';

    protected static string $desc = 'print system ENV information';

    protected function execute(Input $input, Output $output)
    {
        go(static function () {
            $producer = new BalancePayConsumer();
            $producer->consumer('balance_pay');
        });
        \Swoole\Event::wait();
    }
}
