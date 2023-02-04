<?php

declare(strict_types=1);

namespace DcrSwoole\Request;

use DcrSwoole\Utils\Context;

/**
 * Class Request
 * @package dcr\Request
 * @see swoole文档 request
 */
class Request
{
    /**
     * @var \Swoole\Http\Request
     */
    public static $request;

    /**
     * @return mixed|\Swoole\Http\Request|null
     */
    public static function instance(): mixed
    {
        if (!self::$request) {
            $ins = Context::get('SwRequest');
            self::$request = $ins;
            return self::$request;
        }

        return self::$request;
    }

    public static function setRequest()
    {
        $ins = Context::get('SwRequest');
        self::$request = $ins;
    }

    public function all()
    {
        return (array)($this->post() + $this->get());
    }

    public function post($name = null, $default = null)
    {
        if ($name !== null) {
            return self::$request->post[$name] ?? $default;
        }
        return (array)self::$request->post;
    }

    public function get($name = null, $default = null)
    {
        if ($name !== null) {
            return self::$request->get[$name] ?? $default;
        }
        return (array)self::$request->get;
    }

    /**
     * Input
     * @param string $name
     * @param mixed $default
     * @return mixed|null
     */
    public function input(string $name, $default = null)
    {
        $post = $this->post();
        if (isset($post[$name])) {
            return $post[$name];
        }
        $get = $this->get();
        return $get[$name] ?? $default;
    }

    /**
     * Only
     * @param array $keys
     * @return array
     */
    public function only(array $keys): array
    {
        $all = $this->all();
        $result = [];
        foreach ($keys as $key) {
            if (isset($all[$key])) {
                $result[$key] = $all[$key];
            }
        }
        return $result;
    }

    /**
     * Except
     * @param array $keys
     * @return mixed|null
     */
    public function except(array $keys)
    {
        $all = $this->all();
        foreach ($keys as $key) {
            unset($all[$key]);
        }
        return $all;
    }

    /**
     * File
     */
    public function file($name = null)
    {
        $files = self::$request->files;
        if (null === $files) {
            return $name === null ? [] : null;
        }
        if ($name !== null) {
            // Multi files
            if (is_array(current($files))) {
                return $files;
            }
            return $files;
        }

        return $files;
    }

    /**
     * 类似 fpm file_get_contents('php://input')
     * @return string
     */
    public function getContent(): string
    {
        return self::$request->getContent();
    }

    public function getRawContent(): string
    {
        return $this->getContent();
    }

    /**
     * GetRemoteIp
     * @return string
     */
    public function getRemoteIp(): string
    {
        return self::$request->header['x-real-ip'];
    }

    /**
     * @see https://wiki.swoole.com/#/http_server?id=header
     */
    public function allHeader()
    {
        return (array)self::$request->header;
    }

    public function cookie($name)
    {
        return self::$request->cookie[$name] ?? null;
    }

    public function allCookie()
    {
        return (array)self::$request->cookie;
    }

    public function allServer()
    {
        return (array)self::$request->server;
    }

    /**
     * GetRealIp
     * @return string
     */
    public function getRealIp(): string
    {
        return $this->getRemoteIp();
    }


    public function getMethod()
    {
        return self::$request->server['request_method'];
    }


    /**
     * Get the full URL for the request.
     */
    public function fullUrl()
    {
        $uri = $this->allServer()['request_uri'];
        return $uri . '?' . http_build_query($this->get(), '', '&', PHP_QUERY_RFC3986);
    }


    public function normalizeQueryString(string $qs): string
    {
        if ($qs === '') {
            return '';
        }

        parse_str($qs, $qs);
        $qs1 = (array)$qs;
        ksort($qs1);

        return http_build_query($qs, '', '&', PHP_QUERY_RFC3986);
    }


    protected function getRequest()
    {
        return Context::get('SwRequest');
    }
}
