<?php declare(strict_types=1);
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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Leevel\Http;

use Closure;
use DateTime;
use ArrayObject;
use DateTimeZone;
use JsonSerializable;
use InvalidArgumentException;
use UnexpectedValueException;
use Leevel\{
    Support\IJson,
    Flow\TControl,
    Support\IArray,
    Support\TMacro,
    Cookie\ICookie
};

/**
 * HTTP 响应
 * This class borrows heavily from the Symfony4 Framework and is part of the symfony package
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.18
 * @version 1.0
 * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
 */
class Response implements IResponse
{
    use TControl;

    use TMacro;

    /**
     * 响应头
     * 
     * @var \Leevel\Http\ResponseHeaderBag
     */
    public $headers;

    /**
     * 原生响应内容
     *
     * @var mixed
     */
    public $original;

    /**
     * 响应内容
     * 
     * @var string
     */
    protected $content;

    /**
     * HTTP 协议版本
     * 
     * @var string
     */
    protected $protocolVersion;

    /**
     * 状态码
     * 
     * @var int
     */
    protected $statusCode;

    /**
     * 状态码内容
     * 
     * @var string
     */
    protected $statusText;

    /**
     * 字符编码
     * 
     * @var string
     */
    protected $charset;

    /**
     * 是否为 JSON
     * 
     * @var boolean
     */
    protected $isJson = false;

    /**
     * COOKIE Resolver
     * 
     * @var \Closure
     */
    protected static $cookieResolver;

