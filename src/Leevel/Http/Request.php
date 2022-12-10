<?php

declare(strict_types=1);

namespace Leevel\Http;

use Leevel\Support\IArray;
use Symfony\Component\HttpFoundation\Request as BaseRequest;

/**
 * HTTP 请求.
 */
class Request extends BaseRequest implements IArray
{
    /**
     * 请求方法伪装.
     */
    public const VAR_METHOD = '_method';

    /**
     * AJAX 伪装.
     */
    public const VAR_AJAX = '_ajax';

    /**
     * PJAX 伪装.
     */
    public const VAR_PJAX = '_pjax';

    /**
     * 接受 JSON 伪装.
     */
    public const VAR_ACCEPT_JSON = '_acceptjson';

    /**
     * 是否处于协程上下文.
     */
    public static function coroutineContext(): bool
    {
        return true;
    }

    /**
     * 从 Symfony 请求创建 Leevel 请求.
     */
    public static function createFromSymfonyRequest(BaseRequest $request): static
    {
        if ($request instanceof static) {
            return $request;
        }

        $newRequest = new static(
            $request->query->all(), $request->request->all(),
            $request->attributes->all(), $request->cookies->all(),
            $request->files->all(), $request->server->all(),
        );
        $newRequest->headers->replace($request->headers->all());
        $newRequest->content = $request->getContent();

        return $newRequest;
    }

    /**
     * 请求是否包含给定的 keys.
     */
    public function exists(array $keys): bool
    {
        $input = $this->all();
        foreach ($keys as $value) {
            if (!array_key_exists($value, $input)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 取得给定的 keys 数据.
     */
    public function only(array $keys): array
    {
        $results = [];
        $input = $this->all();
        foreach ($keys as $key) {
            $results[$key] = $input[$key] ?? null;
        }

        return $results;
    }

    /**
     * 取得排除给定的 keys 数据.
     */
    public function except(array $keys): array
    {
        $results = $this->all();
        foreach ($keys as $key) {
            if (array_key_exists($key, $results)) {
                unset($results[$key]);
            }
        }

        return $results;
    }

    /**
     * 获取所有请求参数.
     *
     * - 包含 request、query 和 attributes
     * - 优先级从高到底依次为 attributes、query 和 request，优先级高的会覆盖优先级低的参数
     */
    public function all(): array
    {
        return $this->request->all() + $this->query->all() + $this->attributes->all();
    }

    /**
     * 是否为 PHP 运行模式命令行.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     */
    public function isConsole(): bool
    {
        return $this->isRealCli();
    }

    /**
     * PHP 运行模式命令行.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     */
    public function isRealCli(): bool
    {
        return \PHP_SAPI === 'cli';
    }

    /**
     * 是否为 PHP 运行模式 cgi.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     */
    public function isCgi(): bool
    {
        return 'cgi' === substr(\PHP_SAPI, 0, 3);
    }

    /**
     * 是否为 Ajax 请求行为.
     */
    public function isAjax(): bool
    {
        $field = static::VAR_AJAX;
        if ($this->request->has($field) || $this->query->has($field)) {
            return true;
        }

        return $this->isRealAjax();
    }

    /**
     * 是否为 Ajax 请求行为真实.
     */
    public function isRealAjax(): bool
    {
        return $this->isXmlHttpRequest();
    }

    /**
     * 是否为 Pjax 请求行为.
     */
    public function isPjax(): bool
    {
        $field = static::VAR_PJAX;
        if ($this->request->has($field) || $this->query->has($field)) {
            return true;
        }

        return $this->isRealPjax();
    }

    /**
     * 是否为 Pjax 请求行为真实.
     */
    public function isRealPjax(): bool
    {
        return null !== $this->headers->get('X_PJAX');
    }

    /**
     * 是否为接受 JSON 请求.
     */
    public function isAcceptJson(): bool
    {
        $field = static::VAR_ACCEPT_JSON;
        if ($this->request->has($field) || $this->query->has($field)) {
            return true;
        }

        if ($this->isAjax() && !$this->isPjax() && $this->isAcceptAny()) {
            return true;
        }

        return $this->isRealAcceptJson();
    }

    /**
     * 是否为接受 JSON 请求真实.
     */
    public function isRealAcceptJson(): bool
    {
        $accept = $this->headers->get('ACCEPT');
        if (!$accept) {
            return false;
        }

        foreach (['/json', '+json'] as $item) {
            if (false !== strpos($accept, $item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 是否为接受任何请求.
     */
    public function isAcceptAny(): bool
    {
        $accept = $this->headers->get('ACCEPT');
        if (!$accept) {
            return true;
        }

        return false !== strpos($accept, '*');
    }

    /**
     * 获取入口文件.
     */
    public function getEnter(): string
    {
        if ($this->isConsole()) {
            return '';
        }

        return dirname($this->getScriptName());
    }

    /**
     * 设置 pathInfo.
     */
    public function setPathInfo(string $pathInfo): void
    {
        $this->pathInfo = $pathInfo;
    }

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return $this->all();
    }
}
