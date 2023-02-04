<?php

namespace DcrSwoole\Guzzle;

use DcrSwoole\Contract\ConnectionPoolInterface;
use DcrSwoole\Contract\PoolOptionInterface;
use DcrSwoole\Guzzle\Contract\GuzzlePoolInterface;
use DcrSwoole\Guzzle\Contract\HandlerInterface;
use DcrSwoole\Guzzle\Pool\GuzzlePool;
use DcrSwoole\Pool\PoolConfig;
use DcrSwoole\Utils\Context;
use DcrSwoole\Utils\Coroutine;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\TransferStats;
use InvalidArgumentException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;

use Psr\Http\Message\UriInterface;
use Psr\Http\Message\StreamInterface;

use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Promise\FulfilledPromise;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use Swoole\Coroutine\Http\Client as SwooleCoroutineHttpClient;

use function GuzzleHttp\Psr7\stream_for;
use function GuzzleHttp\is_host_in_noproxy;

use function GuzzleHttp\Promise\rejection_for;

/**
 * Class CoroutineHandler
 * Http handler that uses Swoole Coroutine as a transport layer.
 * @package Raylin666\Guzzle
 */
class CoroutineHandler implements HandlerInterface
{
    public const SCHEME_HTTPS = 'https';

    /**
     * 连接池状态是否开启
     * @var bool
     */
    protected $isOpenPool = false;

    /**
     * 连接池配置
     * @var PoolOptionInterface|null
     */
    protected $poolOption;

    /**
     * 连接池
     * @var GuzzlePoolInterface[]|null
     */
    protected $pool = [];

    /**
     * 设置连接池
     * @param bool  $isOpen
     * @param PoolOptionInterface|null $poolOption
     * @return HandlerInterface
     */
    public function withPool(bool $isOpen, ?PoolOptionInterface $poolOption = null): HandlerInterface
    {
        // TODO: Implement withPool() method.

        $this->isOpenPool = $isOpen;
        $isOpen && $poolOption && $this->poolOption = $poolOption;
        return $this;
    }

    /**
     * 是否已开启连接池
     * @return bool
     */
    public function isOpenPool(): bool
    {
        // TODO: Implement isOpenPool() method.

        return $this->isOpenPool;
    }

    /**
     * 获取连接池配置
     * @return PoolOptionInterface|null
     */
    public function getPoolOption(): ?PoolOptionInterface
    {
        // TODO: Implement getPoolOption() method.

        $this->poolOption;
    }

    /**
     * 连接池处理器
     * @param string       $poolName
     * @param UriInterface $uri
     * @return ConnectionPoolInterface
     * @throws \Throwable
     */
    protected function poolHandler($poolName, UriInterface $uri): ConnectionPoolInterface
    {
        $pool = $this->pool[$poolName] ?? null;
        if (! $pool) {
            $poolConfig = new PoolConfig(
                $poolName,
                function () use ($uri) {
                    return $this->newSwooleCoroutineHttpClient($uri->getHost(), $uri->getPort(), $uri->getScheme() === self::SCHEME_HTTPS);
                },
                $this->poolOption
            );

            $pool = new GuzzlePool($poolConfig);
            $this->pool[$poolName] = $pool;
        }

        return $pool->get();
    }

