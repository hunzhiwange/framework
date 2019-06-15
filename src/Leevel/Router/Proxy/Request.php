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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Http\IRequest as BaseRequest;
use Leevel\Http\IRequest as IBaseRequest;

/**
 * 代理 request.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.06.10
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Request implements IRequest
{
    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 是否处于协程上下文.
     *
     * @return bool
     */
    public static function coroutineContext(): bool
    {
        return self::proxy()->coroutineContext();
    }

    /**
     * 重置或者初始化.
     *
     * @param array                $query
     * @param array                $request
     * @param array                $params
     * @param array                $cookies
     * @param array                $files
     * @param array                $server
     * @param null|resource|string $content
     */
    public static function reset(array $query = [], array $request = [], array $params = [], array $cookies = [], array $files = [], array $server = [], $content = null): void
    {
        self::proxy()->reset($query, $request, $params, $cookies, $files, $server, $content);
    }

    /**
     * 全局变量创建一个 Request.
     *
     * @return \Leevel\Http\IRequest
     */
    public static function createFromGlobals(): IBaseRequest
    {
        return self::proxy()->createFromGlobals();
    }

    /**
     * 格式化请求的内容.
     *
     * @param \Leevel\Http\IRequest $request
     *
     * @return \Leevel\Http\IRequest
     */
    public static function normalizeRequestFromContent(IBaseRequest $request): IBaseRequest
    {
        return self::proxy()->normalizeRequestFromContent($request);
    }

    /**
     * 获取参数.
     *
     * @param string     $key
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    public static function get(string $key, $defaults = null)
    {
        return self::proxy()->get($key, $defaults);
    }

    /**
     * 请求是否包含给定的 keys.
     *
     * @param array $keys
     *
     * @return bool
     */
    public static function exists(array $keys): bool
    {
        return self::proxy()->exists($keys);
    }

    /**
     * 请求是否包含非空.
     *
     * @param array $keys
     *
     * @return bool
     */
    public static function has(array $keys): bool
    {
        return self::proxy()->has($keys);
    }

    /**
     * 取得给定的 keys 数据.
     *
     * @param array $keys
     *
     * @return array
     */
    public static function only(array $keys): array
    {
        return self::proxy()->only($keys);
    }

    /**
     * 取得排除给定的 keys 数据.
     *
     * @param array $keys
     *
     * @return array
     */
    public static function except(array $keys): array
    {
        return self::proxy()->except($keys);
    }

    /**
     * 取回输入和文件.
     *
     * @return array
     */
    public static function all(): array
    {
        return self::proxy()->all();
    }

    /**
     * 获取输入数据.
     *
     * @param null|string       $key
     * @param null|array|string $defaults
     *
     * @return mixed
     */
    public static function input(string $key = null, $defaults = null)
    {
        return self::proxy()->input($key, $defaults);
    }

    /**
     * 取回 query.
     *
     * @param null|string       $key
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public static function query(string $key = null, $defaults = null)
    {
        return self::proxy()->query($key, $defaults);
    }

    /**
     * 请求是否存在 COOKIE.
     *
     * @param string $key
     *
     * @return bool
     */
    public static function hasCookie(string $key): bool
    {
        return self::proxy()->hasCookie($key);
    }

    /**
     * 取回 cookie.
     *
     * @param null|string       $key
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public static function cookie(string $key = null, $defaults = null)
    {
        return self::proxy()->cookie($key, $defaults);
    }

    /**
     * 取得所有文件.
     *
     * @return array
     */
    public static function allFiles(): array
    {
        return self::proxy()->allFiles();
    }

    /**
     * 获取文件
     * 数组文件请在末尾加上反斜杆访问.
     *
     * @param null|string $key
     * @param null|mixed  $defaults
     *
     * @return null|array|\Leevel\Http\UploadedFile
     */
    public static function file(?string $key = null, $defaults = null)
    {
        return self::proxy()->file($key, $defaults);
    }

    /**
     * 文件是否存在已上传的文件
     * 数组文件请在末尾加上反斜杆访问.
     *
     * @param string $key
     *
     * @return bool
     */
    public static function hasFile(string $key): bool
    {
        return self::proxy()->hasFile($key);
    }

    /**
     * 验证是否为文件实例.
     *
     * @param mixed $file
     *
     * @return bool
     */
    public static function isValidFile($file): bool
    {
        return self::proxy()->isValidFile($file);
    }

    /**
     * 取回 header.
     *
     * @param null|string       $key
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public static function header(?string $key = null, $defaults = null)
    {
        return self::proxy()->header($key, $defaults);
    }

    /**
     * 取回 server.
     *
     * @param null|string       $key
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public static function server(?string $key = null, $defaults = null)
    {
        return self::proxy()->server($key, $defaults);
    }

    /**
     * 取回数据项.
     *
     * @param string            $source
     * @param string            $key
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public static function getItem(string $source, ?string $key, $defaults)
    {
        return self::proxy()->getItem($source, $key, $defaults);
    }

    /**
     * 合并输入.
     *
     * @param array $input
     */
    public static function merge(array $input): void
    {
        self::proxy()->merge($input);
    }

    /**
     * 替换输入.
     *
     * @param array $input
     */
    public static function replace(array $input): void
    {
        self::proxy()->replace($input);
    }

    /**
     * PHP 运行模式命令行, 兼容 swoole http service
     * Swoole http 服务器也以命令行运行.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     *
     * @return bool
     */
    public static function isCli(): bool
    {
        return self::proxy()->isCli();
    }

    /**
     * PHP 运行模式命令行.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     *
     * @return bool
     */
    public static function isRealCli(): bool
    {
        return self::proxy()->isRealCli();
    }

    /**
     * PHP 运行模式 cgi.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     *
     * @return bool
     */
    public static function isCgi(): bool
    {
        return self::proxy()->isCgi();
    }

    /**
     * 是否为 Ajax 请求行为.
     *
     * @return bool
     */
    public static function isAjax(): bool
    {
        return self::proxy()->isAjax();
    }

    /**
     * 是否为 Ajax 请求行为真实.
     *
     * @return bool
     */
    public static function isRealAjax(): bool
    {
        return self::proxy()->isRealAjax();
    }

    /**
     * 是否为 Ajax 请求行为真实.
     *
     * @return bool
     */
    public static function isXmlHttpRequest(): bool
    {
        return self::proxy()->isXmlHttpRequest();
    }

    /**
     * 是否为 Pjax 请求行为.
     *
     * @return bool
     */
    public static function isPjax(): bool
    {
        return self::proxy()->isPjax();
    }

    /**
     * 是否为 Pjax 请求行为真实.
     *
     * @return bool
     */
    public static function isRealPjax(): bool
    {
        return self::proxy()->isRealPjax();
    }

    /**
     * 是否为 json 请求行为.
     *
     * @return bool
     */
    public static function isJson(): bool
    {
        return self::proxy()->isJson();
    }

    /**
     * 是否为 json 请求行为真实.
     *
     * @return bool
     */
    public static function isRealJson(): bool
    {
        return self::proxy()->isRealJson();
    }

    /**
     * 是否为接受 json 请求
     *
     * @return bool
     */
    public static function isAcceptJson(): bool
    {
        return self::proxy()->isAcceptJson();
    }

    /**
     * 是否为接受 json 请求真实.
     *
     * @return bool
     */
    public static function isRealAcceptJson(): bool
    {
        return self::proxy()->isRealAcceptJson();
    }

    /**
     * 是否为接受任何请求
     *
     * @return bool
     */
    public static function isAcceptAny(): bool
    {
        return self::proxy()->isAcceptAny();
    }

    /**
     * 是否为 HEAD 请求行为.
     *
     * @return bool
     */
    public static function isHead(): bool
    {
        return self::proxy()->isHead();
    }

    /**
     * 是否为 GET 请求行为.
     *
     * @return bool
     */
    public static function isGet(): bool
    {
        return self::proxy()->isGet();
    }

    /**
     * 是否为 POST 请求行为.
     *
     * @return bool
     */
    public static function isPost(): bool
    {
        return self::proxy()->isPost();
    }

    /**
     * 是否为 PUT 请求行为.
     *
     * @return bool
     */
    public static function isPut(): bool
    {
        return self::proxy()->isPut();
    }

    /**
     * 是否为 PATCH 请求行为.
     *
     * @return bool
     */
    public static function isPatch(): bool
    {
        return self::proxy()->isPatch();
    }

    /**
     * 是否为 PURGE 请求行为.
     *
     * @return bool
     */
    public static function isPurge(): bool
    {
        return self::proxy()->isPurge();
    }

    /**
     * 是否为 OPTIONS 请求行为.
     *
     * @return bool
     */
    public static function isOptions(): bool
    {
        return self::proxy()->isOptions();
    }

    /**
     * 是否为 TRACE 请求行为.
     *
     * @return bool
     */
    public static function isTrace(): bool
    {
        return self::proxy()->isTrace();
    }

    /**
     * 是否为 CONNECT 请求行为.
     *
     * @return bool
     */
    public static function isConnect(): bool
    {
        return self::proxy()->isConnect();
    }

    /**
     * 获取 IP 地址.
     *
     * @return string
     */
    public static function getClientIp(): string
    {
        return self::proxy()->getClientIp();
    }

    /**
     * 请求类型.
     *
     * @return string
     */
    public static function getMethod(): string
    {
        return self::proxy()->getMethod();
    }

    /**
     * 设置请求类型.
     *
     * @param string $method
     *
     * @return \Leevel\Http\IRequest
     */
    public static function setMethod(string $method): IBaseRequest
    {
        return self::proxy()->setMethod($method);
    }

    /**
     * 实际请求类型.
     *
     * @return string
     */
    public static function getRealMethod(): string
    {
        return self::proxy()->getRealMethod();
    }

    /**
     * 验证是否为指定的方法.
     *
     * @param string $method
     *
     * @return bool
     */
    public static function isMethod(string $method): bool
    {
        return self::proxy()->isMethod($method);
    }

    /**
     * 返回当前的语言
     *
     * @return null|string
     */
    public static function language(): ?string
    {
        return self::proxy()->language();
    }

    /**
     * 返回当前的语言
     *
     * @return null|string
     */
    public static function getLanguage(): ?string
    {
        return self::proxy()->getLanguage();
    }

    /**
     * 设置当前的语言
     *
     * @param string $language
     *
     * @return \Leevel\Http\IRequest
     */
    public static function setLanguage(string $language): IBaseRequest
    {
        return self::proxy()->setLanguage($language);
    }

    /**
     * 取得请求内容.
     *
     * @return string
     */
    public static function getContent(): string
    {
        return self::proxy()->getContent();
    }

    /**
     * 返回 root URL.
     *
     * @return string
     */
    public static function getRoot(): string
    {
        return self::proxy()->getRoot();
    }

    /**
     * 返回入口文件.
     *
     * @return string
     */
    public static function getEnter(): string
    {
        return self::proxy()->getEnter();
    }

    /**
     * 取得脚本名字.
     *
     * @return string
     */
    public static function getScriptName(): string
    {
        return self::proxy()->getScriptName();
    }

    /**
     * 是否启用 https.
     *
     * @return bool
     */
    public static function isSecure(): bool
    {
        return self::proxy()->isSecure();
    }

    /**
     * 取得 http host.
     *
     * @return string
     */
    public static function getHttpHost(): string
    {
        return self::proxy()->getHttpHost();
    }

    /**
     * 获取 host.
     *
     * @return string
     */
    public static function getHost(): string
    {
        return self::proxy()->getHost();
    }

    /**
     * 取得 Scheme 和 Host.
     *
     * @return string
     */
    public static function getSchemeAndHttpHost(): string
    {
        return self::proxy()->getSchemeAndHttpHost();
    }

    /**
     * 返回当前 URL 地址.
     *
     * @return string
     */
    public static function getUri(): string
    {
        return self::proxy()->getUri();
    }

    /**
     * 服务器端口.
     *
     * @return int
     */
    public static function getPort(): int
    {
        return self::proxy()->getPort();
    }

    /**
     * 返回 scheme.
     *
     * @return string
     */
    public static function getScheme(): string
    {
        return self::proxy()->getScheme();
    }

    /**
     * 取回查询参数.
     *
     * @return null|string
     */
    public static function getQueryString(): ?string
    {
        return self::proxy()->getQueryString();
    }

    /**
     * 设置 pathInfo.
     *
     * @param string $pathInfo
     *
     * @return \Leevel\Http\IRequest
     */
    public static function setPathInfo(string $pathInfo): IBaseRequest
    {
        return self::proxy()->setPathInfo($pathInfo);
    }

    /**
     * pathInfo 兼容性分析.
     *
     * @return string
     */
    public static function getPathInfo(): string
    {
        return self::proxy()->getPathInfo();
    }

    /**
     * 获取基础路径.
     *
     * @return string
     */
    public static function getBasePath(): string
    {
        return self::proxy()->getBasePath();
    }

    /**
     * 分析基础 url.
     *
     * @return string
     */
    public static function getBaseUrl(): string
    {
        return self::proxy()->getBaseUrl();
    }

    /**
     * 请求参数.
     *
     * @return string
     */
    public static function getRequestUri(): ?string
    {
        return self::proxy()->getRequestUri();
    }

    /**
     * 代理服务
     *
     * @return \Leevel\Http\Request
     */
    public static function proxy(): BaseRequest
    {
        return Container::singletons()->make('request');
    }
}
