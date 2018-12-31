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
     * @param array  $query
     * @param array  $request
     * @param array  $params
     * @param array  $cookies
     * @param array  $files
     * @param array  $server
     * @param string $content
     */
    public function reset(array $query = [], array $request = [], array $params = [], array $cookies = [], array $files = [], array $server = [], $content = null);

    /**
     * 全局变量创建一个 Request.
     *
     * @return static
     */
    public static function createFromGlobals();

    /**
     * 格式化请求的内容.
     *
     * @param \Leevel\Http\IRequest $request
     *
     * @return \Leevel\Http\Request
     */
    public static function normalizeRequestFromContent(self $request);

    /**
     * 获取参数.
     *
     * @param string $key
     * @param mixed  $defaults
     *
     * @return mixed
     */
    public function get($key, $defaults = null);

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
    public function input($key = null, $defaults = null);

    /**
     * 取回 query.
     *
     * @param string            $key
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function query($key = null, $defaults = null);

    /**
     * 请求是否存在 COOKIE.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasCookie($key);

    /**
     * 取回 cookie.
     *
     * @param string            $key
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function cookie($key = null, $defaults = null);

    /**
     * 取得所有文件.
     *
     * @return array
     */
    public function allFiles();

    /**
     * 获取文件
     * 数组文件请在末尾加上反斜杆访问.
     *
     * @param string $key
     * @param mixed  $defaults
     *
     * @return null|array|\Leevel\Http\UploadedFile
     */
    public function file($key = null, $defaults = null);

    /**
     * 文件是否存在已上传的文件
     * 数组文件请在末尾加上反斜杆访问.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasFile($key);

    /**
     * 验证是否为文件实例.
     *
     * @param mixed $file
     *
     * @return bool
     */
    public function isValidFile($file);

    /**
     * 取回 header.
     *
     * @param string            $key
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function header($key = null, $defaults = null);

    /**
     * 取回 server.
     *
     * @param string            $key
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function server($key = null, $defaults = null);

    /**
     * 取回数据项.
     *
     * @param string            $source
     * @param string            $key
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function getItem($source, $key, $defaults);

    /**
     * 合并输入.
     *
     * @param array $input
     */
    public function merge(array $input);

    /**
     * 替换输入.
     *
     * @param array $input
     */
    public function replace(array $input);

    /**
     * PHP 运行模式命令行, 兼容 swoole http service
     * Swoole http 服务器也以命令行运行.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     *
     * @return bool
     */
    public function isCli();

    /**
     * PHP 运行模式命令行.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     *
     * @return bool
     */
    public function isRealCli();

    /**
     * PHP 运行模式 cgi.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     *
     * @return bool
     */
    public function isCgi();

    /**
     * 是否为 Ajax 请求行为.
     *
     * @return bool
     */
    public function isAjax();

    /**
     * 是否为 Ajax 请求行为真实.
     *
     * @return bool
     */
    public function isRealAjax();

    /**
     * 是否为 Ajax 请求行为真实.
     *
     * @return bool
     */
    public function isXmlHttpRequest();

    /**
     * 是否为 Pjax 请求行为.
     *
     * @return bool
     */
    public function isPjax();

    /**
     * 是否为 Pjax 请求行为真实.
     *
     * @return bool
     */
    public function isRealPjax();

    /**
     * 是否为 json 请求行为.
     *
     * @return bool
     */
    public function isJson();

    /**
     * 是否为 json 请求行为真实.
     *
     * @return bool
     */
    public function isRealJson();

    /**
     * 是否为接受 json 请求
     *
     * @return bool
     */
    public function isAcceptJson();

    /**
     * 是否为接受 json 请求真实.
     *
     * @return bool
     */
    public function isRealAcceptJson();

    /**
     * 是否为接受任何请求
     *
     * @return bool
     */
    public function isAcceptAny();

    /**
     * 是否为 HEAD 请求行为.
     *
     * @return bool
     */
    public function isHead();

    /**
     * 是否为 GET 请求行为.
     *
     * @return bool
     */
    public function isGet();

    /**
     * 是否为 POST 请求行为.
     *
     * @return bool
     */
    public function isPost();

    /**
     * 是否为 PUT 请求行为.
     *
     * @return bool
     */
    public function isPut();

    /**
     * 是否为 PATCH 请求行为.
     *
     * @return bool
     */
    public function isPatch();

    /**
     * 是否为 PURGE 请求行为.
     *
     * @return bool
     */
    public function isPurge();

    /**
     * 是否为 OPTIONS 请求行为.
     *
     * @return bool
     */
    public function isOptions();

    /**
     * 是否为 TRACE 请求行为.
     *
     * @return bool
     */
    public function isTrace();

    /**
     * 是否为 CONNECT 请求行为.
     *
     * @return bool
     */
    public function isConnect();

    /**
     * 获取 IP 地址
     *
     * @return string
     */
    public function getClientIp();

    /**
     * 请求类型.
     *
     * @return string
     */
    public function getMethod();

    /**
     * 设置请求类型.
     *
     * @param string $method
     *
     * @return $this
     */
    public function setMethod(string $method);

    /**
     * 实际请求类型.
     *
     * @return string
     */
    public function getRealMethod();

    /**
     * 验证是否为指定的方法.
     *
     * @param string $method
     *
     * @return bool
     */
    public function isMethod($method);

    /**
     * 返回当前的语言
     *
     * @return null|string
     */
    public function language();

    /**
     * 返回当前的语言
     *
     * @return null|string
     */
    public function getLanguage();

    /**
     * 设置当前的语言
     *
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage($language);

    /**
     * 取得请求内容.
     *
     * @return resource|string
     */
    public function getContent();

    /**
     * 返回 root URL.
     *
     * @return string
     */
    public function getRoot();

    /**
     * 返回入口文件.
     *
     * @return string
     */
    public function getEnter();

    /**
     * 取得脚本名字.
     *
     * @return string
     */
    public function getScriptName();

    /**
     * 是否启用 https.
     *
     * @return bool
     */
    public function isSecure();

    /**
     * 取得 http host.
     *
     * @return string
     */
    public function getHttpHost();

    /**
     * 获取 host.
     *
     * @return string
     */
    public function getHost();

    /**
     * 取得 Scheme 和 Host.
     *
     * @return string
     */
    public function getSchemeAndHttpHost();

    /**
     * 返回当前 URL 地址
     *
     * @return string
     */
    public function getUri();

    /**
     * 服务器端口.
     *
     * @return int
     */
    public function getPort();

    /**
     * 返回 scheme.
     *
     * @return string
     */
    public function getScheme();

    /**
     * 取回查询参数.
     *
     * @return null|string
     */
    public function getQueryString();

    /**
     * 设置 pathInfo.
     *
     * @param string $pathInfo
     *
     * @return $this
     */
    public function setPathInfo($pathInfo);

    /**
     * pathInfo 兼容性分析.
     *
     * @return string
     */
    public function getPathInfo();

    /**
     * 获取基础路径.
     *
     * @return string
     */
    public function getBasePath();

    /**
     * 分析基础 url.
     *
     * @return string
     */
    public function getBaseUrl();

    /**
     * 请求参数.
     *
     * @return string
     */
    public function getRequestUri();
}
