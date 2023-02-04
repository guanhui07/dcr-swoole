<?php

declare(strict_types=1);

namespace App\Middleware\Contract;

interface MiddlewareInterface
{
    public function handle();
}
