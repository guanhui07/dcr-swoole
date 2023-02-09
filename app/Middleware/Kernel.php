<?php

declare(strict_types=1);

namespace App\Middleware;

/**
 * Class Kernel
 * @package app\Middleware
 * @see @see https://github.com/middlewares/utils
 */
class Kernel
{
    public static function getMiddlewares(): array
    {
        return [
            TestMiddleware::class => TestMiddleware::class,
            AuthMiddleware::class => AuthMiddleware::class,
            RateLimitMiddleware::class => RateLimitMiddleware::class,
        ];
    }
}
