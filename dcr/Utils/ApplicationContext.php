<?php

declare(strict_types=1);

namespace DcrSwoole\Utils;

class ApplicationContext
{
    /**
     * @var \DI\Container
     */
    private static $container;
    
    public static function getContainer(): \DI\Container
    {
        return self::$container;
    }

    public static function hasContainer(): bool
    {
        return isset(self::$container);
    }

    public static function setContainer(\DI\Container $container): \DI\Container
    {
        self::$container = $container;
        return $container;
    }
}
