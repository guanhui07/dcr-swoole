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
            $arr = Arr::get($arr, $end, '');
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
    protected function dealArr(mixed $data)
    {
        if (is_string($data)) {
            switch (strtolower($data)) {
                case 'true':
                case '(true)':
                    return true;
                case 'false':
                case '(false)':
                    return false;
                case 'empty':
                case '(empty)':
                    return '';
                case 'null':
                case '(null)':
                    return null;
                default:
                    return $data;
            }
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
     * @return bool|mixed
     */
    protected function processConfigArr(array $data)
    {
        return array_map(function ($value) {
            if (!is_string($value)) {
                return $value;
            }
            switch (strtolower($value)) {
                case 'true':
                case '(true)':
                    return true;
                case 'false':
                case '(false)':
                    return false;
                case 'empty':
                case '(empty)':
                    return '';
                case 'null':
                case '(null)':
                    return null;
                default:
                    return $value;
            }
        }, $data);
    }
}
