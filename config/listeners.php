<?php

declare(strict_types=1);


use DcrSwoole\Server\WorkerStart;

return [
    //Server::onStart
    'start' => [
    ],
    //Server::onWorkerStart
    'workerStart' => [
        [WorkerStart::class, 'workerStart'],

    ],
];
