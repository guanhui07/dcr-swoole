<?php

declare(strict_types=1);

namespace DcrSwoole\Utils;

use Closure;
use Swoole\Coroutine;

class Context
{
    protected static array $nonCoContext = [];

    public static function set(string $id, $value)
    {
        if (self::inCoroutine()) {
            Coroutine::getContext()[$id] = $value;
        } else {
            static::$nonCoContext[$id] = $value;
        }
        return $value;
    }

    /**
     * @param string $id
     * @param null $default
     * @param null $coroutineId
     * @return mixed
     */
    public static function get(string $id, $default = null, $coroutineId = null): mixed
    {
        if (self::inCoroutine()) {
            if ($coroutineId !== null) {
                return Coroutine::getContext($coroutineId)[$id] ?? $default;
            }
            return Coroutine::getContext()[$id] ?? $default;
        }

        return static::$nonCoContext[$id] ?? $default;
    }

    /**
     * @param string $id
     * @param null $coroutineId
     * @return bool
     */
    public static function has(string $id, $coroutineId = null): bool
    {
        if (self::inCoroutine()) {
            if ($coroutineId !== null) {
                return isset(Coroutine::getContext($coroutineId)[$id]);
            }
            return isset(Coroutine::getContext()[$id]);
        }

        return isset(static::$nonCoContext[$id]);
    }

    /**
     * Release the context when you are not in coroutine environment.
     * @param string $id
     */
    public static function destroy(string $id): void
    {
        unset(static::$nonCoContext[$id]);
    }

    /**
     * Retrieve the value and override it by closure.
     * @param string $id
     * @param Closure $closure
     * @return mixed
     */
    public static function override(string $id, Closure $closure): mixed
    {
        $value = null;
        if (self::has($id)) {
            $value = self::get($id);
        }
        $value = $closure($value);
        self::set($id, $value);
        return $value;
    }

    public static function inCoroutine(): bool
    {
        return Coroutine::getCid() > 0;
    }
}
