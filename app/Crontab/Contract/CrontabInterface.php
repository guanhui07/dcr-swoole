<?php

declare(strict_types=1);

namespace App\Crontab\Contract;

interface CrontabInterface
{
    public function execute();
}
