<?php

declare(strict_types=1);

namespace DcrSwoole\Response;

use App\Utils\Json;
use DcrSwoole\Utils\Context;

/**
 * Class Response
 * @package dcr\Response
 * @see swoole文档 Response
 */
class Response
{
    /**
     * @var \Swoole\Http\Response
     */
    public static $response;

    /**
     * @return \Swoole\Http\Response
     */
    public static function instance(): \Swoole\Http\Response
    {
        if (!self::$response) {
            /**
             * @var   $ins \Swoole\Http\Response
             */
            $ins = Context::get('SwResponse');
            self::$response = $ins;
            return self::$response;
        }

        return self::$response;
    }

    public static function setResponse(): void
    {
        $ins = Context::get('SwResponse');
        self::$response = $ins;
    }


    public function header(string $key, string $value, bool $format = true): bool
    {
        return self::$response->header($key, $value, $format);
    }

    public function redirect(string $url, int $httpCode = 302): bool
    {
        return self::$response->redirect($url, $httpCode);
    }

    public function write(string $data): bool
    {
        return self::$response->write($data);
    }

    public function sendfile(string $filename, int $offset = 0, int $length = 0): bool
    {
        return self::$response->sendfile($filename, $offset, $length);
    }

    public function end(mixed $data): bool
    {
        if (is_array($data)) {
            $data = Json::encode($data);
        }
        if ($data instanceof self) {
        }
        return @self::$response->end($data);
    }
}
