#!/usr/bin/env php
<?php

use DcrSwoole\Di\Container;
use DcrSwoole\Server\Application;
use Swoole\Runtime;

!defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));
!defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', SWOOLE_HOOK_ALL);
!defined('CONFIG_PATH') && define('CONFIG_PATH', dirname(__DIR__) . '/config/');
!defined('PROJECT_ROOT') && define('PROJECT_ROOT', dirname(__DIR__) . '/');

require BASE_PATH . '/vendor/autoload.php';

Runtime::enableCoroutine(SWOOLE_HOOK_FLAGS);

try {
    Container::instance();
} catch (Exception $e) {
}
Application::run();


