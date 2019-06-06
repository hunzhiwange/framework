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

namespace Leevel\Http;

/**
 * HTTP 请求接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.27
 *
 * @version 1.0
 */
interface IRequest
{
    /**
     * METHOD_HEAD.
     *
     * @var string
     */
    const METHOD_HEAD = 'HEAD';

    /**
     * METHOD_GET.
     *
     * @var string
     */
    const METHOD_GET = 'GET';

    /**
     * METHOD_POST.
     *
     * @var string
     */
    const METHOD_POST = 'POST';

    /**
     * METHOD_PUT.
     *
     * @var string
     */
    const METHOD_PUT = 'PUT';

    /**
     * METHOD_PATCH.
     *
     * @var string
     */
    const METHOD_PATCH = 'PATCH';

    /**
     * METHOD_DELETE.
     *
     * @var string
     */
    const METHOD_DELETE = 'DELETE';

    /**
     * METHOD_PURGE.
     *
     * @var string
     */
    const METHOD_PURGE = 'PURGE';

    /**
     * METHOD_OPTIONS.
     *
     * @var string
     */
    const METHOD_OPTIONS = 'OPTIONS';

    /**
     * METHOD_TRACE.
     *
     * @var string
     */
    const METHOD_TRACE = 'TRACE';

    /**
     * METHOD_CONNECT.
     *
     * @var string
     */
    const METHOD_CONNECT = 'CONNECT';

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
     * 是否处于协程上下文.
     *
     * @return bool
     */
    public static function coroutineContext(): bool;

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
    public function reset(array $query = [], array $request = [], array $params = [], array $cookies = [], array $files = [], array $server = [], $content = null): void;

    /**
     * 全局变量创建一个 Request.
     *
     * @return static
     */
    public static function createFromGlobals(): self;

    /**
     * 格式化请求的内容.
     *
     * @param \Leevel\Http\IRequest $request
     *
     * @return \Leevel\Http\IRequest
     */
    public static function normalizeRequestFromContent(self $request): self;

    /**
     * 获取参数.
     *
     * @param string $key
     * @param mixed  $defaults
     *
     * @return mixed
     */
    public function get(string $key, $defaults = null);

    /**
     * 请求是否包含给定的 keys.
     *
     * @param array $keys
     *
     * @return bool
     */
    public function exists(array $keys): bool;

    /**
     * 请求是否包含非空.
     *
     * @param array $keys
     *
     * @return bool
     */
    public function has(array $keys): bool;

    /**
     * 取得给定的 keys 数据.
     *
     * @param array $keys
     *
     * @return array
     */
    public function only(array $keys): array;

    /**
     * 取得排除给定的 keys 数据.
     *
     * @param array $keys
     *
     * @return array
     */
    public function except(array $keys): array;

    /**
     * 取回输入和文件.
     *
     * @return array
     */
    public function all(): array;

    /**
     * 获取输入数据.
     *
     * @param string            $key
     * @param null|array|string $defaults
     *
     * @return mixed
     */
    public function input(string $key = null, $defaults = null);

    /**
     * 取回 query.
     *
     * @param string            $key
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function query(string $key = null, $defaults = null);

    /**
     * 请求是否存在 COOKIE.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasCookie(string $key): bool;

    /**
     * 取回 cookie.
     *
     * @param string            $key
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function cookie(string $key = null, $defaults = null);

    /**
     * 取得所有文件.
     *
     * @return array
     */
    public function allFiles(): array;

    /**
     * 获取文件
     * 数组文件请在末尾加上反斜杆访问.
     *
     * @param string $key
     * @param mixed  $defaults
     *
     * @return null|array|\Leevel\Http\UploadedFile
     */
    public function file(?string $key = null, $defaults = null);

    /**
     * 文件是否存在已上传的文件
     * 数组文件请在末尾加上反斜杆访问.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasFile(string $key): bool;

    /**
     * 验证是否为文件实例.
     *
     * @param mixed $file
     *
     * @return bool
     */
    public function isValidFile($file): bool;

    /**
     * 取回 header.
     *
     * @param string            $key
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function header(?string $key = null, $defaults = null);

    /**
     * 取回 server.
     *
     * @param string            $key
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function server(?string $key = null, $defaults = null);

    /**
     * 取回数据项.
     *
     * @param string            $source
     * @param string            $key
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function getItem(string $source, ?string $key, $defaults);

    /**
     * 合并输入.
     *
     * @param array $input
     */
    public function merge(array $input): void;

    /**
     * 替换输入.
     *
     * @param array $input
     */
    public function replace(array $input): void;

    /**
     * PHP 运行模式命令行, 兼容 swoole http service
     * Swoole http 服务器也以命令行运行.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     *
     * @return bool
     */
    public function isCli(): bool;

    /**
     * PHP 运行模式命令行.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     *
     * @return bool
     */
    public function isRealCli(): bool;

    /**
     * PHP 运行模式 cgi.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     *
     * @return bool
     */
    public function isCgi(): bool;

