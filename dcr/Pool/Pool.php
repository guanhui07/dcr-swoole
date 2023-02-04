<?php

namespace  DcrSwoole\Pool;

use DcrSwoole\Contract\ConnectionPoolInterface;
use DcrSwoole\Contract\PoolInterface;
use DcrSwoole\Contract\PoolOptionInterface;
use DcrSwoole\Utils\Queue;
use RuntimeException;
use Throwable;

/**
 * Class Pool
 * @package Raylin666\Pool
 * 参考的 Raylin666\Pool
 */
abstract class Pool implements PoolInterface
{
    /**
     * 连接池名称
     * @var string
     */
    protected $name;

    /**
     * 连接池队列
     * @var Queue
     */
    protected $queue;

    /**
     * 连接池基础配置
     * @var PoolConfig
     */
    protected $config;

    /**
     * 连接回调
     * @var callable
     */
    protected $connectionCallback;

    /**
     * 连接池选项配置
     * @var PoolOptionInterface
     */
    protected $option;

    /**
     * 当前的连接数量
     * @var int
     */
    protected $currentConnections = 0;

    /**
     * Pool constructor.
     * @param PoolConfig $config
     */
    public function __construct(PoolConfig $config)
    {
        $this->config = $config;

        $this->name = $config->getName();

        $this->initPoolOption($config->getPoolOption());

        $this->connectionCallback = $config->getConnectionCallback();

        $this->queue = make(
            Queue::class,
            [
                'capacity' => $this->option->getMaxConnections()
            ]
        );

        $this->initConnection();
    }

    /**
     * @return ConnectionPoolInterface
     * @throws Throwable
     */
    public function get(): ConnectionPoolInterface
    {
        // TODO: Implement get() method.

        $num = $this->getConnectionsNum();

        try {
            if ($num === 0 && $this->currentConnections < $this->option->getMaxConnections()) {
                ++$this->currentConnections;
                return $this->createConnection();
            }
        } catch (Throwable $e) {
            --$this->currentConnections;
            throw $e;
        }

        try {
            $connection = $this->queue->pop($this->option->getWaitTimeout());
        } catch (RuntimeException $e) {
            // 非协程环境下, Queue 失败处理, 手动执行等待
            if ($e->getMessage() == "Can't shift from an empty datastructure") {
                sleep($this->option->getWaitTimeout());
                $connection = $this->queue->pop($this->option->getWaitTimeout());
            }
        }

        if (! $connection instanceof ConnectionPoolInterface) {
            throw new RuntimeException('Connection pool exhausted. Cannot establish new connection before wait_timeout.');
        }

        return $connection;
    }

    /**
     * @param ConnectionPoolInterface $connectionPool
     */
    public function release(ConnectionPoolInterface $connectionPool): void
    {
        // TODO: Implement release() method.

        $this->queue->push($connectionPool);
    }

    /**
     * @return PoolOptionInterface
     */
    public function getOption(): PoolOptionInterface
    {
        // TODO: Implement getOption() method.

        return $this->option;
    }

    /**
     * flush queue connections pool
     */
    public function flush(): void
    {
        // TODO: Implement flush() method.

        $num = $this->getConnectionsNum();

        if ($num > 0) {
            while ($this->currentConnections > $this->option->getMinConnections() && $conn = $this->queue->pop(0.001)) {
                try {
                    $conn->close();
                } catch (Throwable $e) {
                    // ...
                } finally {
                    --$this->currentConnections;
                    --$num;
                }

                if ($num <= 0) {
                    // Ignore connections queued during flushing.
                    break;
                }
            }
        }
    }

    /**
     * @param bool $must
     */
    public function flushOne(bool $must = false): void
    {
        $num = $this->getConnectionsNum();

        if ($num > 0 && $conn = $this->queue->pop(0.001)) {
            if ($must || ! $conn->check()) {
                try {
                    $conn->close();
                } catch (Throwable $e) {
                    // ...
                } finally {
                    --$this->currentConnections;
                }
            } else {
                $this->release($conn);
            }
        }
    }

    /**
     * @return ConnectionPoolInterface
     */
    abstract protected function createConnection(): ConnectionPoolInterface;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Queue
     */
    public function getQueue(): Queue
    {
        return $this->queue;
    }

    /**
     * @return PoolConfig
     */
    public function getConfig(): PoolConfig
    {
        return $this->config;
    }

    /**
     * @return callable
     */
    public function getConnectionCallback(): callable
    {
        return $this->connectionCallback;
    }

    /**
     * @return int
     */
    public function getCurrentConnections(): int
    {
        return $this->currentConnections;
    }

    /**
     * @return int
     */
    public function getConnectionsNum(): int
    {
        return $this->queue->length();
    }

    /**
     * 初始化连接池配置
     * @param PoolOptionInterface|null $poolOption
     */
    protected function initPoolOption(?PoolOptionInterface $poolOption = null): void
    {
        if (! $poolOption instanceof PoolOptionInterface) {
            $poolOption = make(
                PoolOption::class
            );
        }

        /** @var $poolOption PoolOption */
        if ($poolOption->getMinConnections() > $poolOption->getMaxConnections()) {
            $poolOption->withMinConnections($poolOption->getMaxConnections() - 1);
        }

        $this->option = $poolOption;
    }

    /**
     * 初始化(预创建)连接池
     */
    protected function initConnection()
    {
        if ($this->getConnectionsNum() === 0 && $this->currentConnections === 0) {
            for ($i = $this->option->getMinConnections(); $i--;) {
                ++$this->currentConnections;
                $this->release($this->createConnection());
            }
        }
    }
}
