<?php
declare(strict_types=1);


namespace App\Aspect\Contract;


use Hyperf\Di\Aop\ProceedingJoinPoint;

interface AspectInterface
{
    public function process(ProceedingJoinPoint $proceedingJoinPoint);
}