    /**
     * 是否为 Ajax 请求行为.
     *
     * @return bool
     */
    public function isAjax(): bool;

    /**
     * 是否为 Ajax 请求行为真实.
     *
     * @return bool
     */
    public function isRealAjax(): bool;

    /**
     * 是否为 Ajax 请求行为真实.
     *
     * @return bool
     */
    public function isXmlHttpRequest(): bool;

    /**
     * 是否为 Pjax 请求行为.
     *
     * @return bool
     */
    public function isPjax(): bool;

    /**
     * 是否为 Pjax 请求行为真实.
     *
     * @return bool
     */
    public function isRealPjax(): bool;

    /**
     * 是否为 json 请求行为.
     *
     * @return bool
     */
    public function isJson(): bool;

    /**
     * 是否为 json 请求行为真实.
     *
     * @return bool
     */
    public function isRealJson(): bool;

    /**
     * 是否为接受 json 请求
     *
     * @return bool
     */
    public function isAcceptJson(): bool;

    /**
     * 是否为接受 json 请求真实.
     *
     * @return bool
     */
    public function isRealAcceptJson(): bool;

    /**
     * 是否为接受任何请求
     *
     * @return bool
     */
    public function isAcceptAny(): bool;

    /**
     * 是否为 HEAD 请求行为.
     *
     * @return bool
     */
    public function isHead(): bool;

    /**
     * 是否为 GET 请求行为.
     *
     * @return bool
     */
    public function isGet(): bool;

    /**
     * 是否为 POST 请求行为.
     *
     * @return bool
     */
    public function isPost(): bool;

    /**
     * 是否为 PUT 请求行为.
     *
     * @return bool
     */
    public function isPut(): bool;

    /**
     * 是否为 PATCH 请求行为.
     *
     * @return bool
     */
    public function isPatch(): bool;

    /**
     * 是否为 PURGE 请求行为.
     *
     * @return bool
     */
    public function isPurge(): bool;

    /**
     * 是否为 OPTIONS 请求行为.
     *
     * @return bool
     */
    public function isOptions(): bool;

    /**
     * 是否为 TRACE 请求行为.
     *
     * @return bool
     */
    public function isTrace(): bool;

    /**
     * 是否为 CONNECT 请求行为.
     *
     * @return bool
     */
    public function isConnect(): bool;

    /**
     * 获取 IP 地址.
     *
     * @return string
     */
    public function getClientIp(): string;

    /**
     * 请求类型.
     *
     * @return string
     */
    public function getMethod(): string;

    /**
     * 设置请求类型.
     *
     * @param string $method
     *
     * @return \Leevel\Http\IRequest
     */
    public function setMethod(string $method): self;

    /**
     * 实际请求类型.
     *
     * @return string
     */
    public function getRealMethod(): string;

    /**
     * 验证是否为指定的方法.
     *
     * @param string $method
     *
     * @return bool
     */
    public function isMethod(string $method): bool;

    /**
     * 返回当前的语言
     *
     * @return null|string
     */
    public function language(): ?string;

    /**
     * 返回当前的语言
     *
     * @return null|string
     */
    public function getLanguage(): ?string;

    /**
     * 设置当前的语言
     *
     * @param string $language
     *
     * @return \Leevel\Http\IRequest
     */
    public function setLanguage(string $language): self;

    /**
     * 取得请求内容.
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * 返回 root URL.
     *
     * @return string
     */
    public function getRoot(): string;

    /**
     * 返回入口文件.
     *
     * @return string
     */
    public function getEnter(): string;

    /**
     * 取得脚本名字.
     *
     * @return string
     */
    public function getScriptName(): string;

    /**
     * 是否启用 https.
     *
     * @return bool
     */
    public function isSecure(): bool;

    /**
     * 取得 http host.
     *
     * @return string
     */
    public function getHttpHost(): string;

    /**
     * 获取 host.
     *
     * @return string
     */
    public function getHost(): string;

    /**
     * 取得 Scheme 和 Host.
     *
     * @return string
     */
    public function getSchemeAndHttpHost(): string;

    /**
     * 返回当前 URL 地址.
     *
     * @return string
     */
    public function getUri(): string;

    /**
     * 服务器端口.
     *
     * @return int
     */
    public function getPort(): int;

    /**
     * 返回 scheme.
     *
     * @return string
     */
    public function getScheme(): string;

    /**
     * 取回查询参数.
     *
     * @return null|string
     */
    public function getQueryString(): ?string;

    /**
     * 设置 pathInfo.
     *
     * @param string $pathInfo
     *
     * @return \Leevel\Http\IRequest
     */
    public function setPathInfo(string $pathInfo): self;

    /**
     * pathInfo 兼容性分析.
     *
     * @return string
     */
    public function getPathInfo(): string;

    /**
     * 获取基础路径.
     *
     * @return string
     */
    public function getBasePath(): string;

    /**
     * 分析基础 url.
     *
     * @return string
     */
    public function getBaseUrl(): string;

    /**
     * 请求参数.
     *
     * @return string
     */
    public function getRequestUri(): ?string;
}
