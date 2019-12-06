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
    const VAR_METHOD = ':method';

    /**
     * AJAX 伪装.
     *
     * @var string
     */
    const VAR_AJAX = ':ajax';

    /**
     * PJAX 伪装.
     *
     * @var string
     */
    const VAR_PJAX = ':pjax';

    /**
     * JSON 伪装.
     *
     * @var string
     */
    const VAR_JSON = ':json';

    /**
     * 接受 JSON 伪装.
     *
     * @var string
     */
    const VAR_ACCEPT_JSON = ':acceptjson';

    /**
     * 是否处于协程上下文.
     */
    public static function coroutineContext(): bool;

    /**
     * 重置或者初始化.
     *
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
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    public function get(string $key, $defaults = null);

    /**
     * 请求是否包含给定的 keys.
     */
    public function exists(array $keys): bool;

    /**
     * 请求是否包含非空.
     */
    public function has(array $keys): bool;

    /**
     * 取得给定的 keys 数据.
     */
    public function only(array $keys): array;

    /**
     * 取得排除给定的 keys 数据.
     */
    public function except(array $keys): array;

    /**
     * 取回输入和文件.
     */
    public function all(): array;

    /**
     * 获取输入数据.
     *
     * @param null|array|string $defaults
     *
     * @return mixed
     */
    public function input(string $key = null, $defaults = null);

    /**
     * 取回 query.
     *
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function query(string $key = null, $defaults = null);

    /**
     * 请求是否存在 COOKIE.
     */
    public function hasCookie(string $key): bool;

    /**
     * 取回 cookie.
     *
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function cookie(string $key = null, $defaults = null);

    /**
     * 取得所有文件.
     */
    public function allFiles(): array;

    /**
     * 获取文件.
     *
     * - 数组文件请在末尾加上反斜杆访问.
     *
     * @param null|mixed $defaults
     *
     * @return null|array|\Leevel\Http\UploadedFile
     */
    public function file(?string $key = null, $defaults = null);

    /**
     * 文件是否存在已上传的文件.
     *
     * - 数组文件请在末尾加上反斜杆访问.
     */
    public function hasFile(string $key): bool;

    /**
     * 验证是否为文件实例.
     *
     * @param mixed $file
     */
    public function isValidFile($file): bool;

    /**
     * 取回 header.
     *
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function header(?string $key = null, $defaults = null);

    /**
     * 取回 server.
     *
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function server(?string $key = null, $defaults = null);

    /**
     * 取回数据项.
     *
     * @param string            $key
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function getItem(string $source, ?string $key, $defaults);

    /**
     * 合并输入.
     */
    public function merge(array $input): void;

    /**
     * 替换输入.
     */
    public function replace(array $input): void;

    /**
     * PHP 运行模式命令行, 兼容 Swoole HTTP Service.
     *
     * - Swoole HTTP 服务器也以命令行运行.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     */
    public function isCli(): bool;

    /**
     * PHP 运行模式命令行.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     */
    public function isRealCli(): bool;

    /**
     * PHP 运行模式 cgi.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     */
    public function isCgi(): bool;

    /**
     * 是否为 Ajax 请求行为.
     */
    public function isAjax(): bool;

    /**
     * 是否为 Ajax 请求行为真实.
     */
    public function isRealAjax(): bool;

    /**
     * 是否为 Ajax 请求行为真实.
     */
    public function isXmlHttpRequest(): bool;

    /**
     * 是否为 Pjax 请求行为.
     */
    public function isPjax(): bool;

    /**
     * 是否为 Pjax 请求行为真实.
     */
    public function isRealPjax(): bool;

    /**
     * 是否为 json 请求行为.
     */
    public function isJson(): bool;

    /**
     * 是否为 json 请求行为真实.
     */
    public function isRealJson(): bool;

    /**
     * 是否为接受 json 请求.
     */
    public function isAcceptJson(): bool;

    /**
     * 是否为接受 json 请求真实.
     */
    public function isRealAcceptJson(): bool;

    /**
     * 是否为接受任何请求.
     */
    public function isAcceptAny(): bool;

    /**
     * 是否为 HEAD 请求行为.
     */
    public function isHead(): bool;

    /**
     * 是否为 GET 请求行为.
     */
    public function isGet(): bool;

    /**
     * 是否为 POST 请求行为.
     */
    public function isPost(): bool;

    /**
     * 是否为 PUT 请求行为.
     */
    public function isPut(): bool;

    /**
     * 是否为 PATCH 请求行为.
     */
    public function isPatch(): bool;

    /**
     * 是否为 PURGE 请求行为.
     */
    public function isPurge(): bool;

    /**
     * 是否为 OPTIONS 请求行为.
     */
    public function isOptions(): bool;

    /**
     * 是否为 TRACE 请求行为.
     */
    public function isTrace(): bool;

    /**
     * 是否为 CONNECT 请求行为.
     */
    public function isConnect(): bool;

    /**
     * 获取 IP 地址.
     */
    public function getClientIp(): string;

    /**
     * 请求类型.
     */
    public function getMethod(): string;

    /**
     * 设置请求类型.
     *
     * @return \Leevel\Http\IRequest
     */
    public function setMethod(string $method): self;

    /**
     * 实际请求类型.
     */
    public function getRealMethod(): string;

    /**
     * 验证是否为指定的方法.
     */
    public function isMethod(string $method): bool;

    /**
     * 返回当前的语言.
     */
    public function language(): ?string;

    /**
     * 返回当前的语言.
     */
    public function getLanguage(): ?string;

    /**
     * 设置当前的语言.
     *
     * @return \Leevel\Http\IRequest
     */
    public function setLanguage(string $language): self;

    /**
     * 取得请求内容.
     */
    public function getContent(): string;

    /**
     * 返回 root URL.
     */
    public function getRoot(): string;

    /**
     * 返回入口文件.
     */
    public function getEnter(): string;

    /**
     * 取得脚本名字.
     */
    public function getScriptName(): string;

    /**
     * 是否启用 https.
     */
    public function isSecure(): bool;

    /**
     * 取得 http host.
     */
    public function getHttpHost(): string;

    /**
     * 获取 host.
     */
    public function getHost(): string;

    /**
     * 取得 Scheme 和 Host.
     */
    public function getSchemeAndHttpHost(): string;

    /**
     * 返回当前 URL 地址.
     */
    public function getUri(): string;

    /**
     * 服务器端口.
     */
    public function getPort(): int;

    /**
     * 返回 scheme.
     */
    public function getScheme(): string;

    /**
     * 取回查询参数.
     */
    public function getQueryString(): ?string;

    /**
     * 设置 pathInfo.
     *
     * @return \Leevel\Http\IRequest
     */
    public function setPathInfo(string $pathInfo): self;

    /**
     * pathInfo 兼容性分析.
     */
    public function getPathInfo(): string;

    /**
     * 获取基础路径.
     */
    public function getBasePath(): string;

    /**
     * 分析基础 url.
     */
    public function getBaseUrl(): string;

    /**
     * 请求参数.
     */
    public function getRequestUri(): string;
}
