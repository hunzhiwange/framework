<?php

declare(strict_types=1);

namespace Leevel\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Http\Request as BaseRequest;

/**
 * 代理 request.
 *
 * @method static \Leevel\Http\Request createFromSymfonyRequest(\Symfony\Component\HttpFoundation\Request $request)                                                                            从 Symfony 请求创建 Leevel 请求.
 * @method static bool exists(array $keys)                                                                                                                                                     请求是否包含给定的 keys.
 * @method static array only(array $keys)                                                                                                                                                      取得给定的 keys 数据.
 * @method static array except(array $keys)                                                                                                                                                    取得排除给定的 keys 数据.
 * @method static array all()                                                                                                                                                                  获取所有请求参数.
 * @method static bool isConsole()                                                                                                                                                             是否为 PHP 运行模式命令行, 兼容 Swoole HTTP Service.
 * @method static bool isRealCli()                                                                                                                                                             PHP 运行模式命令行.
 * @method static bool isCgi()                                                                                                                                                                 是否为 PHP 运行模式 cgi.
 * @method static bool isAjax()                                                                                                                                                                是否为 Ajax 请求行为.
 * @method static bool isRealAjax()                                                                                                                                                            是否为 Ajax 请求行为真实.
 * @method static bool isPjax()                                                                                                                                                                是否为 Pjax 请求行为.
 * @method static bool isRealPjax()                                                                                                                                                            是否为 Pjax 请求行为真实.
 * @method static bool isAcceptJson()                                                                                                                                                          是否为接受 JSON 请求.
 * @method static bool isRealAcceptJson()                                                                                                                                                      是否为接受 JSON 请求真实.
 * @method static bool isAcceptAny()                                                                                                                                                           是否为接受任何请求.
 * @method static string getEnter()                                                                                                                                                            获取入口文件.
 * @method static void setPathInfo(string $pathInfo)                                                                                                                                           设置 pathInfo.
 * @method static array toArray()                                                                                                                                                              对象转数组.
 * @method static void initialize(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)                 Sets the parameters for this request.
 * @method static \Leevel\Http\Request createFromGlobals()                                                                                                                                     Creates a new request with values from PHP's super globals.
 * @method static \Leevel\Http\Request create(string $uri, string $method = 'GET', array $parameters = [], array $cookies = [], array $files = [], array $server = [], $content = null)        Creates a Request based on a given URI and configuration.
 * @method static void setFactory(?callable $callable = null)                                                                                                                                  Sets a callable able to create a Request instance.
 * @method static \Leevel\Http\Request duplicate(?array $query = null, ?array $request = null, ?array $attributes = null, ?array $cookies = null, ?array $files = null, ?array $server = null) Clones a request and overrides some of its parameters.
 * @method static void overrideGlobals()                                                                                                                                                       Overrides the PHP global variables according to this request instance.
 * @method static void setTrustedProxies(array $proxies, int $trustedHeaderSet)                                                                                                                Sets a list of trusted proxies.
 * @method static array getTrustedProxies()                                                                                                                                                    Gets the list of trusted proxies.
 * @method static int getTrustedHeaderSet()                                                                                                                                                    Gets the set of trusted headers from trusted proxies.
 * @method static void setTrustedHosts(array $hostPatterns)                                                                                                                                    Sets a list of trusted host patterns.
 * @method static array getTrustedHosts()                                                                                                                                                      Gets the list of trusted host patterns.
 * @method static string normalizeQueryString(string $qs)                                                                                                                                      Normalizes a query string.
 * @method static void enableHttpMethodParameterOverride()                                                                                                                                     Enables support for the _method request parameter to determine the intended HTTP method.
 * @method static bool getHttpMethodParameterOverride()                                                                                                                                        Checks whether support for the _method request parameter is enabled.
 * @method static mixed get(string $key, $default = null)                                                                                                                                      Gets a "parameter" value from any bag.
 * @method static \Symfony\Component\HttpFoundation\Session\SessionInterface getSession()                                                                                                      Gets the Session.
 * @method static bool hasPreviousSession()                                                                                                                                                    Whether the request contains a Session which was started in one of the previous requests.
 * @method static bool hasSession()                                                                                                                                                            Whether the request contains a Session object.
 * @method static void setSession(\Symfony\Component\HttpFoundation\Session\SessionInterface $session)
 * @method static void setSessionFactory(callable $factory)                                                                                                                                    @internal
 * @method static array getClientIps()                                                                                                                                                         Returns the client IP addresses.
 * @method static ?string getClientIp()                                                                                                                                                        Returns the client IP address.
 * @method static string getScriptName()                                                                                                                                                       Returns current script name.
 * @method static string getPathInfo()                                                                                                                                                         Returns the path being requested relative to the executed script.
 * @method static string getBasePath()                                                                                                                                                         Returns the root path from which this request is executed.
 * @method static string getBaseUrl()                                                                                                                                                          Returns the root URL from which this request is executed.
 * @method static string getScheme()                                                                                                                                                           Gets the request's scheme.
 * @method static mixed getPort()                                                                                                                                                              Returns the port on which the request is made.
 * @method static ?string getUser()                                                                                                                                                            Returns the user.
 * @method static ?string getPassword()                                                                                                                                                        Returns the password.
 * @method static string getUserInfo()                                                                                                                                                         Gets the user info.
 * @method static string getHttpHost()                                                                                                                                                         Returns the HTTP host being requested.
 * @method static string getRequestUri()                                                                                                                                                       Returns the requested URI (path and query string).
 * @method static string getSchemeAndHttpHost()                                                                                                                                                Gets the scheme and HTTP host.
 * @method static string getUri() Generates a normalized URI (URL)                                                                                                                             for the Request.
 * @method static string getUriForPath(string $path)                                                                                                                                           Generates a normalized URI for the given path.
 * @method static string getRelativeUriForPath(string $path)                                                                                                                                   Returns the path as relative reference from the current Request path.
 * @method static ?string getQueryString()                                                                                                                                                     Generates the normalized query string for the Request.
 * @method static bool isSecure()                                                                                                                                                              Checks whether the request is secure or not.
 * @method static string getHost()                                                                                                                                                             Returns the host name.
 * @method static void setMethod(string $method)                                                                                                                                               Sets the request method.
 * @method static string getMethod()                                                                                                                                                           Gets the request "intended" method.
 * @method static string getRealMethod()                                                                                                                                                       Gets the "real" request method.
 * @method static ?string getMimeType(string $format)                                                                                                                                          Gets the mime type associated with the format.
 * @method static array getMimeTypes(string $format)                                                                                                                                           Gets the mime types associated with the format.
 * @method static ?string getFormat(string $mimeType)                                                                                                                                          Gets the format associated with the mime type.
 * @method static void setFormat(string $format, $mimeTypes)                                                                                                                                   Associates a format with mime types.
 * @method static ?string getRequestFormat(?string $default = 'html')                                                                                                                          Gets the request format.
 * @method static void setRequestFormat(string $format)                                                                                                                                        Sets the request format.
 * @method static ?string getContentType()                                                                                                                                                     Gets the format associated with the request.
 * @method static void setDefaultLocale(string $locale)                                                                                                                                        Sets the default locale.
 * @method static string getDefaultLocale()                                                                                                                                                    Get the default locale.
 * @method static void setLocale(string $locale)                                                                                                                                               Sets the locale.
 * @method static string getLocale()                                                                                                                                                           Get the locale.
 * @method static bool isMethod(string $method)                                                                                                                                                Checks if the request method is of specified type.
 * @method static bool isMethodSafe()                                                                                                                                                          Checks whether or not the method is safe.
 * @method static bool isMethodIdempotent()                                                                                                                                                    Checks whether or not the method is idempotent.
 * @method static bool isMethodCacheable()                                                                                                                                                     Checks whether the method is cacheable or not.
 * @method static string getProtocolVersion()                                                                                                                                                  Returns the protocol version.
 * @method static mixed getContent(bool $asResource = false)                                                                                                                                   Returns the request body content.
 * @method static array getETags()                                                                                                                                                             Gets the Etags.
 * @method static bool isNoCache()
 * @method static ?string getPreferredFormat(?string $default = 'html')                                                                                                                        Gets the preferred format for the response by inspecting, in the following order: the request format set using setRequestFormat the values of the Accept HTTP header the content type of the body of the request.
 * @method static ?string getPreferredLanguage(?array $locales = null)                                                                                                                         Returns the preferred language.
 * @method static array getLanguages()                                                                                                                                                         Gets a list of languages acceptable by the client browser.
 * @method static array getCharsets()                                                                                                                                                          Gets a list of charsets acceptable by the client browser.
 * @method static array getEncodings()                                                                                                                                                         Gets a list of encodings acceptable by the client browser.
 * @method static array getAcceptableContentTypes()                                                                                                                                            Gets a list of content types acceptable by the client browser.
 * @method static bool isXmlHttpRequest()                                                                                                                                                      Returns true if the request is a XMLHttpRequest.
 * @method static bool isFromTrustedProxy()                                                                                                                                                    Indicates whether this request originated from a trusted proxy.
 */
class Request
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): BaseRequest
    {
        return Container::singletons()->make('request');
    }
}
