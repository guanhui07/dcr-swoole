<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Middleware\Contract\MiddlewareInterface;
use App\Model\UserModel;
use Closure;
use DcrSwoole\Log\LogBase;
use DcrSwoole\Request\Request;
use RuntimeException;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(): Closure
    {
        return static function ($request, $next) {
            echo 'auth login middleware';
            $data = Request::instance()->post;
            LogBase::info(var_export($data, true));
            $token = $data['token'] ?? '';
            if (!$token) {
                throw new RuntimeException('has not login');
            }
//            $user = UserModel::query()->where('token', $token)->first();
//            if ($user) {
//                throw new RuntimeException('未登录');
//            }
            return $next->handle($request);
        };
    }
}
