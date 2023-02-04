<?php

declare(strict_types=1);

namespace DcrSwoole\Utils;

class Utils
{
    /**
     * ScanDir.
     * @param string $basePath
     * @param bool $withBasePath
     * @return array
     */
    public static function scanDir(string $basePath, bool $withBasePath = true): array
    {
        if (!is_dir($basePath)) {
            return [];
        }
        $paths = array_diff(scandir($basePath), array('.', '..')) ?: [];
        return $withBasePath ? array_map(function ($path) use ($basePath) {
            return $basePath . DIRECTORY_SEPARATOR . $path;
        }, $paths) : $paths;
    }
}
