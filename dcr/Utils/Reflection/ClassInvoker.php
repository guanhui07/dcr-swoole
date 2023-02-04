<?php

declare(strict_types=1);

namespace DcrSwoole\Utils\Reflection;

use ReflectionClass;

class ClassInvoker
{
    /**
     * @var object
     */
    protected $instance;

    /**
     * @var ReflectionClass
     */
    protected $reflection;

    public function __construct(object $instance)
    {
        $this->instance = $instance;
        $this->reflection = new ReflectionClass($instance);
    }

    public function __get($name)
    {
        $property = $this->reflection->getProperty($name);

        $property->setAccessible(true);

        return $property->getValue($this->instance);
    }

    public function __set($key, $value)
    {
    }

    public function __isset(string $key)
    {
    }

    public function __call($name, $arguments)
    {
        $method = $this->reflection->getMethod($name);

        $method->setAccessible(true);

        return $method->invokeArgs($this->instance, $arguments);
    }
}
