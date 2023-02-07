<?php

declare(strict_types=1);

namespace DcrSwoole\Route;


use DcrSwoole\Framework\Router;
use function array_merge;
use function count;
use function preg_replace_callback;
use function str_replace;

/**
 * Class Route
 * @see 参考webman-framework 的 Route/Route
 */
class Route
{
    /**
     * @var string|null
     */
    protected ?string $name = null;

    /**
     * @var array
     */
    protected array $methods = [];

    /**
     * @var string
     */
    protected string $path = '';

    /**
     * @var callable
     */
    protected $callback = null;

    /**
     * @var array
     */
    protected array $middlewares = [];

    /**
     * @var array
     */
    protected array $params = [];

    /**
     * Route constructor.
     * @param array $methods
     * @param string $path
     * @param callable $callback
     */
    public function __construct($methods, string $path, $callback)
    {
        $this->methods = (array)$methods;
        $this->path = $path;
        $this->callback = $callback;
    }

    /**
     * Get name.
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name ?? null;
    }

    /**
     * Name.
     * @param string $name
     * @return $this
     */
    public function name(string $name): Route
    {
        $this->name = $name;
        Router::setByName($name, $this);
        return $this;
    }

    /**
     * Middleware.
     * @param mixed $middleware
     * @return $this|array
     */
    public function middleware($middleware = null)
    {
        if ($middleware === null) {
            return $this->middlewares;
        }
        $this->middlewares = array_merge($this->middlewares, is_array($middleware) ? $middleware : [$middleware]);
        return $this;
    }

    /**
     * GetPath.
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * GetMethods.
     * @return array
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * GetCallback.
     * @return callable|null
     */
    public function getCallback()//:?callable fix: null|callable|array
    {
        return $this->callback;
    }

    /**
     * GetMiddleware.
     * @return array
     */
    public function getMiddleware(): array
    {
        return $this->middlewares;
    }

    /**
     * Param.
     * @param string|null $name
     * @param $default
     * @return array|mixed|null
     */
    public function param(string $name = null, $default = null)
    {
        if ($name === null) {
            return $this->params;
        }
        return $this->params[$name] ?? $default;
    }

    /**
     * SetParams.
     * @param array $params
     * @return $this
     */
    public function setParams(array $params): Route
    {
        $this->params = array_merge($this->params, $params);
        return $this;
    }

    /**
     * Url.
     * @param array $parameters
     * @return string
     */
    public function url(array $parameters = []): string
    {
        if (empty($parameters)) {
            return $this->path;
        }
        $path = str_replace(['[', ']'], '', $this->path);
        $path = preg_replace_callback('/\{(.*?)(?:\:[^\}]*?)*?\}/', function ($matches) use (&$parameters) {
            if (!$parameters) {
                return $matches[0];
            }
            if (isset($parameters[$matches[1]])) {
                $value = $parameters[$matches[1]];
                unset($parameters[$matches[1]]);
                return $value;
            }
            $key = key($parameters);
            if (is_int($key)) {
                $value = $parameters[$key];
                unset($parameters[$key]);
                return $value;
            }
            return $matches[0];
        }, $path);
        return count($parameters) > 0 ? $path . '?' . http_build_query($parameters) : $path;
    }
}
