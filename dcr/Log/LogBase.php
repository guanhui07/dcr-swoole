<?php

declare(strict_types=1);

namespace  DcrSwoole\Log;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * Class Log
 * @see https://github.com/Seldaek/monolog
 */
class LogBase
{
    public static function info($str): void
    {
        $log = self::getLogger(Logger::INFO);
        $log->warning((string)$str);
    }

    public static function debug($str): void
    {
        $log = self::getLogger(Logger::INFO);
        $log->warning((string)$str);
    }

    public static function warning($str)
    {
        $log = self::getLogger(Logger::WARNING);
        $log->warning((string)$str);
    }

    public static function error($str): void
    {
        $log = self::getLogger(Logger::ERROR);
        // add records to the log
        $log->error((string)$str);
    }

    public static function write($str, $config = ''): void
    {
        self::error($str);
    }

    /**
     * @param int $level
     *
     * @return Logger
     */
    protected static function getLogger($level = Logger::WARNING): Logger
    {
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler(base_path() . 'runtime/log.log', $level));
        return $log;
    }
}
