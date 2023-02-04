<?php

declare(strict_types=1);

namespace  DcrSwoole\Log;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Log
{
    public function info($str)
    {
        $log = $this->getLogger(Logger::INFO);
        $log->warning((string)$str);
    }

    public function debug($str)
    {
        $log = $this->getLogger(Logger::INFO);
        $log->warning((string)$str);
    }

    public function warning($str)
    {
        $log = $this->getLogger(Logger::WARNING);
        $log->warning((string)$str);
    }

    public function error($str)
    {
        $log = $this->getLogger(Logger::ERROR);
        // add records to the log
        $log->error((string)$str);
    }

    public function write($str, $config = '')
    {
        $this->error($str);
    }

    /**
     * @param int $level
     * @return Logger
     */
    protected function getLogger($level = Logger::WARNING): Logger
    {
        $log = new Logger('name');
        $log->pushHandler(new StreamHandler(base_path() . 'runtime/log.log', $level));
        return $log;
    }
}
