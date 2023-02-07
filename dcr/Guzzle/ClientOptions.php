<?php

namespace DcrSwoole\Guzzle;

use DcrSwoole\Guzzle\Contract\ClientOptionsInterface;
use GuzzleHttp\HandlerStack;

/**
 * Class ClientOptions
 */
class ClientOptions implements ClientOptionsInterface
{
    /**
     * 客户端配置
     * @var array
     */
    protected $options = [
        'handler' => null,
        'base_uri' => '',
        'allow_redirects' => [],
        'http_errors' => true,
        'decode_content' => true,
        'verify' => true,
        'cookies' => false,
        'idn_conversion' => true,
    ];

    /**
     * @param HandlerStack $handlerStack
     * @return ClientOptionsInterface
     */
    public function withHandler(HandlerStack $handlerStack): ClientOptionsInterface
    {
        // TODO: Implement withHandler() method.

        $this->options['handler'] = $handlerStack;
        return $this;
    }

    /**
     * @return HandlerStack|null
     */
    public function getHandler(): ?HandlerStack
    {
        // TODO: Implement getHandler() method.

        return $this->options['handler'];
    }

    /**
     * @param $uri
     * @return ClientOptionsInterface
     */
    public function withBaseUri($uri): ClientOptionsInterface
    {
        // TODO: Implement withBaseUri() method.

        $this->options['base_uri'] = $uri;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getBaseUri()
    {
        // TODO: Implement getBaseUri() method.

        return $this->options['base_uri'];
    }

    /**
     * @param array $settings
     * @return ClientOptionsInterface
     */
    public function withAllowRedirects(array $settings): ClientOptionsInterface
    {
        // TODO: Implement withAllowRedirects() method.

        $this->options['allow_redirects'] = $settings;
        return $this;
    }

    /**
     * @return array
     */
    public function getAllowRedirects(): array
    {
        // TODO: Implement getAllowRedirects() method.

        return $this->options['allow_redirects'];
    }

    /**
     * @param bool $is
     * @return ClientOptionsInterface
     */
    public function withHttpErrors(bool $is): ClientOptionsInterface
    {
        // TODO: Implement withHttpErrors() method.

        $this->options['http_errors'] = $is;
        return $this;
    }

    /**
     * @return bool
     */
    public function getHttpErrors(): bool
    {
        // TODO: Implement getHttpErrors() method.

        return $this->options['http_errors'];
    }

    /**
     * @param bool $is
     * @return ClientOptionsInterface
     */
    public function withDecodeContent(bool $is): ClientOptionsInterface
    {
        // TODO: Implement withDecodeContent() method.

        $this->options['decode_content'] = $is;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDecodeContent(): bool
    {
        // TODO: Implement getDecodeContent() method.

        return $this->options['decode_content'];
    }

    /**
     * @param bool $is
     * @return ClientOptionsInterface
     */
    public function withCookies(bool $is): ClientOptionsInterface
    {
        // TODO: Implement withCookies() method.

        $this->options['cookies'] = $is;
        return $this;
    }

    /**
     * @return bool
     */
    public function getCookies(): bool
    {
        // TODO: Implement getCookies() method.

        return $this->options['cookies'];
    }

    /**
     * @param bool $is
     * @return ClientOptionsInterface
     */
    public function withVerify(bool $is): ClientOptionsInterface
    {
        // TODO: Implement withVerify() method.

        $this->options['verify'] = $is;
        return $this;
    }

    /**
     * @return bool
     */
    public function getVerify(): bool
    {
        // TODO: Implement getVerify() method.

        return $this->options['verify'];
    }

    /**
     * @param bool $is
     * @return ClientOptionsInterface
     */
    public function withIdnConversion(bool $is): ClientOptionsInterface
    {
        // TODO: Implement withIdnConversion() method.

        $this->options['idn_conversion'] = $is;
        return $this;
    }

    /**
     * @return bool
     */
    public function getIdnConversion(): bool
    {
        // TODO: Implement getIdnConversion() method.

        return $this->options['idn_conversion'];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        // TODO: Implement toArray() method.

        return $this->options;
    }
}
