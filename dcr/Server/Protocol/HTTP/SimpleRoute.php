<?php

declare(strict_types=1);

namespace DcrSwoole\Server\Protocol\HTTP;

use DcrSwoole\Config\Config;
use DcrSwoole\Framework\Router;
use DcrSwoole\Utils\ApplicationContext;
use FastRoute\Dispatcher;
use RuntimeException;

use function FastRoute\simpleDispatcher;

/**
 * 不用简单路由模式
 * Class SimpleRoute
 * @package DcrSwoole\Server\Protocol\HTTP
 */
class SimpleRoute
{
    /**
     * @var SimpleRoute
     */
    private static $instance;

    private static $config;

    private static $dispatcher = null;

    private static $cache = [];

    public static $uriToMiddlewares = [];

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            // 配置文件获取
            di()->get(Config::class)->get('routes', []);
            self::$config = [];

            $annotations = Router::getRoutes();
            self::$dispatcher = simpleDispatcher(
                function (\FastRoute\RouteCollector $routerCollector) use ($annotations) {
//                    foreach (self::$config as $routerDefine) {
//                        $routerCollector->addRoute($routerDefine[0], $routerDefine[1], $routerDefine[2]);
//                    }
                    foreach ($annotations as $routerDefine) {
//                        $routerCollector->addRoute($routerDefine[0], $routerDefine[1], $routerDefine[2]);
                        $routerCollector->addRoute($routerDefine->getMethods(), $routerDefine->getPath(), $routerDefine->getCallback());

                        self::$uriToMiddlewares[$routerDefine->getPath()] = $routerDefine->getMiddleware();

                    }
                }
            );
        }
        return self::$instance;
    }

    /**
     * @param $server
     * @param $fd
     * @param $data
     * @return mixed
     * @throws \Exception
     */
    public function dispatch($server, $fd, $data)
    {
        $container = ApplicationContext::getContainer();

        $first_line = \strstr($data, "\r\n", true);
        $tmp = \explode(' ', $first_line, 3);
        $method = $tmp[0] ?? 'GET';
        $uri = $tmp[1] ?? '/';
        $routeInfo = self::$dispatcher->dispatch($method, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];

                if (isset(self::$cache[$handler])) {
                    $cache_entity = self::$cache[$handler];
                    return $cache_entity[0]->{$cache_entity[1]}($server, $fd, $vars ?? null);
                }

                if (is_string($handler)) {
                    $handlerArr = explode('@', $handler);
                    if (count($handlerArr) != 2) {
                        throw new RuntimeException("Route {$uri} config error, Only @ are supported");
                    }

                    $className = $handlerArr[0];
                    $func = $handlerArr[1];

                    if (!class_exists($className)) {
                        throw new RuntimeException("Route {$uri} defined '{$className}' Class Not Found");
                    }

//                    $controller = new $className();
                    $controller = $container->get($className);
                    if (!method_exists($controller, $func)) {
                        throw new RuntimeException("Route {$uri} defined '{$func}' Method Not Found");
                    }

                    self::$cache[$handler] = [$controller, $func];
                    return $controller->{$func}($server, $fd, $vars ?? null);
                }
                if (is_callable($handler)) {
                    return call_user_func_array($handler, [$server, $fd, $vars ?? null]);
                }

                throw new RuntimeException("Route {$uri} config error");
                break;
            case Dispatcher::NOT_FOUND:
                return $this->defaultRouter($server, $fd, $uri);
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                return $server->send($fd, SimpleResponse::build('', 405));
//                throw new RuntimeException('Request Method Not Allowed', 405);
                break;
            default:
                return $server->send($fd, SimpleResponse::build('', 400));
        }
        throw new RuntimeException("Undefined Route {$uri}");
    }

    /**
     * @param $server
     * @param $fd
     * @param $uri
     * @return mixed
     * @throws \Exception
     */
    public function defaultRouter($server, $fd, $uri)
    {
        $uri = trim($uri, '/');
        $uri = explode('/', $uri);

        if ($uri[0] === '') {
            $className = '\\App\\Controller\\IndexController';
            if (class_exists($className) && method_exists($className, 'index')) {
                return (new $className())->index($server, $fd);
            }
//            throw new RuntimeException('The default route index/index class does not exist', 404);
        }
        return $server->send($fd, SimpleResponse::build('', 404));
//        throw new RuntimeException('Route Not Found', 404);
    }
}
