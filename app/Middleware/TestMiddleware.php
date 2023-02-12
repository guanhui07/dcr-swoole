<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Middleware\Contract\MiddlewareInterface;
use Closure;
use DcrSwoole\Log\LogBase;
use DcrSwoole\Request\Request;
use RuntimeException;

class TestMiddleware implements MiddlewareInterface
{
    public function handle(): Closure
    {
        return static function ($request, $next) {
            $data = di()->get(Request::class)->get();
            LogBase::info(var_export($data, true));
//            throw new RuntimeException('test middlere error');
            return $next->handle($request);
        };
    }
}
