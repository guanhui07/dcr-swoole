<?php

declare(strict_types=1);

namespace DcrSwoole\Config;

use Illuminate\Support\Arr;

class Config
{
    public function get($str = 'app', $default = null)
    {
        // config('app.debug');
        if (str_contains($str, '.')) {
            $tmpArr = explode('.', $str, 2);

            $arr = require(base_path() . 'config/' . $tmpArr[0] . '.php');
            $end = end($tmpArr);
            $arr = Arr::get($arr, $end, $default);
            return $this->dealArr($arr);
        }

        // config('app')
        $arr = require(base_path() . 'config/' . $str . '.php');
        $arr = $this->dealArr($arr);

        return $arr;
    }

    /**
     * @param mixed $data
     *
     * @return bool|mixed
     */
    protected function dealArr(mixed $data): mixed
    {
        if (is_string($data)) {
            return match (strtolower($data)) {
                'true', '(true)' => true,
                'false', '(false)' => false,
                'empty', '(empty)' => '',
                'null', '(null)' => null,
                default => $data,
            };
        }
        if (is_array($data)) {
            return $this->processConfigArr($data);
        }
        return $data;
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    protected function isTrue(mixed $data): bool
    {
        return is_string($data) && $data === 'true';
    }

    /**
     * @param mixed $data
     *
     * @return bool
     */
    protected function isFalse(mixed $data): bool
    {
        return is_string($data) && $data === 'false';
    }

    /**
     * @param array $data
     *
     * @return array
     */
    protected function processConfigArr(array $data): array
    {
        return array_map(static function ($value) {
            if (!is_string($value)) {
                return $value;
            }
            return match (strtolower($value)) {
                'true', '(true)' => true,
                'false', '(false)' => false,
                'empty', '(empty)' => '',
                'null', '(null)' => null,
                default => $value,
            };
        }, $data);
    }
}
