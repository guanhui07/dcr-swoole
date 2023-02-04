<?php

declare(strict_types=1);

namespace App\Event;

use Swoole\WebSocket\Server;

class WebSocket
{
    /**
     * @param Server $server
     * @param $request
     */
    public static function onOpen(Server $server, $request)
    {
        echo "server: handshake success with fd{$request->fd}\n";
    }

    /**
     * @param Server $server
     * @param $frame
     */
    public static function onMessage(Server $server, $frame)
    {
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        $server->push($frame->fd, 'this is server');
    }

    /**
     * @param Server $server
     * @param $fd
     */
    public static function onClose(Server $server, $fd)
    {
    }
}
