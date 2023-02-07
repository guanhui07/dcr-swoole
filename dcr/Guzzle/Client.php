<?php

namespace DcrSwoole\Guzzle;

use DcrSwoole\Contract\PoolOptionInterface;
use DcrSwoole\Guzzle\Contract\ClientOptionsInterface;
use DcrSwoole\Guzzle\Contract\HandlerInterface;
use DcrSwoole\Guzzle\Contract\MiddlewareInterface;
use DcrSwoole\Guzzle\Middleware\RetryMiddleware;
use DcrSwoole\Utils\Coroutine;
use GuzzleHttp\HandlerStack;
//use Raylin666\Utils\Coroutine\Coroutine;
use GuzzleHttp\Client as GuzzleHttpClient;

/**
 * Class Client
 * @package Raylin666\Guzzle
 */
class Client
{
    /**
     * @var HandlerInterface
     */
    protected $handler;

    /**
     * 连接池配置项
     * @var PoolOptionInterface|null
     */
    protected $poolOption;

    /**
     * 中间件
     * @var array
     */
    protected $middlewares = [
        // 重试中间件 [默认开启]
        'retry' => [
            RetryMiddleware::class, [1, 10]
        ],
    ];

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $this->handler = make(CoroutineHandler::class);
    }

    /**
     * 设置 GuzzleHttp\Client 处理器
     * @param HandlerInterface $handler
     * @return Client
     */
    public function withHandler(HandlerInterface $handler): self
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * 获取 GuzzleHttp\Client 处理器
     * @return HandlerInterface
     */
    public function getHandler(): HandlerInterface
    {
        return $this->handler;
    }

    /**
     * 设置连接池配置项 [不设置将不开启连接池]
     * @param PoolOptionInterface $poolOption
     * @return Client
     */
    public function withPoolOption(PoolOptionInterface $poolOption): self
    {
        $this->poolOption = $poolOption;
        return $this;
    }

    /**
     * @return PoolOptionInterface|null
     */
    public function getPoolOption(): ?PoolOptionInterface
    {
        return $this->poolOption;
    }

    /**
     * 设置中间件 [覆盖默认中间件]
     * @param array $middlewares
     * @return Client
     */
    public function withMiddlewares(array $middlewares): self
    {
        $this->middlewares = $middlewares;
        return $this;
    }

    /**
     * @return array
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * 创建 GuzzleHttp\Client 客户端
     * @param ClientOptionsInterface|null $clientOptions
     * @return GuzzleHttpClient
     */
    public function create(?ClientOptionsInterface $clientOptions = null): GuzzleHttpClient
    {
        $stack = Coroutine::inCoroutine() ? $this->getHandlerStack() : null;

        is_null($clientOptions) && $clientOptions = new ClientOptions();

        if ($stack instanceof HandlerStack) {
            $clientOptions->withHandler($stack);
        }

        return new GuzzleHttpClient($clientOptions->toArray());
    }

    /**
     * @return HandlerStack
     */
    protected function getHandlerStack(): HandlerStack
    {
        $poolOption = $this->getPoolOption();
        // 设置了连接池配置标志着需要开启连接池模式
        $this->handler->withPool(boolval($poolOption), $poolOption);
        $stack = HandlerStack::create($this->handler);

        foreach ($this->getMiddlewares() as $key => $middleware) {
            if (is_array($middleware)) {
                [$class, $arguments] = $middleware;
                $middleware = new $class(...$arguments);
            }

            if ($middleware instanceof MiddlewareInterface) {
                $stack->push($middleware->getMiddleware(), $key);
            }
        }

        return $stack;
    }
}
