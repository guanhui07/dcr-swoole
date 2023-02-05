<?php

namespace App\Utils;

/**
 * Class Enviroment
 */
class Enviroment
{
    public static function isProd()
    {
        return di()->get(\DcrSwoole\Config\Config::class)->get('app.env') === 'prod';
    }

    public static function isRoyeeDev($name='test')
    {
        if (file_exists('/Users/'.$name)) {
            return true;
        }
        return false;
    }

    public static function isDev()
    {
        return di()->get(\DcrSwoole\Config\Config::class)->get('app.env') === 'dev';
    }

    public static function isLocal()
    {
        return di()->get(\DcrSwoole\Config\Config::class)->get('app.env') === 'local';
    }
}