    /**
     * @param RequestInterface $request
     * @param array            $options
     * @return FulfilledPromise|\GuzzleHttp\Promise\PromiseInterface
     */
    public function __invoke(RequestInterface $request, array $options)
    {
        $connectionPool = null;
        $uri = $request->getUri();
        $host = $uri->getHost();
        $isSSL = $uri->getScheme() === 'https';
        $query = $uri->getQuery();
        $port = $uri->getPort() ?: ($isSSL ? 443 : 80);
        $path = $uri->getPath() ?: '/';
        if ($query !== '') {
            $path .= '?' . $query;
        }

        if ($this->isOpenPool()) {
            // 连接池处理
            $poolName = $this->getPoolName($uri);
            if (Context::has($poolName)) {
                $client = Context::get($poolName);
            } else {
                $connectionPool = $this->poolHandler($poolName, $uri);
                if ($client = $connectionPool->getConnection()) {
                    Context::set($poolName, $client);
                }
            }
        } else {
            // 普通协程处理
            $client = $this->newSwooleCoroutineHttpClient($host, $port, $isSSL);
        }

        try {
            $client->setMethod($request->getMethod());
            $client->setData((string) $request->getBody());
            // 初始化Headers
            $this->initHeaders($client, $request, $options);
            // 初始化配置
            $settings = $this->getSettings($request, $options);
            // 设置客户端参数
            (! empty($settings)) && $client->set($settings);

            $ms = microtime(true);

            $this->execute($client, $path);

            $ex = $this->checkStatusCode($client, $request);
            if ($ex !== true) {
                return rejection_for($ex);
            }

            $response = $this->getResponse($client, $request, $options, microtime(true) - $ms);
        } finally {
            if ($this->isOpenPool() && $connectionPool) {
                Coroutine::defer(function () use ($connectionPool) {
                    $connectionPool->release();
                });
            }
        }

        return new FulfilledPromise($response);
    }

    /**
     * @param SwooleCoroutineHttpClient $client
     * @param                           $path
     */
    protected function execute(SwooleCoroutineHttpClient $client, $path)
    {
        $client->execute($path);
    }

    /**
     * @param      $host
     * @param      $port
     * @param bool $ssl
     * @return SwooleCoroutineHttpClient
     */
    protected function newSwooleCoroutineHttpClient($host, $port, bool $ssl = false): SwooleCoroutineHttpClient
    {
        return new SwooleCoroutineHttpClient($host, $port, $ssl);
    }

    /**
     * @param SwooleCoroutineHttpClient $client
     * @param RequestInterface          $request
     * @param                           $options
     */
    protected function initHeaders(SwooleCoroutineHttpClient $client, RequestInterface $request, $options)
    {
        $headers = [];
        foreach ($request->getHeaders() as $name => $value) {
            $headers[$name] = implode(',', $value);
        }

        $userInfo = $request->getUri()->getUserInfo();
        if ($userInfo) {
            $headers['Authorization'] = sprintf('Basic %s', base64_encode($userInfo));
        }

        $headers = $this->rewriteHeaders($headers);

        $client->setHeaders($headers);
    }

    /**
     * @param array $headers
     * @return array
     */
    protected function rewriteHeaders(array $headers): array
    {
        // Unknown reason, Content-Length will cause 400 some time.
        // Expect header is not supported by \Swoole\Coroutine\Http\Client.
        unset($headers['Content-Length'], $headers['Expect']);
        return $headers;
    }

    /**
     * @param RequestInterface $request
     * @param                  $options
     * @return array
     */
    protected function getSettings(RequestInterface $request, $options): array
    {
        $settings = [];
        if (isset($options['delay']) && $options['delay'] > 0) {
            Coroutine::sleep((float) $options['delay'] / 1000);
        }

        // 验证服务端证书
        if (isset($options['verify'])) {
            if ($options['verify'] === false) {
                $settings['ssl_verify_peer'] = false;
            } else {
                $settings['ssl_verify_peer'] = false;
                $settings['ssl_allow_self_signed'] = true;
                $settings['ssl_host_name'] = $request->getUri()->getHost();
                if (is_string($options['verify'])) {
                    // Throw an error if the file/folder/link path is not valid or doesn't exist.
                    if (! file_exists($options['verify'])) {
                        throw new InvalidArgumentException("SSL CA bundle not found: {$options['verify']}");
                    }
                    // If it's a directory or a link to a directory use CURLOPT_CAPATH.
                    // If not, it's probably a file, or a link to a file, so use CURLOPT_CAINFO.
                    if (is_dir($options['verify']) ||
                        (is_link($options['verify']) && is_dir(readlink($options['verify'])))) {
                        $settings['ssl_capath'] = $options['verify'];
                    } else {
                        $settings['ssl_cafile'] = $options['verify'];
                    }
                }
            }
        }

        // 超时
        if (isset($options['timeout']) && $options['timeout'] > 0) {
            $settings['timeout'] = $options['timeout'];
        }

        // Proxy
        if (isset($options['proxy'])) {
            $uri = null;
            if (is_array($options['proxy'])) {
                $scheme = $request->getUri()->getScheme();
                if (isset($options['proxy'][$scheme])) {
                    $host = $request->getUri()->getHost();
                    if (! isset($options['proxy']['no']) || ! is_host_in_noproxy($host, $options['proxy']['no'])) {
                        $uri = new Uri($options['proxy'][$scheme]);
                    }
                }
            } else {
                $uri = new Uri($options['proxy']);
            }

            if ($uri) {
                $settings['http_proxy_host'] = $uri->getHost();
                $settings['http_proxy_port'] = $uri->getPort();
                if ($uri->getUserInfo()) {
                    [$user, $password] = explode(':', $uri->getUserInfo());
                    $settings['http_proxy_user'] = $user;
                    $settings['http_proxy_password'] = $password;
                }
            }
        }

        // SSL KEY
        isset($options['ssl_key']) && $settings['ssl_key_file'] = $options['ssl_key'];
        isset($options['cert']) && $settings['ssl_cert_file'] = $options['cert'];
        // Swoole Setting
        if (isset($options['swoole']) && is_array($options['swoole'])) {
            $settings = array_replace($settings, $options['swoole']);
        }

        return $settings;
    }


