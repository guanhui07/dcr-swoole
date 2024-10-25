<?php

namespace App\Utils;

class Log
{
    public static function debug(string $message)
    {
        echo $message.PHP_EOL;
    }

    public static function error(string $message)
    {
        echo $message.PHP_EOL;
    }
}