    /**
     * 状态码
     *
     * @see http://www.iana.org/assignments/http-status-codes/
     * @var array
     */
    public static $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing', // RFC2518
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status', // RFC4918
        208 => 'Already Reported', // RFC5842
        226 => 'IM Used', // RFC3229
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect', // RFC7238
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot', // RFC2324
        421 => 'Misdirected Request', // RFC7540
        422 => 'Unprocessable Entity', // RFC4918
        423 => 'Locked', // RFC4918
        424 => 'Failed Dependency', // RFC4918
        425 => 'Reserved for WebDAV advanced collections expired proposal', // RFC2817
        426 => 'Upgrade Required', // RFC2817
        428 => 'Precondition Required', // RFC6585
        429 => 'Too Many Requests', // RFC6585
        431 => 'Request Header Fields Too Large', // RFC6585
        451 => 'Unavailable For Legal Reasons', // RFC7725
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates', // RFC2295
        507 => 'Insufficient Storage', // RFC4918
        508 => 'Loop Detected', // RFC5842
        510 => 'Not Extended', // RFC2774
        511 => 'Network Authentication Required', // RFC6585
    ];

    /**
     * 构造函数
     * 
     * @param string $content
     * @param integer $status
     * @param array $headers
     * @return void
     */
    public function __construct($content = '', int $status = 200, array $headers = []) {
        $this->headers = new ResponseHeaderBag($headers);
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->setProtocolVersion('1.0');
    }

    /**
     * 创建一个响应
     * 
     * @param string $content
     * @param integer $status
     * @param array $headers
     * @return static
     */
    public static function create($content = '', int $status = 200, array $headers = []) {
        return new static($content, $status, $headers);
    }

    /**
     * 设置 COOKIE Resolver
     * 
     * @param \Closure $cookieResolver
     * @return void
     */
    public static function setCookieResolver(Closure $cookieResolver = null) {
        static::$cookieResolver = $cookieResolver;
    }

    /**
     * 发送 HTTP 响应
     *
     * @return $this
     */
    public function send()
    {
        $this->sendHeaders();
        
        $this->sendContent();
        
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }

        return $this;
    }

    /**
     * 发送响应头
     *
     * @return $this
     */
    public function sendHeaders()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (headers_sent()) {
            return $this;
        }

        foreach ($this->headers->all() as $name => $value) {
            header($name . ': ' . $value, false, $this->statusCode);
        }

        // 状态码
        header(sprintf('HTTP/%s %s %s', $this->protocolVersion, $this->statusCode, $this->statusText), true, $this->statusCode);

        return $this;
    }

    /**
     * 发送响应内容
     *
     * @return $this
     */
    public function sendContent()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        echo $this->content;

        return $this;
    }

    /**
     * 设置内容
     *
     * @param mixed $content
     * @return $this
     */
    public function setContent($content)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->original = $content;

        if ($this->contentShouldJson($content)) {
            $this->setHeader('Content-Type', 'application/json');

            $this->isJson = true;

            $content = $this->contentToJson($content);
        }

        if (null !== $content && ! is_scalar($content) && ! is_callable([$content, '__toString'])) {
            throw new UnexpectedValueException(sprintf('The Response content must be a scalar or object implementing __toString(), %s given.', gettype($content)));
        }

        $this->content = (string) $content;

        return $this;
    }

    /**
     * 附加内容
     *
     * @param string $content
     * @return $this
     */
    public function appendContent(string $content = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->content = $this->getContent() . $content;

        return $this;
    }

    /**
     * 设置响应头
     *
     * @param string $key
     * @param string $value
     * @param bool $replace
     * @return $this
     */
    public function setHeader($key, $value, $replace = true)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if ($replace === true || ! $this->headers->has($key)) {
            $this->headers->set($key, $value);
        }

        return $this;
    }

    /**
     * 批量设置响应头
     *
     * @param array $headers
     * @return $this
     */
    public function withHeaders(array $headers)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        foreach ($headers as $key => $value) {
            $this->headers->set($key, $value);
        }

        return $this;
    }

    /**
     * 设置 COOKIE 别名
     *
     * @param string $name
     * @param string $value
     * @param array $option
     * @return $this
     */
    public function cookie($name, $value = '', array $option = [])
    {
        return $this->setCookie($name, $value, $option);
    }

    /**
     * 设置 COOKIE
     *
     * @param string $name
     * @param string $value
     * @param array $option
     * @return $this
     */
    public function setCookie($name, $value = '', array $option = [])
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (! static::$cookieResolver) {
            throw new InvalidArgumentException("Cookie resolver is not set.");
        }

        $cookie = call_user_func(static::$cookieResolver);
        $cookie->set($name, $value, $option);

        return $this;
    }

    /**
     * 批量设置 COOKIE
     *
     * @param array $cookies
     * @param array $option
     * @return $this
     */
    public function withCookies(array $cookies, array $option = [])
    {
        if ($this->checkTControl()) {
            return $this;
        }

        foreach ($cookies as $key => $value) {
            $this->setCookie($key, $value, $option);
        }

        return $this;
    }

    /**
     * 取回 JSON 数据
     *
     * @param bool $assoc
     * @param int $depth
     * @return mixed
     */
    public function getData(bool $assoc = true, int $depth = 512)
    {
        if ($this->isJson) {
            return json_decode($this->content, $assoc, $depth);
        }

        return $this->content;
    }

    /**
     * 设置 JSON 数据
     *
     * @param mixed $data
     * @param int $encodingOptions
     * @return $this
     */
    public function setData($data = [], $encodingOptions = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->original = $data;

        if ($encodingOptions === null) {
           $encodingOptions = 256; 
        }

        if ($data instanceof IArray) {
            $data = json_encode($data->toArray(), $encodingOptions);
        } elseif ($data instanceof IJson) {
            $data = $data->toJson($encodingOptions);
        } elseif ($data instanceof JsonSerializable) {
            $data = json_encode($data->jsonSerialize(), $encodingOptions);
        } else {
            $data = json_encode($data, $encodingOptions);
        }

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidArgumentException(json_last_error_msg());
        }

        $this->content = (string) $data;

        return $this;
    }

    /**
     * 获取内容
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * 获取内容
     *
     * @return string
     */
    public function content()
    {
        return $this->getContent();
    }

    /**
     * 获取原始内容
     *
     * @return string
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * 设置 HTTP 协议版本 (1.0 or 1.1).
     *
     * @param string $protocolVersion
     * @return $this
     */
    public function setProtocolVersion(string $protocolVersion)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->protocolVersion = $protocolVersion;

        return $this;
    }

    /**
     * 获取 HTTP 协议版本
     *
     * @final
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * 设置相应状态码
     * 
     * @param int $code
     * @param mixed $text
     * @return $this
     */
    public function setStatusCode(int $code, $text = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->statusCode = $code;

        if ($this->isInvalid()) {
            throw new InvalidArgumentException(sprintf('The HTTP status code %s is not valid.', $code));
        }

        if (null === $text) {
            $this->statusText = self::$statusTexts[$code] ?? 'unknown status';
            return $this;
        }

        if (false === $text) {
            $this->statusText = '';
            return $this;
        }

        $this->statusText = $text;

        return $this;
    }

    /**
     * 获取状态码
     *
     * @return int
     */
    public function status(): int
    {
        return $this->getStatusCode();
    }

    /**
     * 获取状态码
     *
     * @final
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * 编码设置
     *
     * @param string $charset
     * @return $this
     */
    public function setCharset(string $charset)
    {
        return $this->charset($charset);
    }

    /**
     * 编码设置
     *
     * @param string $charset
     * @return $this
     */
    public function charset(string $charset)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->charset = $charset;

        return $this;
    }

    /**
     * 获取编码
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * 设置过期时间
     *
     * @param \DateTime $datetime
     * @return $this
     */
    public function setExpires(DateTime $datetime = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (is_null($datetime)) {
            $this->headers->remove('Expires');
            return $this;
        }

        $this->setHeader('Expires', $this->normalizeDateTime($datetime));

        return $this;
    }

    /**
     * 设置最后修改时间
     *
     * @param \DateTime $datetime
     * @return $this
     */
    public function setLastModified(DateTime $datetime = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (is_null($datetime)) {
            $this->headers->remove('Last-Modified');
            return $this;
        }

        $this->setHeader('Last-Modified', $this->normalizeDateTime($datetime));

        return $this;
    }

    /**
     * 设置缓存
     *
     * @param int $minutes
     * @return $this
     */
    public function setCache(int $minutes)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $date = new DateTime();
        $date->modify('+' . $minutes . 'minutes');

        $this->setExpires($date);
        $this->setHeader('Cache-Control', 'max-age=' . ($minutes * 60));

        return $this;
    }

    /**
     * 设置响应未修改
     *
     * @return $this
     */
    public function setNotModified()
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setStatusCode(304, self::$statusTexts[304]);

        return $this;
    }

    /**
     * 设置响应内容类型
     *
     * @param string $contentType
     * @param string $charset
     * @return $this
     */
    public function setContentType(string $contentType, $charset = null)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        if (is_null($charset)) {
            $charset = $this->getCharset();
        }

        if ($charset === null) {
            $this->setHeader('Content-Type', $contentType);
        } else {
            $this->setHeader('Content-Type', $contentType . '; charset=' . $charset);
        }

        return $this;
    }

    /**
     * 设置响应内容长度
     *
     * @param int $contentLength
     * @return $this
     */
    public function setContentLength(int $contentLength)
    {
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setHeader('Content-Length', $contentLength);

        return $this;
    }

    /**
     * 设置自定义标识符
     *
     * @param string $etag
     * @return $this
     */
    public function setEtag(string $etag)
    {   
        if ($this->checkTControl()) {
            return $this;
        }

        $this->setHeader('Etag', $etag);

        return $this;
    }

    /**
     * 响应是否为 JSON
     *
     * @return bool
     */
    public function isJson(): bool
    {
        return $this->isJson;
    }

    /**
     * 响应是否正确
     *
     * @return bool
     */
    public function isInvalid(): bool
    {
        return $this->statusCode < 100 || $this->statusCode >= 600;
    }

    /**
     * 是否为信息性响应
     *
     * @return bool
     */
    public function isInformational(): bool
    {
        return $this->statusCode >= 100 && $this->statusCode < 200;
    }

    /**
     * 是否为正确响应
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * 是否为重定向响应
     *
     * @return bool
     */
    public function isRedirection(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * 是否为客户端错误响应
     *
     * @return bool
     */
    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * 是否为服务端错误响应
     *
     * @return bool
     */
    public function isServerError(): bool
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }

    /**
     * 是否为正常响应
     *
     * @return bool
     */
    public function isOk(): bool
    {
        return 200 === $this->statusCode;
    }

    /**
     * 是否为受限响应
     *
     * @return bool
     */
    public function isForbidden(): bool
    {
        return 403 === $this->statusCode;
    }

    /**
     * 是否为 404 NOT FOUND
     *
     * @return bool
     */
    public function isNotFound(): bool
    {
        return 404 === $this->statusCode;
    }

    /**
     * 是否为表单重定向响应
     *
     * @return bool
     */
    public function isRedirect(string $location = null): bool
    {
        return in_array($this->statusCode, [
            201, 
            301, 
            302, 
            303, 
            307, 
            308
        ]) 
            && (null === $location ?: $location == $this->headers->get('Location'));
    }

    /**
     * 是否为空响应
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return in_array($this->statusCode, [
            204, 
            304
        ]);
    }

    /**
     * 格式化响应时间
     *
     * @param \DateTime $datetime
     * @return string
     */
    protected function normalizeDateTime(DateTime $datetime)
    {
        $date = clone $datetime;

        $date->setTimezone(new DateTimeZone('UTC'));

        return $date->format('D, d M Y H:i:s') . ' GMT';
    }

    /**
     * 内容转换为 JSON
     *
     * @param mixed $content
     * @return string
     */
    protected function contentToJson($content)
    {
        if ($content instanceof IJson) {
            return $content->toJson();
        }

        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 可以转换为 JSON
     *
     * @param mixed $content
     * @return bool
     */
    protected function contentShouldJson($content)
    {
        return $content instanceof IJson ||
               $content instanceof ArrayObject ||
               $content instanceof JsonSerializable ||
               is_array($content);
    }
}
