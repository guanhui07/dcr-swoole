<?php

declare(strict_types=1);

namespace DcrSwoole;

use DcrSwoole\Config\Config;
use Exception;

class Listener
{
    private static $instance;

    private static $config;

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            self::$config = di()->get(Config::class)->get('listeners');
        }
        return self::$instance;
    }

    public function listen($listener, ...$args)
    {
        $listeners = self::$config[$listener] ?? [];
        while ($listeners) {
            [$class, $func] = array_shift($listeners);
            try {
                $class::getInstance()->{$func}(...$args);
            } catch (Exception $e) {
                throw new \RuntimeException($e->getMessage());
            }
        }
    }
}
