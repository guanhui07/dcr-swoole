<?php

declare(strict_types=1);

namespace DcrSwoole\Server;

use App\Utils\ConfigBase;
use DcrSwoole\Crontab\CrontabBootstrap;
use DcrSwoole\Listener;
use DcrSwoole\Request\Request;
use DcrSwoole\Response\Response;
use DcrSwoole\Server\Protocol\HTTP\SimpleRoute;
use DcrSwoole\Server\Route\RouteDispatch;
use DcrSwoole\Utils\ApplicationContext;
use DcrSwoole\Utils\Context;
use DcrSwooleCrontab\Process\CrontabDispatcherProcess;
use Swoole\Http\Server;
use Swoole\Server as HttpServer;

/**
 * Class Http
 * @package DcrSwoole\Server
 * http启动
 */
class Http
{
    protected $_server;

    protected $_config;

    /** @var RouteDispatch */
    protected RouteDispatch $_route;

    public function __construct()
    {
        $config = config('servers');
        $httpConfig = $config['http'];
        $this->_config = $httpConfig;
        if (isset($httpConfig['settings']['only_simple_http'])) {
            $this->_server = new HttpServer($httpConfig['ip'], $httpConfig['port'], $config['mode']);
            $this->_server->on('workerStart', [$this, 'onSimpleWorkerStart']);
            $this->_server->on('receive', [$this, 'onReceive']);
            unset($httpConfig['settings']['only_simple_http']);
        } else {
            $this->_server = new Server(
                $httpConfig['ip'],
                $httpConfig['port'],
                $config['mode'],
                $httpConfig['sock_type']
            );
            $this->_server->on('workerStart', [$this, 'onWorkerStart']);
            $this->_server->on('request', [$this, 'onRequest']);
        }
        $this->_server->set($httpConfig['settings']);

        if ($config['mode'] === SWOOLE_BASE) {
            $this->_server->on('managerStart', [$this, 'onManagerStart']);
        } else {
            $this->_server->on('start', [$this, 'onStart']);
        }

        foreach ($httpConfig['callbacks'] as $eventKey => $callbackItem) {
            [$class, $func] = $callbackItem;
            $this->_server->on($eventKey, [$class, $func]);
        }

        $container = ApplicationContext::getContainer();
        $container->set(\Swoole\Server::class, $this->_server);

        $this->swooleInit();

        if (isset($this->_config['process']) && !empty($this->_config['process'])) {
            foreach ($this->_config['process'] as $processItem) {
                [$class, $func] = $processItem;
                $this->_server->addProcess($class::$func($this->_server));
            }
        }

        $this->_server->start();
    }

    public function onStart(HttpServer $server)
    {
        Application::echoSuccess("Swoole Http Server running：http://{$this->_config['ip']}:{$this->_config['port']}");
        Listener::getInstance()->listen('start', $server);
    }

    public function onManagerStart(HttpServer $server)
    {
        Application::echoSuccess("Swoole Http Server running：http://{$this->_config['ip']}:{$this->_config['port']}");
        Listener::getInstance()->listen('managerStart', $server);
    }

    public function onWorkerStart(HttpServer $server, int $workerId)
    {
        $this->_route = RouteDispatch::getInstance();
        Listener::getInstance()->listen('workerStart', $server, $workerId);
    }

    public function onSimpleWorkerStart(HttpServer $server, int $workerId)
    {
        $this->_route = SimpleRoute::getInstance();
        Listener::getInstance()->listen('simpleWorkerStart', $server, $workerId);
    }

    public function onRequest(\Swoole\Http\Request $request, \Swoole\Http\Response $response)
    {
        Context::set('SwRequest', $request);
        Context::set('SwResponse', $response);
        Request::setRequest();
        Response::setResponse();
        //请求时候发布路由
        $this->_route->dispatch($request, $response);
    }

    public function onReceive($server, $fd, $from_id, $data)
    {
        $this->_route->dispatch($server, $fd, $data);
    }

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    protected function swooleInit(): void
    {
        // init crontab
        di()->get(CrontabBootstrap::class)->run();
    }
}
