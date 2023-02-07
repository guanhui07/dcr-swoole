<?php

declare(strict_types=1);

namespace DcrSwoole\Engine;

use DcrSwoole\Engine\Exception\CoroutineDestroyedException;
use DcrSwoole\Engine\Exception\RunningInNonCoroutineException;
use RuntimeException;
use Swoole\Coroutine as SwooleCo;

/**
 * 参考的hyperf
 * Class Coroutine
 * @package DcrSwoole\Engine
 */
class Coroutine
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @var int
     */
    private $id;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public static function create(callable $callable, ...$data)
    {
        $coroutine = new static($callable);
        $coroutine->execute(...$data);
        return $coroutine;
    }

    public function execute(...$data)
    {
        $this->id = SwooleCo::create($this->callable, ...$data);
        return $this;
    }

    public function getId()
    {
        if (is_null($this->id)) {
            throw new RuntimeException('Coroutine was not be executed.');
        }
        return $this->id;
    }

    public static function id(): int
    {
        return \Swoole\Coroutine::getCid();
    }

    public static function pid(?int $id = null)
    {
        if ($id) {
            $cid = SwooleCo::getPcid($id);
            if ($cid === false) {
                throw new CoroutineDestroyedException(sprintf('Coroutine #%d has been destroyed.', $id));
            }
        } else {
            $cid = SwooleCo::getPcid();
        }
        if ($cid === false) {
            throw new RunningInNonCoroutineException('Non-Coroutine environment don\'t has parent coroutine id.');
        }
        return max(0, $cid);
    }

    public static function set(array $config)
    {
        SwooleCo::set($config);
    }

    /**
     * @return null|\ArrayObject
     */
    public static function getContextFor(?int $id = null)
    {
        if ($id === null) {
            return SwooleCo::getContext();
        }

        return SwooleCo::getContext($id);
    }

    public static function defer(callable $callable): void
    {
        SwooleCo::defer($callable);
    }
}
