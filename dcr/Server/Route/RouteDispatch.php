<?php

declare(strict_types=1);

namespace DcrSwoole\Server\Route;

use App\Middleware\Contract\MiddlewareInterface;
use App\Middleware\Kernel;
use App\Utils\Json;
use ArrayAccess;
use DcrSwoole\Framework\Router;
use DcrSwoole\Response\Response;
use DcrSwoole\Route\Route;
use DcrSwoole\Utils\ApplicationContext;
use DcrSwoole\Utils\Collection;
use DcrSwoole\Utils\Context;
use DcrSwoole\Utils\Contracts\Arrayable;
use Exception;
use FastRoute\Dispatcher;
use RuntimeException;
use Throwable;

use function FastRoute\simpleDispatcher;

class RouteDispatch
{
    private static $instance;

    private static $config;

    /**
     * @var Dispatcher
     */
    private static $dispatcher = null;

    public static $uriToMiddlewares = [];

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
            // 路由
            di()->get(\DcrSwoole\Config\Config::class)->get('routes', []);
//            self::$config = Config::getInstance()->get('routes', []);
            self::$config = [];

            $annotations = Router::getRoutes();

//            print_r($annotations);

            self::$dispatcher = simpleDispatcher(
                function (\FastRoute\RouteCollector $routerCollector) use ($annotations) {
//                    foreach (self::$config as $routerDefine) {
//                        /** @var Route $routerDefine */
//                        $routerCollector->addRoute($routerDefine->getMethods(), $routerDefine->getPath(), $routerDefine->getCallback());
//                    }
                    foreach ($annotations as $routerDefine) {
                        /** @var Route $routerDefine */
                        $routerCollector->addRoute($routerDefine->getMethods(), $routerDefine->getPath(), $routerDefine->getCallback());
//                        $routerCollector->addRoute($routerDefine[0], $routerDefine[1], $routerDefine[2]);
//                        var_dump($routerDefine->getPath(),$routerDefine->getMiddleware());
                        self::$uriToMiddlewares[$routerDefine->getPath()] = $routerDefine->getMiddleware();
                    }
//                    print_r(self::$uriToMiddlewares);
                }
            );
        }
        return self::$instance;
    }

    public function middleware($middleware)
    {
        $middlewares = Kernel::getMiddlewares();
        $arr = [];

        foreach ((array)$middleware as $m) {
            /** @var MiddlewareInterface $obj */
            $class = $middlewares[$m];
            $obj = (new $class());
            $arr[] = $obj->handle();
        }
        try {
            \Middlewares\Utils\Dispatcher::run($arr + [
                    function ($request, $next) {
                        return $next->handle($request);
                    },
                ]);
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function doMiddleWares($uri = null)
    {
        try {
            if ($uri) {
                $route_middlewares = \array_reverse(self::$uriToMiddlewares[$uri]);
//                var_dump($route_middlewares);
                foreach ($route_middlewares as $class_name) {
                    $this->middleware($class_name);
                }
            }
        } catch (Exception $e) {
            return di()->get(Response::class)->end(
                Json::encode(['msg' => $e->getMessage(), 'code' => 11211])
            );
        }
    }
    /**
     * @param $request
     * @param $response
     * @return mixed|void
     * @throws \Exception
     */
    public function dispatch($request, $response)
    {
        $container = ApplicationContext::getContainer();
        $method = $request->server['request_method'] ?? 'GET';
        $uri = $request->server['request_uri'] ?? '/';
        $routeInfo = self::$dispatcher->dispatch($method, $uri);

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                return $this->defaultRouter($request, $response, $uri);
            case Dispatcher::METHOD_NOT_ALLOWED:
                $response->status(405);
                return $response->end();
            case Dispatcher::FOUND:
                $this->doMiddleWares($uri);
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                if (is_string($handler)) {
                    $handler = explode('@', $handler);
                    if (count($handler) != 2) {
                        throw new RuntimeException("Route {$uri} config error, Only @ are supported");
                    }

                    $className = $handler[0];
                    $func = $handler[1];

                    if (!class_exists($className)) {
                        throw new RuntimeException("Route {$uri} defined '{$className}' Class Not Found");
                    }

                    $method = $func;
//                    $controller = new $className();
                    try {
                        $controller = $container->get($className);

                        if (!method_exists($controller, $func)) {
                            throw new RuntimeException("Route {$uri} defined '{$func}' Method Not Found");
                        }

                        $middlewareHandler = function ($request, $response, $vars) use ($controller, $func) {
                            $data = $controller->{$func}($request, $response, $vars ?? null);

                            if (is_string($data) || is_int($data) || is_bool($data)) {
                                $data = (string) $data;
                                $data = ApplicationContext::getContainer()->get(Response::class)->end($data);
                            }
                            if ($data instanceof Collection) {
                                $data = $data->toArray();
                            }
                            if ($data instanceof \Illuminate\Support\Collection) {
                                $data = $data->toArray();
                            }
                            if (is_array($data)) {
                                $data = ApplicationContext::getContainer()->get(Response::class)->end($data);
                            }
                            if (is_object($data)) {
                                $data = (array) $data;
                                $data = ApplicationContext::getContainer()->get(Response::class)->end($data);
                            }
                            return $data;
                        };
                        $middleware = 'middleware';
                        //获取到 中间件

                        if (property_exists($controller, $middleware)) {
                            $classMiddlewares = $controller->{$middleware}['__construct'] ?? [];
                            $methodMiddlewares = $controller->{$middleware}[$func] ?? [];
                            $middlewares = array_merge($classMiddlewares, $methodMiddlewares);
                            if ($middlewares) {
                                $middlewareHandler = $this->packMiddleware($middlewareHandler, array_reverse($middlewares));
                            }
                        }

                        return $middlewareHandler($request, $response, $vars ?? null);
                    } catch (\Exception $e) {
                        return $response->end(
                            Json::encode(['msg' => $e->getMessage(), 'code' => 11211])
                        );
                    }
                }

                if (is_callable($handler)) {
                    return call_user_func_array($handler, [$request, $response, $vars ?? null]);
                }

                throw new RuntimeException("Route {$uri} config error");
            default:
                $response->status(400);
                return $response->end();
        }
    }

    /**
     * @param $request
     * @param $response
     * @param $uri
     */
    public function defaultRouter($request, $response, $uri)
    {
        $uri = trim($uri, '/');
        $uri = explode('/', $uri);

        if ($uri[0] === '') {
            $className = '\\App\\Controller\\IndexController';
            if (class_exists($className) && method_exists($className, 'index')) {
                (new $className())->index($request, $response);
            }
        }
        $response->status(404);
        return $response->end();
    }

    /**
     * @param $handler
     * @param array $middlewares
     * @return mixed
     */
    public function packMiddleware($handler, $middlewares = [])
    {
        foreach ($middlewares as $middleware) {
            $handler = $middleware($handler);
        }
        return $handler;
    }
}
