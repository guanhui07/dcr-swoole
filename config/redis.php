<?php

declare(strict_types=1);

return [
    'host' => $_ENV['redis_host'] ?? 'localhost',
    'port' =>$_ENV['redis_port'] ?? 6379,
    'auth' => $_ENV['redis_auth'] ?? '',
    'db_index' => 0,
    'time_out' => 1,
    'size' => 64, // pool size
];
