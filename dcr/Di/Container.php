<?php

declare(strict_types=1);

namespace DcrSwoole\Di;

use DcrSwoole\Utils\ApplicationContext;
use DI\ContainerBuilder;
use Exception;

/**
 * 服务容器 Di 底层:反射Api 用的包 php-di
 * Class Container
 * @see  https://github.com/PHP-DI/PHP-DI
 */
class Container
{
    /**
     * @var \DI\Container
     */
    public static $container;

    /**
     * @see https://php-di.org/doc/attributes.html
     * @return \DI\Container
     * @throws Exception
     */
    public static function instance(): \DI\Container
    {
        if (!self::$container) {
            $containerBuilder = new ContainerBuilder();
            $containerBuilder->addDefinitions([
                //                'route' => \DI\create(Route::class)
            ]);
//            $containerBuilder->addDefinitions(config('dependence', []));
            // 通过以下方式ContainerBuilder启用属性：php8 原生注解 ,
            // 记得通过 导入属性类use DI\Attribute\Inject;
            $containerBuilder->useAttributes(true);
            $container = $containerBuilder->build();
            //            foreach ($configs as $k=>$config) {
            //                $container->make($config,[]);
            //            }
            self::$container = $container;
            ApplicationContext::setContainer($container);
            return $container;
        }

        return self::$container;
    }
}
