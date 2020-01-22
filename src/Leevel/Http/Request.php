<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Http;

use ArrayAccess;
use Leevel\Support\IArray;
use Symfony\Component\HttpFoundation\Request as BaseRequest;

/**
 * HTTP 请求.
 */
class Request extends BaseRequest implements IArray, ArrayAccess
{
    /**
     * 请求方法伪装.
     *
     * @var string
     */
    const VAR_METHOD = '_method';

    /**
     * AJAX 伪装.
     *
     * @var string
     */
    const VAR_AJAX = '_ajax';

    /**
     * PJAX 伪装.
     *
     * @var string
     */
    const VAR_PJAX = '_pjax';

    /**
     * JSON 伪装.
     *
     * @var string
     */
    const VAR_JSON = '_json';

    /**
     * 接受 JSON 伪装.
     *
     * @var string
     */
    const VAR_ACCEPT_JSON = '_acceptjson';

    /**
     * 是否存在输入值.
     */
    public function __isset(string $key): bool
    {
        return null !== $this->__get($key);
    }

    /**
     * 获取输入值.
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->get($key);
    }

    /**
     * 是否处于协程上下文.
     */
    public static function coroutineContext(): bool
    {
        return true;
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
     * 获取输入和文件.
     */
    public function all(): array
    {
        return $this->getInputSource()->all() +
            $this->query->all() +
            $this->files->all();
    }

    /**
     * 获取输入数据.
     *
     * @param null|array|string $defaults
     *
     * @return mixed
     */
    public function input(?string $key = null, $defaults = null)
    {
        $input = $this->all();
        if (null === $key) {
            return $input;
        }

        return $input[$key] ?? $defaults;
    }

    /**
     * 是否为 PHP 运行模式命令行, 兼容 Swoole HTTP Service.
     *
     * - Swoole HTTP 服务器也以命令行运行.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     */
    public function isCli(): bool
    {
        if ('swoole-http-server' === $this->server->get('SERVER_SOFTWARE')) {
            return false;
        }

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
     * 是否为 JSON 请求行为.
     */
    public function isJson(): bool
    {
        $field = static::VAR_JSON;
        if ($this->request->has($field) || $this->query->has($field)) {
            return true;
        }

        return $this->isRealJson();
    }

    /**
     * 是否为 JSON 请求行为真实.
     */
    public function isRealJson(): bool
    {
        $contentType = $this->headers->get('CONTENT_TYPE');
        if (!$contentType) {
            return false;
        }

        foreach (['/json', '+json'] as $item) {
            if (false !== strpos($contentType, $item)) {
                return true;
            }
        }

        return false;
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

        if (false !== strpos($accept, '*')) {
            return true;
        }

        return false;
    }

    /**
     * 获取入口文件.
     */
    public function getEnter(): string
    {
        if ($this->isCli()) {
            return '';
        }

        return dirname($this->getScriptName());
    }

    /**
     * 设置 pathInfo.
     *
     * @return \Leevel\Http\Request
     */
    public function setPathInfo(string $pathInfo): self
    {
        $this->pathInfo = $pathInfo;

        return $this;
    }

    /**
     * 对象转数组.
     */
    public function toArray(): array
    {
        return $this->all();
    }

    /**
     * 实现 ArrayAccess::offsetExists.
     *
     * @param mixed $index
     */
    public function offsetExists($index): bool
    {
        return array_key_exists($index, $this->all());
    }

    /**
     * 实现 ArrayAccess::offsetGet.
     *
     * @param mixed $index
     *
     * @return mixed
     */
    public function offsetGet($index)
    {
        $all = $this->all();

        return $all[$index] ?? null;
    }

    /**
     * 实现 ArrayAccess::offsetSet.
     *
     * @param mixed $index
     * @param mixed $newval
     */
    public function offsetSet($index, $newval): void
    {
        $this->getInputSource()->set($index, $newval);
    }

    /**
     * 实现 ArrayAccess::offsetUnset.
     *
     * @param string $index
     */
    public function offsetUnset($index): void
    {
        $this->getInputSource()->remove($index);
    }

    /**
     * 取得请求输入源.
     */
    protected function getInputSource(): object
    {
        return static::METHOD_GET === $this->getMethod() ? $this->query : $this->request;
    }
}
