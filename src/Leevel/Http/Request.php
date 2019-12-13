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
use SplFileObject;

/**
 * HTTP 请求.
 *
 * - This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2016.11.19
 *
 * @version 1.0
 *
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class Request implements IRequest, IArray, ArrayAccess
{
    /**
     * GET Bag.
     *
     * @var \Leevel\Http\Bag
     */
    public Bag $query;

    /**
     * POST Bag.
     *
     * @var \Leevel\Http\Bag
     */
    public Bag $request;

    /**
     * 路由解析后的参数.
     *
     * @var \Leevel\Http\Bag
     */
    public Bag $params;

    /**
     * COOKIE Bag.
     *
     * @var \Leevel\Http\Bag
     */
    public Bag $cookies;

    /**
     * FILE Bag.
     *
     * @var \Leevel\Http\FileBag
     */
    public FileBag $files;

    /**
     * SERVER Bag.
     *
     * @var \Leevel\Http\ServerBag
     */
    public ServerBag $server;

    /**
     * HEADER Bag.
     *
     * @var \Leevel\Http\HeaderBag
     */
    public HeaderBag $headers;

    /**
     * 内容.
     *
     * @var null|false|resource|string
     */
    protected $content;

    /**
     * 基础 url.
     *
     * @var string
     */
    protected ?string $baseUrl;

    /**
     * 基础路径.
     *
     * @var string
     */
    protected ?string $basePath;

    /**
     * 请求 url.
     *
     * @var string
     */
    protected ?string $requestUri;

    /**
     * 请求类型.
     *
     * @var string
     */
    protected ?string $method;

    /**
     * pathInfo.
     *
     * @var string
     */
    protected ?string $pathInfo;

    /**
     * 当前语言.
     *
     * @var string
     */
    protected ?string $language;

    /**
     * 构造函数.
     *
     * @param null|resource|string $content
     */
    public function __construct(array $query = [], array $request = [], array $params = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        $this->reset($query, $request, $params, $cookies, $files, $server, $content);
    }

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
     * 重置或者初始化.
     *
     * @param null|resource|string $content
     */
    public function reset(array $query = [], array $request = [], array $params = [], array $cookies = [], array $files = [], array $server = [], $content = null): void
    {
        $this->query = new Bag($query);
        $this->request = new Bag($request);
        $this->params = new Bag($params);
        $this->cookies = new Bag($cookies);
        $this->files = new FileBag($files);
        $this->server = new ServerBag($server);
        $this->headers = new HeaderBag($this->server->getHeaders());
        $this->content = $content;
        $this->baseUrl = null;
        $this->requestUri = null;
        $this->method = null;
        $this->pathInfo = null;
        $this->language = null;
    }

    /**
     * 全局变量创建一个 Request.
     *
     * @return static
     */
    public static function createFromGlobals(): IRequest
    {
        $request = new static($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER, null);
        $request = static::normalizeRequestFromContent($request);

        return $request;
    }

    /**
     * 格式化请求的内容.
     *
     * @param \Leevel\Http\IRequest $request
     *
     * @return \Leevel\Http\IRequest
     */
    public static function normalizeRequestFromContent(IRequest $request): IRequest
    {
        $contentType = $request->headers->get('CONTENT_TYPE');
        $method = strtoupper($request->server->get('REQUEST_METHOD', self::METHOD_GET));

        if ($contentType) {
            if (0 === strpos($contentType, 'application/x-www-form-urlencoded') &&
                in_array($method, [
                    static::METHOD_PUT,
                    static::METHOD_DELETE,
                    static::METHOD_PATCH,
                ], true)
            ) {
                parse_str($request->getContent(), $data);
                $request->request = new Bag($data);
            } elseif (0 === strpos($contentType, 'application/json') &&
                $content = $request->getContent()) {
                $request->request = new Bag(json_decode($content, true));
            }
        }

        return $request;
    }

    /**
     * 获取参数.
     *
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    public function get(string $key, $defaults = null)
    {
        $all = $this->all();
        if (array_key_exists($key, $all)) {
            return $all[$key];
        }

        return $this->params->get($key, $defaults);
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
     * 请求是否包含非空.
     */
    public function has(array $keys): bool
    {
        foreach ($keys as $value) {
            if ($this->isEmptyString($value)) {
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
        return array_replace_recursive($this->input(), $this->allFiles());
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
        $input = $this->getInputSource()->all() + $this->query->all();
        if (null === $key) {
            return $input;
        }

        return $input[$key] ?? $defaults;
    }

    /**
     * 获取 query.
     *
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function query(?string $key = null, $defaults = null)
    {
        return $this->getItem('query', $key, $defaults);
    }

    /**
     * 请求是否存在 COOKIE.
     */
    public function hasCookie(string $key): bool
    {
        return null !== $this->cookie($key);
    }

    /**
     * 获取 cookie.
     *
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function cookie(?string $key = null, $defaults = null)
    {
        return $this->getItem('cookies', $key, $defaults);
    }

    /**
     * 取得所有文件.
     */
    public function allFiles(): array
    {
        return $this->files->all();
    }

    /**
     * 获取文件.
     *
     * - 数组文件请在末尾加上反斜杆访问.
     *
     * @param null|mixed $defaults
     *
     * @return null|array|\Leevel\Http\UploadedFile
     */
    public function file(?string $key = null, $defaults = null)
    {
        if (!$key || false === strpos($key, '\\')) {
            return $this->getItem('files', $key, $defaults);
        }

        return $this->files->getArr($key, is_array($defaults) ? $defaults : []);
    }

    /**
     * 文件是否存在已上传的文件.
     *
     * - 数组文件请在末尾加上反斜杆访问.
     */
    public function hasFile(string $key): bool
    {
        $files = $this->file($key);
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            if ($this->isValidFile($file)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 验证是否为文件实例.
     *
     * @param mixed $file
     */
    public function isValidFile($file): bool
    {
        return $file instanceof SplFileObject && '' !== $file->getPath();
    }

    /**
     * 获取 header.
     *
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function header(?string $key = null, $defaults = null)
    {
        return $this->getItem('headers', $key, $defaults);
    }

    /**
     * 获取 server.
     *
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function server(?string $key = null, $defaults = null)
    {
        return $this->getItem('server', $key, $defaults);
    }

    /**
     * 获取数据项.
     *
     * @param string            $key
     * @param null|array|string $defaults
     *
     * @return array|string
     */
    public function getItem(string $source, ?string $key, $defaults)
    {
        if (null === $key) {
            return $this->{$source}->all();
        }

        return $this->{$source}->get($key, $defaults);
    }

    /**
     * 合并输入.
     */
    public function merge(array $input): void
    {
        $this->getInputSource()->add($input);
    }

    /**
     * 替换输入.
     */
    public function replace(array $input): void
    {
        $this->getInputSource()->replace($input);
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
     * 是否为 Ajax 请求行为真实.
     */
    public function isXmlHttpRequest(): bool
    {
        return 'XMLHttpRequest' === $this->headers->get('X_REQUESTED_WITH');
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
     * 是否为 json 请求行为.
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
     * 是否为 json 请求行为真实.
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
     * 是否为接受 json 请求.
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
     * 是否为接受 json 请求真实.
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
     * 是否为 HEAD 请求行为.
     */
    public function isHead(): bool
    {
        return $this->getMethod() === static::METHOD_HEAD;
    }

    /**
     * 是否为 GET 请求行为.
     */
    public function isGet(): bool
    {
        return $this->getMethod() === static::METHOD_GET;
    }

    /**
     * 是否为 POST 请求行为.
     */
    public function isPost(): bool
    {
        return $this->getMethod() === static::METHOD_POST;
    }

    /**
     * 是否为 PUT 请求行为.
     */
    public function isPut(): bool
    {
        return $this->getMethod() === static::METHOD_PUT;
    }

    /**
     * 是否为 PATCH 请求行为.
     */
    public function isPatch(): bool
    {
        return $this->getMethod() === static::METHOD_PATCH;
    }

    /**
     * 是否为 PURGE 请求行为.
     */
    public function isPurge(): bool
    {
        return $this->getMethod() === static::METHOD_PURGE;
    }

    /**
     * 是否为 OPTIONS 请求行为.
     */
    public function isOptions(): bool
    {
        return $this->getMethod() === static::METHOD_OPTIONS;
    }

    /**
     * 是否为 TRACE 请求行为.
     */
    public function isTrace(): bool
    {
        return $this->getMethod() === static::METHOD_TRACE;
    }

    /**
     * 是否为 CONNECT 请求行为.
     */
    public function isConnect(): bool
    {
        return $this->getMethod() === static::METHOD_CONNECT;
    }

    /**
     * 获取 IP 地址.
     */
    public function getClientIp(): string
    {
        return $this->headers->get('CLIENT_IP', $this->server->get('REMOTE_ADDR', '0.0.0.0'));
    }

    /**
     * 请求类型.
     */
    public function getMethod(): string
    {
        if (null !== $this->method) {
            return $this->method;
        }

        $this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
        if ('POST' === $this->method) {
            if ($method = $this->headers->get('X-HTTP-METHOD-OVERRIDE')) {
                $this->method = strtoupper($method);
            } else {
                $field = static::VAR_METHOD;
                $this->method = strtoupper($this->request->get($field, $this->query->get($field, 'POST')));
            }
        }

        return $this->method;
    }

    /**
     * 设置请求类型.
     *
     * @return \Leevel\Http\IRequest
     */
    public function setMethod(string $method): IRequest
    {
        $this->method = null;
        $this->server->set('REQUEST_METHOD', $method);

        return $this;
    }

    /**
     * 实际请求类型.
     */
    public function getRealMethod(): string
    {
        return strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
    }

    /**
     * 是否为指定的方法.
     */
    public function isMethod(string $method): bool
    {
        return $this->getMethod() === strtoupper($method);
    }

    /**
     * 获取当前的语言.
     */
    public function language(): ?string
    {
        return $this->language;
    }

    /**
     * 获取当前的语言.
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * 设置当前的语言.
     *
     * @return \Leevel\Http\IRequest
     */
    public function setLanguage(string $language): IRequest
    {
        $this->language = $language;

        return $this;
    }

    /**
     * 取得请求内容.
     */
    public function getContent(): string
    {
        $resources = is_resource($this->content);
        if ($resources) {
            rewind($this->content);

            return stream_get_contents($this->content);
        }

        if (null === $this->content) {
            $this->content = file_get_contents('php://input');
        }

        return $this->content;
    }

    /**
     * 获取根 URL.
     */
    public function getRoot(): string
    {
        return rtrim($this->getSchemeAndHttpHost().$this->getBaseUrl(), '/');
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
     * 取得脚本名字.
     */
    public function getScriptName(): string
    {
        return $this->server->get('SCRIPT_NAME', $this->server->get('ORIG_SCRIPT_NAME', ''));
    }

    /**
     * 是否启用 https.
     */
    public function isSecure(): bool
    {
        if (in_array((string) $this->server->get('HTTPS'), ['1', 'on'], true)) {
            return true;
        }

        if (443 === (int) $this->server->get('SERVER_PORT')) {
            return true;
        }

        return false;
    }

    /**
     * 取得 http host.
     */
    public function getHttpHost(): string
    {
        $scheme = $this->getScheme();
        $port = $this->getPort();
        if (('http' === $scheme && 80 === $port) || ('https' === $scheme && 443 === $port)) {
            return $this->getHost();
        }

        return $this->getHost().':'.$port;
    }

    /**
     * 获取 host.
     */
    public function getHost(): string
    {
        $host = $this->headers->get('X_FORWARDED_HOST', $this->headers->get('HOST', ''));
        if (!$host) {
            $host = $this->server->get('SERVER_NAME', $this->server->get('SERVER_ADDR', ''));
        }

        if (false !== strpos($host, ':')) {
            list($host) = explode(':', $host);
        }

        return $host;
    }

    /**
     * 取得 Scheme 和 Host.
     */
    public function getSchemeAndHttpHost(): string
    {
        return $this->getScheme().'://'.$this->getHttpHost();
    }

    /**
     * 获取当前 URL 地址.
     */
    public function getUri(): string
    {
        if (null !== $queryString = $this->getQueryString()) {
            $queryString = '?'.$queryString;
        }

        return $this->getSchemeAndHttpHost().rtrim($this->getBaseUrl(), '/').$this->getPathInfo().$queryString;
    }

    /**
     * 服务器端口.
     */
    public function getPort(): int
    {
        $port = (int) $this->server->get('SERVER_PORT');
        if (!$port) {
            $port = 'https' === $this->getScheme() ? 443 : 80;
        }

        return $port;
    }

    /**
     * 获取 scheme.
     */
    public function getScheme(): string
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    /**
     * 获取查询参数.
     */
    public function getQueryString(): ?string
    {
        $queryString = $this->normalizeQueryString($this->server->get('QUERY_STRING'));

        return '' === $queryString && '0' !== $queryString ? null : $queryString;
    }

    /**
     * 设置 pathInfo.
     *
     * @return \Leevel\Http\IRequest
     */
    public function setPathInfo(string $pathInfo): IRequest
    {
        $this->pathInfo = $pathInfo;

        return $this;
    }

    /**
     * 获取 pathInfo.
     */
    public function getPathInfo(): string
    {
        if (null !== $this->pathInfo) {
            return $this->pathInfo;
        }

        $pathInfo = $this->server->get('PATH_INFO', '');
        if ($pathInfo) {
            return $this->pathInfo = $this->parsePathInfo($pathInfo);
        }

        if ('' === ($requestUri = $this->getRequestUri())) {
            return $this->pathInfo = $this->parsePathInfo('');
        }

        if (($pos = strpos($requestUri, '?')) > -1) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        $baseUrl = $this->getBaseUrl();
        if ('' !== $baseUrl &&
            (false === ($pathInfo = substr($requestUri, strlen($baseUrl))))) {
            $pathInfo = '';
        } elseif ('' === $baseUrl || '/' === $baseUrl) {
            $pathInfo = $requestUri;
        }

        return $this->pathInfo = $this->parsePathInfo($pathInfo);
    }

    /**
     * 获取基础路径.
     */
    public function getBasePath(): string
    {
        if (null !== $this->basePath) {
            return $this->basePath;
        }

        $baseUrl = $this->getBaseUrl();
        if (empty($baseUrl)) {
            return $this->basePath = '';
        }

        $fileName = basename($this->server->get('SCRIPT_FILENAME', ''));
        if (basename($baseUrl) === $fileName) {
            $basePath = dirname($baseUrl);
        } else {
            $basePath = $baseUrl;
        }

        if ('\\' === \DIRECTORY_SEPARATOR) {
            $basePath = str_replace('\\', '/', $basePath); // @codeCoverageIgnore
        }

        return $this->basePath = rtrim($basePath, '/');
    }

    /**
     * 获取基础 URL.
     */
    public function getBaseUrl(): string
    {
        if (null !== $this->baseUrl) {
            return $this->baseUrl;
        }

        $fileName = basename($this->server->get('SCRIPT_FILENAME', ''));
        if (basename($this->server->get('SCRIPT_NAME', '')) === $fileName) {
            $url = $this->server->get('SCRIPT_NAME');
        } elseif (basename($this->server->get('PHP_SELF', '')) === $fileName) {
            $url = $this->server->get('PHP_SELF');
        } elseif (basename($this->server->get('ORIG_SCRIPT_NAME', '')) === $fileName) {
            $url = $this->server->get('ORIG_SCRIPT_NAME');
        } else {
            $path = $this->server->get('PHP_SELF');
            $segs = explode('/', trim($fileName, '/'));
            $segs = array_reverse($segs);
            $index = 0;
            $maxCount = count($segs);
            $url = '';
            do {
                $seg = $segs[$index];
                $url = '/'.$seg.$url;
                $index++;
            } while (($maxCount > $index) && (false !== ($pos = strpos($path, $url))) && (0 !== $pos));
        }

        $url = (string) $url;
        if (!$url) {
            return $this->baseUrl = '';
        }

        $requestUri = (string) $this->getRequestUri();
        if ('' !== $requestUri && '/' !== substr($requestUri, 0, 1)) {
            $requestUri = '/'.$requestUri;
        }

        $prefix = $this->getUrlEncodedPrefix($requestUri, $url);
        if (false !== $prefix) {
            return $this->baseUrl = $prefix;
        }

        $prefix = $this->getUrlEncodedPrefix($requestUri, dirname($url));
        if (false !== $prefix) {
            return $this->baseUrl = rtrim($prefix, '/').'/';
        }

        if (!strpos(rawurldecode($requestUri), basename($url))) {
            return $this->baseUrl = '';
        }

        if (strlen($requestUri) >= strlen($url) &&
            false !== ($pos = strpos($requestUri, $url)) && 0 !== $pos) {
            $url = substr($requestUri, 0, $pos + strlen($url));
        }

        return $this->baseUrl = rtrim($url, '/').'/';
    }

    /**
     * 获取请求参数.
     */
    public function getRequestUri(): string
    {
        if (null !== $this->requestUri) {
            return $this->requestUri;
        }

        $requestUri = $this->headers->get('X_REWRITE_URL', $this->server->get('REQUEST_URI', ''));
        if (!$requestUri) {
            $requestUri = $this->server->get('ORIG_PATH_INFO', '');
            if ($this->server->get('QUERY_STRING')) {
                $requestUri .= '?'.$this->server->get('QUERY_STRING');
            }
        }

        return $this->requestUri = $requestUri;
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
     * pathinfo 处理.
     */
    protected function parsePathInfo(string $pathInfo): string
    {
        if ($pathInfo) {
            $ext = pathinfo($pathInfo, PATHINFO_EXTENSION);
            if ($ext) {
                $pathInfo = substr($pathInfo, 0, -(strlen($ext) + 1));
            }
        }

        $pathInfo = empty($pathInfo) ? '/' : '/'.ltrim($pathInfo, '/');

        return $pathInfo;
    }

    /**
     * 格式化查询参数.
     */
    protected function normalizeQueryString(?string $queryString): string
    {
        if (!$queryString && '0' !== $queryString) {
            return '';
        }

        $parts = [];
        foreach (explode('&', $queryString) as $item) {
            if ('' === $item && '0' !== $item) {
                continue;
            }
            $parts[] = $item;
        }

        return implode('&', $parts);
    }

    /**
     * 取得请求输入源.
     *
     * @return \Leevel\Http\Bag
     */
    protected function getInputSource(): Bag
    {
        return $this->getMethod() === static::METHOD_GET ? $this->query : $this->request;
    }

    /**
     * 是否为空字符串.
     */
    protected function isEmptyString(string $key): bool
    {
        $value = $this->input($key);

        return is_string($value) && '' === trim($value) && '0' !== $value;
    }

    /**
     * URL 前缀编码.
     *
     * @return bool|string
     */
    protected function getUrlEncodedPrefix(string $value, string $prefix)
    {
        if (0 !== strpos(rawurldecode($value), $prefix)) {
            return false;
        }

        $len = strlen($prefix);
        if (preg_match(sprintf('#^(%%[[:xdigit:]]{2}|.){%d}#', $len), $value, $matches)) {
            return $matches[0];
        }

        return false;
    }
}
