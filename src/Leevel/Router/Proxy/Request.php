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

namespace Leevel\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Http\Request as BaseRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * 代理 request.
 *
 * @codeCoverageIgnore
 */
class Request
{
    /**
     * call.
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 是否处于协程上下文.
     */
    public static function coroutineContext(): bool
    {
        return self::proxy()->coroutineContext();
    }

    /**
     * 从 Symfony 请求创建 Leevel 请求.
     */
    public static function createFromSymfonyRequest(SymfonyRequest $request): BaseRequest
    {
        return self::proxy()->createFromSymfonyRequest($request);
    }

    /**
     * 请求是否包含给定的 keys.
     */
    public static function exists(array $keys): bool
    {
        return self::proxy()->exists($keys);
    }

    /**
     * 取得给定的 keys 数据.
     */
    public static function only(array $keys): array
    {
        return self::proxy()->only($keys);
    }

    /**
     * 取得排除给定的 keys 数据.
     */
    public static function except(array $keys): array
    {
        return self::proxy()->except($keys);
    }

    /**
     * 获取所有请求参数.
     *
     * - 包含 request、query 和 attributes
     * - 优先级从高到底依次为 attributes、query 和 request，优先级高的会覆盖优先级低的参数
     */
    public static function all(): array
    {
        return self::proxy()->all();
    }

    /**
     * 是否为 PHP 运行模式命令行, 兼容 Swoole HTTP Service.
     *
     * - Swoole HTTP 服务器也以命令行运行.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     */
    public static function isConsole(): bool
    {
        return self::proxy()->isConsole();
    }

    /**
     * PHP 运行模式命令行.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     */
    public static function isRealCli(): bool
    {
        return self::proxy()->isRealCli();
    }

    /**
     * 是否为 PHP 运行模式 cgi.
     *
     * @see http://php.net/manual/zh/function.php-sapi-name.php
     */
    public static function isCgi(): bool
    {
        return self::proxy()->isCgi();
    }

    /**
     * 是否为 Ajax 请求行为.
     */
    public static function isAjax(): bool
    {
        return self::proxy()->isAjax();
    }

    /**
     * 是否为 Ajax 请求行为真实.
     */
    public static function isRealAjax(): bool
    {
        return self::proxy()->isRealAjax();
    }

    /**
     * 是否为 Pjax 请求行为.
     */
    public static function isPjax(): bool
    {
        return self::proxy()->isPjax();
    }

    /**
     * 是否为 Pjax 请求行为真实.
     */
    public static function isRealPjax(): bool
    {
        return self::proxy()->isRealPjax();
    }

    /**
     * 是否为接受 JSON 请求.
     */
    public static function isAcceptJson(): bool
    {
        return self::proxy()->isAcceptJson();
    }

    /**
     * 是否为接受 JSON 请求真实.
     */
    public static function isRealAcceptJson(): bool
    {
        return self::proxy()->isRealAcceptJson();
    }

    /**
     * 是否为接受任何请求.
     */
    public static function isAcceptAny(): bool
    {
        return self::proxy()->isAcceptAny();
    }

    /**
     * 获取入口文件.
     */
    public static function getEnter(): string
    {
        return self::proxy()->getEnter();
    }

    /**
     * 设置 pathInfo.
     */
    public static function setPathInfo(string $pathInfo): void
    {
        self::proxy()->setPathInfo($pathInfo);
    }

    /**
     * 对象转数组.
     */
    public static function toArray(): array
    {
        return self::proxy()->toArray();
    }

