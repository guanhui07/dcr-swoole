
## aop切面

# AOP 面向切面编程

## 概念

AOP 为 `Aspect Oriented Programming` 的缩写，意为：`面向切面编程`，通过动态代理等技术实现程序功能的统一维护的一种技术。AOP 是 OOP 的延续，也是 Hyperf 中的一个重要内容，是函数式编程的一种衍生范型。利用 AOP 可以对业务逻辑的各个部分进行隔离，从而使得业务逻辑各部分之间的耦合度降低，提高程序的可重用性，同时提高了开发的效率。

用通俗的话来讲，就是在 Hyperf 里可以通过 `切面(Aspect)` 介入到任意类的任意方法的执行流程中去，从而改变或加强原方法的功能，这就是 AOP。

> 注意这里所指的任意类并不是完全意义上的所有类，在 Hyperf 启动初期用于实现 AOP 功能的类自身不能被切入。

## 介绍

相对于其它框架实现的 AOP 功能的使用方式，我们进一步简化了该功能的使用不做过细的划分，仅存在 `环绕(Around)` 一种通用的形式：

- `切面(Aspect)` 为对流程织入的定义类，包括要介入的目标，以及实现对原方法的修改加强处理
- `代理类(ProxyClass)` ，每个被介入的目标类最终都会生成一个代理类，来达到执行 `切面(Aspect)` 方法的目的

## 定义切面(Aspect)


```php
<?php
declare(strict_types=1);


namespace App\Aspect;


use App\Service\UserService;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Exception\Exception;

/**
 * 需要再config/config 文件配置 切面类
 */
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
        var_dump('test aop ');
        try {
            return $proceedingJoinPoint->process();
        } catch (Exception $e) {
        }
    }
}
```

