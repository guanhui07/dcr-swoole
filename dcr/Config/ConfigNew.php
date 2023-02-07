<?php

declare(strict_types=1);

namespace DcrSwoole\Config;

class ConfigNew
{
    private static $instance;

    private static array $config = [];

    public static function getInstance(): ConfigNew
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @param $keys
     * @param null $default
     * @return mixed
     */
    public function get($keys, $default = null): mixed
    {
        $keys = explode('.', strtolower($keys));
        if (empty($keys)) {
            return null;
        }

        $file = array_shift($keys);

        if (empty(self::$config[$file])) {
            if (!is_file(CONFIG_PATH . $file . '.php')) {
                return null;
            }
            self::$config[$file] = include CONFIG_PATH . $file . '.php';
        }
        $config = self::$config[$file];

        while ($keys) {
            $key = array_shift($keys);
            if (!isset($config[$key])) {
                $config = $default;
                break;
            }
            $config = $config[$key];
        }

        return $config;
    }
}