    /**
     * Sets the parameters for this request.
     *
     * This method also re-initializes all properties.
     *
     * @param array                $query      The GET parameters
     * @param array                $request    The POST parameters
     * @param array                $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array                $cookies    The COOKIE parameters
     * @param array                $files      The FILES parameters
     * @param array                $server     The SERVER parameters
     * @param null|resource|string $content    The raw body data
     */
    public static function initialize(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        self::proxy()->initialize($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    /**
     * Creates a new request with values from PHP's super globals.
     */
    public static function createFromGlobals(): BaseRequest
    {
        return self::proxy()->createFromGlobals();
    }

    /**
     * Creates a Request based on a given URI and configuration.
     *
     * The information contained in the URI always take precedence
     * over the other information (server and parameters).
     *
     * @param string               $uri        The URI
     * @param string               $method     The HTTP method
     * @param array                $parameters The query (GET) or request (POST) parameters
     * @param array                $cookies    The request cookies ($_COOKIE)
     * @param array                $files      The request files ($_FILES)
     * @param array                $server     The server parameters ($_SERVER)
     * @param null|resource|string $content    The raw body data
     */
    public static function create(string $uri, string $method = 'GET', array $parameters = [], array $cookies = [], array $files = [], array $server = [], $content = null): BaseRequest
    {
        return self::proxy()->create($uri, $method, $parameters, $cookies, $files, $server, $content);
    }

    /**
     * Sets a callable able to create a Request instance.
     *
     * This is mainly useful when you need to override the Request class
     * to keep BC with an existing system. It should not be used for any
     * other purpose.
     *
     * @param null|callable $callable A PHP callable
     */
    public static function setFactory(?callable $callable = null): void
    {
        self::proxy()->setFactory($callable);
    }

    /**
     * Clones a request and overrides some of its parameters.
     *
     * @param array $query      The GET parameters
     * @param array $request    The POST parameters
     * @param array $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array $cookies    The COOKIE parameters
     * @param array $files      The FILES parameters
     * @param array $server     The SERVER parameters
     */
    public static function duplicate(?array $query = null, ?array $request = null, ?array $attributes = null, ?array $cookies = null, ?array $files = null, ?array $server = null): BaseRequest
    {
        return self::proxy()->duplicate($query, $request, $attributes, $cookies, $files, $server);
    }

    /**
     * Overrides the PHP global variables according to this request instance.
     *
     * It overrides $_GET, $_POST, $_REQUEST, $_SERVER, $_COOKIE.
     * $_FILES is never overridden, see rfc1867
     */
    public static function overrideGlobals(): void
    {
        self::proxy()->overrideGlobals();
    }

    /**
     * Sets a list of trusted proxies.
     *
     * You should only list the reverse proxies that you manage directly.
     *
     * @param array $proxies          A list of trusted proxies, the string 'REMOTE_ADDR' will be replaced with $_SERVER['REMOTE_ADDR']
     * @param int   $trustedHeaderSet A bit field of Request::HEADER_*, to set which headers to trust from your proxies
     *
     * @throws \InvalidArgumentException When $trustedHeaderSet is invalid
     */
    public static function setTrustedProxies(array $proxies, int $trustedHeaderSet): void
    {
        self::proxy()->setTrustedProxies($proxies, $trustedHeaderSet);
    }

    /**
     * Gets the list of trusted proxies.
     *
     * @return array An array of trusted proxies
     */
    public static function getTrustedProxies(): array
    {
        return self::proxy()->getTrustedProxies();
    }

    /**
     * Gets the set of trusted headers from trusted proxies.
     *
     * @return int A bit field of Request::HEADER_* that defines which headers are trusted from your proxies
     */
    public static function getTrustedHeaderSet(): int
    {
        return self::proxy()->getTrustedHeaderSet();
    }

    /**
     * Sets a list of trusted host patterns.
     *
     * You should only list the hosts you manage using regexs.
     *
     * @param array $hostPatterns A list of trusted host patterns
     */
    public static function setTrustedHosts(array $hostPatterns): void
    {
        self::proxy()->setTrustedHosts($hostPatterns);
    }

    /**
     * Gets the list of trusted host patterns.
     *
     * @return array An array of trusted host patterns
     */
    public static function getTrustedHosts(): array
    {
        return self::proxy()->getTrustedHosts();
    }

    /**
     * Normalizes a query string.
     *
     * It builds a normalized query string, where keys/value pairs are alphabetized,
     * have consistent escaping and unneeded delimiters are removed.
     *
     * @param string $qs Query string
     *
     * @return string A normalized query string for the Request
     */
    public static function normalizeQueryString(string $qs): string
    {
        return self::proxy()->normalizeQueryString($qs);
    }

    /**
     * Enables support for the _method request parameter to determine the intended HTTP method.
     *
     * Be warned that enabling this feature might lead to CSRF issues in your code.
     * Check that you are using CSRF tokens when required.
     * If the HTTP method parameter override is enabled, an html-form with method "POST" can be altered
     * and used to send a "PUT" or "DELETE" request via the _method request parameter.
     * If these methods are not protected against CSRF, this presents a possible vulnerability.
     *
     * The HTTP method can only be overridden when the real HTTP method is POST.
     */
    public static function enableHttpMethodParameterOverride(): void
    {
        self::proxy()->enableHttpMethodParameterOverride();
    }

    /**
     * Checks whether support for the _method request parameter is enabled.
     *
     * @return bool True when the _method request parameter is enabled, false otherwise
     */
    public static function getHttpMethodParameterOverride(): bool
    {
        return self::proxy()->getHttpMethodParameterOverride();
    }

    /**
     * Gets a "parameter" value from any bag.
     *
     * This method is mainly useful for libraries that want to provide some flexibility. If you don't need the
     * flexibility in controllers, it is better to explicitly get request parameters from the appropriate
     * public property instead (attributes, query, request).
     *
     * Order of precedence: PATH (routing placeholders or custom attributes), GET, BODY
     *
     * @param string $key     The key
     * @param mixed  $default The default value if the parameter key does not exist
     *
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        return self::proxy()->get($key, $default);
    }

    /**
     * Gets the Session.
     *
     * @return \Symfony\Component\HttpFoundation\Session\SessionInterface The session
     */
    public static function getSession(): SessionInterface
    {
        return self::proxy()->getSession();
    }

    /**
     * Whether the request contains a Session which was started in one of the
     * previous requests.
     */
    public static function hasPreviousSession(): bool
    {
        return self::proxy()->hasPreviousSession();
    }

    /**
     * Whether the request contains a Session object.
     *
     * This method does not give any information about the state of the session object,
     * like whether the session is started or not. It is just a way to check if this Request
     * is associated with a Session instance.
     *
     * @return bool true when the Request contains a Session object, false otherwise
     */
    public static function hasSession(): bool
    {
        return self::proxy()->hasSession();
    }

    public static function setSession(SessionInterface $session): void
    {
        self::proxy()->setSession($session);
    }

    /**
     * @internal
     */
    public static function setSessionFactory(callable $factory): void
    {
        self::proxy()->setSessionFactory($factory);
    }

    /**
     * Returns the client IP addresses.
     *
     * In the returned array the most trusted IP address is first, and the
     * least trusted one last. The "real" client IP address is the last one,
     * but this is also the least trusted one. Trusted proxies are stripped.
     *
     * Use this method carefully; you should use getClientIp() instead.
     *
     * @return array The client IP addresses
     *
     * @see getClientIp()
     */
    public static function getClientIps(): array
    {
        return self::proxy()->getClientIps();
    }

    /**
     * Returns the client IP address.
     *
     * This method can read the client IP address from the "X-Forwarded-For" header
     * when trusted proxies were set via "setTrustedProxies()". The "X-Forwarded-For"
     * header value is a comma+space separated list of IP addresses, the left-most
     * being the original client, and each successive proxy that passed the request
     * adding the IP address where it received the request from.
     *
     * If your reverse proxy uses a different header name than "X-Forwarded-For",
     * ("Client-Ip" for instance), configure it via the $trustedHeaderSet
     * argument of the Request::setTrustedProxies() method instead.
     *
     * @return null|string The client IP address
     *
     * @see getClientIps()
     * @see https://wikipedia.org/wiki/X-Forwarded-For
     */
    public static function getClientIp(): ?string
    {
        return self::proxy()->getClientIp();
    }

    /**
     * Returns current script name.
     */
    public static function getScriptName(): string
    {
        return self::proxy()->getScriptName();
    }

    /**
     * Returns the path being requested relative to the executed script.
     *
     * The path info always starts with a /.
     *
     * Suppose this request is instantiated from /mysite on localhost:
     *
     *  * http://localhost/mysite              returns an empty string
     *  * http://localhost/mysite/about        returns '/about'
     *  * http://localhost/mysite/enco%20ded   returns '/enco%20ded'
     *  * http://localhost/mysite/about?var=1  returns '/about'
     *
     * @return string The raw path (i.e. not urldecoded)
     */
    public static function getPathInfo(): string
    {
        return self::proxy()->getPathInfo();
    }

    /**
     * Returns the root path from which this request is executed.
     *
     * Suppose that an index.php file instantiates this request object:
     *
     *  * http://localhost/index.php         returns an empty string
     *  * http://localhost/index.php/page    returns an empty string
     *  * http://localhost/web/index.php     returns '/web'
     *  * http://localhost/we%20b/index.php  returns '/we%20b'
     *
     * @return string The raw path (i.e. not urldecoded)
     */
    public static function getBasePath(): string
    {
        return self::proxy()->getBasePath();
    }

    /**
     * Returns the root URL from which this request is executed.
     *
     * The base URL never ends with a /.
     *
     * This is similar to getBasePath(), except that it also includes the
     * script filename (e.g. index.php) if one exists.
     *
     * @return string The raw URL (i.e. not urldecoded)
     */
    public static function getBaseUrl(): string
    {
        return self::proxy()->getBaseUrl();
    }

    /**
     * Gets the request's scheme.
     */
    public static function getScheme(): string
    {
        return self::proxy()->getScheme();
    }

    /**
     * Returns the port on which the request is made.
     *
     * This method can read the client port from the "X-Forwarded-Port" header
     * when trusted proxies were set via "setTrustedProxies()".
     *
     * The "X-Forwarded-Port" header must contain the client port.
     *
     * @return int|string can be a string if fetched from the server bag
     */
    public static function getPort()
    {
        return self::proxy()->getPort();
    }

    /**
     * Returns the user.
     */
    public static function getUser(): ?string
    {
        return self::proxy()->getUser();
    }

    /**
     * Returns the password.
     */
    public static function getPassword(): ?string
    {
        return self::proxy()->getPassword();
    }

    /**
     * Gets the user info.
     *
     * @return string A user name and, optionally, scheme-specific information about how to gain authorization to access the server
     */
    public static function getUserInfo(): string
    {
        return self::proxy()->getUserInfo();
    }

    /**
     * Returns the HTTP host being requested.
     *
     * The port name will be appended to the host if it's non-standard.
     */
    public static function getHttpHost(): string
    {
        return self::proxy()->getHttpHost();
    }

    /**
     * Returns the requested URI (path and query string).
     *
     * @return string The raw URI (i.e. not URI decoded)
     */
    public static function getRequestUri(): string
    {
        return self::proxy()->getRequestUri();
    }

    /**
     * Gets the scheme and HTTP host.
     *
     * If the URL was called with basic authentication, the user
     * and the password are not added to the generated string.
     *
     * @return string The scheme and HTTP host
     */
    public static function getSchemeAndHttpHost(): string
    {
        return self::proxy()->getSchemeAndHttpHost();
    }

    /**
     * Generates a normalized URI (URL) for the Request.
     *
     * @return string A normalized URI (URL) for the Request
     *
     * @see getQueryString()
     */
    public static function getUri(): string
    {
        return self::proxy()->getUri();
    }

    /**
     * Generates a normalized URI for the given path.
     *
     * @param string $path A path to use instead of the current one
     *
     * @return string The normalized URI for the path
     */
    public static function getUriForPath(string $path): string
    {
        return self::proxy()->getUriForPath($path);
    }

    /**
     * Returns the path as relative reference from the current Request path.
     *
     * Only the URIs path component (no schema, host etc.) is relevant and must be given.
     * Both paths must be absolute and not contain relative parts.
     * Relative URLs from one resource to another are useful when generating self-contained downloadable document archives.
     * Furthermore, they can be used to reduce the link size in documents.
     *
     * Example target paths, given a base path of "/a/b/c/d":
     * - "/a/b/c/d"     -> ""
     * - "/a/b/c/"      -> "./"
     * - "/a/b/"        -> "../"
     * - "/a/b/c/other" -> "other"
     * - "/a/x/y"       -> "../../x/y"
     *
     * @param string $path The target path
     *
     * @return string The relative target path
     */
    public static function getRelativeUriForPath(string $path): string
    {
        return self::proxy()->getRelativeUriForPath($path);
    }

    /**
     * Generates the normalized query string for the Request.
     *
     * It builds a normalized query string, where keys/value pairs are alphabetized
     * and have consistent escaping.
     *
     * @return null|string A normalized query string for the Request
     */
    public static function getQueryString(): ?string
    {
        return self::proxy()->getQueryString();
    }

    /**
     * Checks whether the request is secure or not.
     *
     * This method can read the client protocol from the "X-Forwarded-Proto" header
     * when trusted proxies were set via "setTrustedProxies()".
     *
     * The "X-Forwarded-Proto" header must contain the protocol: "https" or "http".
     */
    public static function isSecure(): bool
    {
        return self::proxy()->isSecure();
    }

    /**
     * Returns the host name.
     *
     * This method can read the client host name from the "X-Forwarded-Host" header
     * when trusted proxies were set via "setTrustedProxies()".
     *
     * The "X-Forwarded-Host" header must contain the client host name.
     *
     * @throws \Symfony\Component\HttpFoundation\Exception when the host name is invalid or not trusted
     */
    public static function getHost(): string
    {
        return self::proxy()->getHost();
    }

    /**
     * Sets the request method.
     */
    public static function setMethod(string $method): void
    {
        self::proxy()->setMethod($method);
    }

    /**
     * Gets the request "intended" method.
     *
     * If the X-HTTP-Method-Override header is set, and if the method is a POST,
     * then it is used to determine the "real" intended HTTP method.
     *
     * The _method request parameter can also be used to determine the HTTP method,
     * but only if enableHttpMethodParameterOverride() has been called.
     *
     * The method is always an uppercased string.
     *
     * @return string The request method
     *
     * @see getRealMethod()
     */
    public static function getMethod(): string
    {
        return self::proxy()->getMethod();
    }

    /**
     * Gets the "real" request method.
     *
     * @return string The request method
     *
     * @see getMethod()
     */
    public static function getRealMethod(): string
    {
        return self::proxy()->getRealMethod();
    }

    /**
     * Gets the mime type associated with the format.
     *
     * @param string $format The format
     *
     * @return null|string The associated mime type (null if not found)
     */
    public static function getMimeType(string $format): ?string
    {
        return self::proxy()->getMimeType($format);
    }

    /**
     * Gets the mime types associated with the format.
     *
     * @param string $format The format
     *
     * @return array The associated mime types
     */
    public static function getMimeTypes(string $format): array
    {
        return self::proxy()->getMimeTypes($format);
    }

    /**
     * Gets the format associated with the mime type.
     *
     * @param string $mimeType The associated mime type
     *
     * @return null|string The format (null if not found)
     */
    public static function getFormat(string $mimeType): ?string
    {
        return self::proxy()->getFormat($mimeType);
    }

    /**
     * Associates a format with mime types.
     *
     * @param string       $format    The format
     * @param array|string $mimeTypes The associated mime types (the preferred one must be the first as it will be used as the content type)
     */
    public static function setFormat(string $format, $mimeTypes): void
    {
        self::proxy()->setFormat($format, $mimeTypes);
    }

    /**
     * Gets the request format.
     *
     * Here is the process to determine the format:
     *
     *  * format defined by the user (with setRequestFormat())
     *  * _format request attribute
     *  * $default
     *
     * @see getPreferredFormat
     *
     * @param null|string $default The default format
     *
     * @return null|string The request format
     */
    public static function getRequestFormat(?string $default = 'html'): ?string
    {
        return self::proxy()->getRequestFormat($default);
    }

    /**
     * Sets the request format.
     *
     * @param string $format The request format
     */
    public static function setRequestFormat(string $format): void
    {
        self::proxy()->setRequestFormat($format);
    }

    /**
     * Gets the format associated with the request.
     *
     * @return null|string The format (null if no content type is present)
     */
    public static function getContentType(): ?string
    {
        return self::proxy()->getContentType();
    }

    /**
     * Sets the default locale.
     */
    public static function setDefaultLocale(string $locale): void
    {
        self::proxy()->setDefaultLocale($locale);
    }

    /**
     * Get the default locale.
     */
    public static function getDefaultLocale(): string
    {
        return self::proxy()->getDefaultLocale();
    }

    /**
     * Sets the locale.
     */
    public static function setLocale(string $locale): void
    {
        self::proxy()->setLocale($locale);
    }

    /**
     * Get the locale.
     */
    public static function getLocale(): string
    {
        return self::proxy()->getLocale();
    }

    /**
     * Checks if the request method is of specified type.
     *
     * @param string $method Uppercase request method (GET, POST etc)
     */
    public static function isMethod(string $method): bool
    {
        return self::proxy()->isMethod($method);
    }

    /**
     * Checks whether or not the method is safe.
     *
     * @see https://tools.ietf.org/html/rfc7231#section-4.2.1
     */
    public static function isMethodSafe(): bool
    {
        return self::proxy()->isMethodSafe();
    }

    /**
     * Checks whether or not the method is idempotent.
     */
    public static function isMethodIdempotent(): bool
    {
        return self::proxy()->isMethodIdempotent();
    }

    /**
     * Checks whether the method is cacheable or not.
     *
     * @see https://tools.ietf.org/html/rfc7231#section-4.2.3
     *
     * @return bool True for GET and HEAD, false otherwise
     */
    public static function isMethodCacheable(): bool
    {
        return self::proxy()->isMethodCacheable();
    }

    /**
     * Returns the protocol version.
     *
     * If the application is behind a proxy, the protocol version used in the
     * requests between the client and the proxy and between the proxy and the
     * server might be different. This returns the former (from the "Via" header)
     * if the proxy is trusted (see "setTrustedProxies()"), otherwise it returns
     * the latter (from the "SERVER_PROTOCOL" server parameter).
     */
    public static function getProtocolVersion(): string
    {
        return self::proxy()->getProtocolVersion();
    }

    /**
     * Returns the request body content.
     *
     * @param bool $asResource If true, a resource will be returned
     *
     * @throws \LogicException
     *
     * @return resource|string The request body content or a resource to read the body stream
     */
    public static function getContent(bool $asResource = false)
    {
        return self::proxy()->getContent($asResource);
    }

    /**
     * Gets the Etags.
     *
     * @return array The entity tags
     */
    public static function getETags(): array
    {
        return self::proxy()->getETags();
    }

    public static function isNoCache(): bool
    {
        return self::proxy()->isNoCache();
    }

    /**
     * Gets the preferred format for the response by inspecting, in the following order:
     *   * the request format set using setRequestFormat
     *   * the values of the Accept HTTP header
     *   * the content type of the body of the request.
     */
    public static function getPreferredFormat(?string $default = 'html'): ?string
    {
        return self::proxy()->getPreferredFormat($default);
    }

    /**
     * Returns the preferred language.
     *
     * @param string[] $locales An array of ordered available locales
     *
     * @return null|string The preferred locale
     */
    public static function getPreferredLanguage(?array $locales = null): ?string
    {
        return self::proxy()->getPreferredLanguage($locales);
    }

    /**
     * Gets a list of languages acceptable by the client browser.
     *
     * @return array Languages ordered in the user browser preferences
     */
    public static function getLanguages(): array
    {
        return self::proxy()->getLanguages();
    }

    /**
     * Gets a list of charsets acceptable by the client browser.
     *
     * @return array List of charsets in preferable order
     */
    public static function getCharsets(): array
    {
        return self::proxy()->getCharsets();
    }

    /**
     * Gets a list of encodings acceptable by the client browser.
     *
     * @return array List of encodings in preferable order
     */
    public static function getEncodings(): array
    {
        return self::proxy()->getEncodings();
    }

    /**
     * Gets a list of content types acceptable by the client browser.
     *
     * @return array List of content types in preferable order
     */
    public static function getAcceptableContentTypes(): array
    {
        return self::proxy()->getAcceptableContentTypes();
    }

    /**
     * Returns true if the request is a XMLHttpRequest.
     *
     * It works if your JavaScript library sets an X-Requested-With HTTP header.
     * It is known to work with common JavaScript frameworks:
     *
     * @see https://wikipedia.org/wiki/List_of_Ajax_frameworks#JavaScript
     *
     * @return bool true if the request is an XMLHttpRequest, false otherwise
     */
    public static function isXmlHttpRequest(): bool
    {
        return self::proxy()->isXmlHttpRequest();
    }

    /**
     * Indicates whether this request originated from a trusted proxy.
     *
     * This can be useful to determine whether or not to trust the
     * contents of a proxy-specific header.
     *
     * @return bool true if the request came from a trusted proxy, false otherwise
     */
    public static function isFromTrustedProxy(): bool
    {
        return self::proxy()->isFromTrustedProxy();
    }

    /**
     * 代理服务.
     */
    public static function proxy(): BaseRequest
    {
        return Container::singletons()->make('request');
    }
}
