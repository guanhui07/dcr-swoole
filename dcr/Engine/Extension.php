<?php

declare(strict_types=1);

namespace DcrSwoole\Engine;

class Extension
{
    public static function isLoaded(): bool
    {
        return extension_loaded('Swoole');
    }
}
