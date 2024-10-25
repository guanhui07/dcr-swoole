<?php
declare(strict_types=1);
/**
 * The file is part of Dcr/framework
 *
 *
 */

namespace App\Utils;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log
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

    public static function warning($str): void
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
     * @return Logger
     */
    public static function getLogger($level = Logger::WARNING): Logger
    {
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler(PROJECT_ROOT . 'runtime/log.log', $level));
        return $log;
    }
}