    /**
     * @param SwooleCoroutineHttpClient $client
     * @param RequestInterface          $request
     * @param array                     $options
     * @param float                     $transferTime
     * @return Psr7\Response
     */
    protected function getResponse(SwooleCoroutineHttpClient $client, RequestInterface $request, array $options, float $transferTime)
    {
        if ($client->set_cookie_headers) {
            $client->headers['set-cookie'] = $client->set_cookie_headers;
        }

        $body = $client->body;
        if (isset($options['sink']) && is_string($options['sink'])) {
            $body = $this->createSink($body, $options['sink']);
        }

        $response = new Response(
            $client->statusCode,
            isset($client->headers) ? $client->headers : [],
            $body
        );

        if ($callback = $options[RequestOptions::ON_STATS] ?? null) {
            $stats = new TransferStats(
                $request,
                $response,
                $transferTime,
                $client->errCode,
                []
            );

            $callback($stats);
        }

        return $response;
    }

    /**
     * @param string $body
     * @return StreamInterface
     */
    protected function createStream(string $body): StreamInterface
    {
        return stream_for($body);
    }

    /**
     * @param string $body
     * @param string $sink
     * @return bool|resource|string
     */
    protected function createSink(string $body, string $sink)
    {
        if (! empty($options['stream'])) {
            return $body;
        }
        $stream = fopen($sink, 'w+');
        if ($body !== '') {
            fwrite($stream, $body);
            fseek($stream, 0);
        }

        return $stream;
    }

    /**
     * @param SwooleCoroutineHttpClient $client
     * @param                           $request
     * @return bool|ConnectException|RequestException
     */
    protected function checkStatusCode(SwooleCoroutineHttpClient $client, $request)
    {
        $statusCode = $client->statusCode;
        $errCode = $client->errCode;
        $ctx = [
            'statusCode' => $statusCode,
            'errCode' => $errCode,
        ];

        if ($statusCode === SWOOLE_HTTP_CLIENT_ESTATUS_CONNECT_FAILED) {
            return new ConnectException(sprintf('Connection failed, errCode=%s', $errCode), $request, null, $ctx);
        }

        if ($statusCode === SWOOLE_HTTP_CLIENT_ESTATUS_REQUEST_TIMEOUT) {
            return new RequestException(sprintf('Request timed out, errCode=%s', $errCode), $request, null, null, $ctx);
        }

        if ($statusCode === SWOOLE_HTTP_CLIENT_ESTATUS_SERVER_RESET) {
            return new RequestException('Server reset', $request, null, null, $ctx);
        }

        return true;
    }

    /**
     * @param UriInterface $uri
     * @return string
     */
    protected function getPoolName(UriInterface $uri)
    {
        return sprintf('guzzle.handler.%s.%d.%s', $uri->getHost(), $uri->getPort(), $uri->getScheme());
    }
}
