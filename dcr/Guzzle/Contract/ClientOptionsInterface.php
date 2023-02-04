<?php

namespace  DcrSwoole\Guzzle\Contract;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\RedirectMiddleware;

/**
 * Interface ClientOptionsInterface
 * @package Raylin666\Guzzle\Contract
 */
interface ClientOptionsInterface
{
    /**
     * @param HandlerStack $handlerStack
     * @return $this
     */
    public function withHandler(HandlerStack $handlerStack): self;

    /**
     * @return HandlerStack|null
     */
    public function getHandler(): ?HandlerStack;

    /**
     * @param $uri
     * @return $this
     */
    public function withBaseUri($uri): self;

    /**
     * @return mixed
     */
    public function getBaseUri();

    /**
     * @param array $settings   数据字段内容参考 RedirectMiddleware::$defaultSettings
     * @return $this
     */
    public function withAllowRedirects(array $settings): self;

    /**
     * @return array
     */
    public function getAllowRedirects(): array;

    /**
     * @param bool $is
     * @return $this
     */
    public function withHttpErrors(bool $is): self;

    /**
     * @return bool
     */
    public function getHttpErrors(): bool;

    /**
     * @param bool $is
     * @return $this
     */
    public function withDecodeContent(bool $is): self;

    /**
     * @return bool
     */
    public function getDecodeContent(): bool;

    /**
     * @param bool $is
     * @return $this
     */
    public function withVerify(bool $is): self;

    /**
     * @return bool
     */
    public function getVerify(): bool;

    /**
     * @param bool $is
     * @return $this
     */
    public function withCookies(bool $is): self;

    /**
     * @return bool
     */
    public function getCookies(): bool;

    /**
     * @param bool $is
     * @return $this
     */
    public function withIdnConversion(bool $is): self;

    /**
     * @return bool
     */
    public function getIdnConversion(): bool;

    /**
     * @return array
     */
    public function toArray(): array;
}
