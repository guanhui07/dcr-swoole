<?php

namespace App\Utils;

use DcrSwoole\Config\Config;

/**
 * Class Enviroment
 */
class Enviroment
{
    public static function isProd()
    {
        return di()->get(Config::class)->get('app.env') === 'prod';
    }

    public static function isRoyeeDev($name='test')
    {
        if (file_exists('/Users/'.$name)) {
            return true;
        }
        return false;
    }

    public static function isDev(): bool
    {
        return di()->get(Config::class)->get('app.env') === 'dev';
    }

    public static function isLocal(): bool
    {
        return di()->get(Config::class)->get('app.env') === 'local';
    }
}
