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