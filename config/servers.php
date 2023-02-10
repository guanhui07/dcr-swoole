<?php

declare(strict_types=1);


use App\Event\WebSocket;
use Swoole\Constant;

return [
    'mode' => SWOOLE_PROCESS,
    'http' => [
        'ip' => '0.0.0.0',
        'port' => 9501,
        'sock_type' => SWOOLE_SOCK_TCP,
        'callbacks' => [
        ],
        'settings' => [
            'worker_num' => swoole_cpu_num(),
            Constant::OPTION_ENABLE_COROUTINE => true,
            Constant::OPTION_OPEN_TCP_NODELAY => true,
            Constant::OPTION_MAX_COROUTINE => 100000,
            Constant::OPTION_OPEN_HTTP2_PROTOCOL => true,
            Constant::OPTION_MAX_REQUEST => 100000,
            Constant::OPTION_SOCKET_BUFFER_SIZE => 2 * 1024 * 1024,
            Constant::OPTION_BUFFER_OUTPUT_SIZE => 2 * 1024 * 1024,
        ],
    ],
    'ws' => [
        'ip' => '0.0.0.0',
        'port' => 9502,
        'sock_type' => SWOOLE_SOCK_TCP,
        'callbacks' => [
            "open" => [WebSocket::class, 'onOpen'],
            "message" => [WebSocket::class, 'onMessage'],
            "close" => [WebSocket::class, 'onClose'],
        ],
        'settings' => [
            'worker_num' => swoole_cpu_num(),
            'open_websocket_protocol' => true,
        ],
    ],
];
