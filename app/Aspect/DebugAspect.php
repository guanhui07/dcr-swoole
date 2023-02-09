<?php
declare(strict_types=1);


namespace App\Aspect;


use App\Service\UserService;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;

class DebugAspect
{
    public array $classes = [
        UserService::class . '::first',
    ];

    /**
     * 测试切面
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        var_dump(11);
        try {
            return $proceedingJoinPoint->process();
        } catch (Exception $e) {
        }
    }
}