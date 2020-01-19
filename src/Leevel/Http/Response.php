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

use ArrayObject;
use DateTime;
use DateTimeZone;
use InvalidArgumentException;
use JsonSerializable;
use Leevel\Flow\FlowControl;
use Leevel\Support\IArray;
use Leevel\Support\IJson;
use UnexpectedValueException;

/**
 * HTTP 响应.
 */
class Response
{
    use FlowControl;

    /**
     * HTTP_CONTINUE.
     *
     * @var int
     */
    const HTTP_CONTINUE = 100;

    /**
     * HTTP_SWITCHING_PROTOCOLS.
     *
     * @var int
     */
    const HTTP_SWITCHING_PROTOCOLS = 101;

    /**
     * HTTP_PROCESSING (RFC2518).
     *
     * @var int
     */
    const HTTP_PROCESSING = 102;

    /**
     * HTTP_OK.
     *
     * @var int
     */
    const HTTP_OK = 200;

    /**
     * HTTP_CREATED.
     *
     * @var int
     */
    const HTTP_CREATED = 201;

    /**
     * HTTP_ACCEPTED.
     *
     * @var int
     */
    const HTTP_ACCEPTED = 202;

    /**
     * HTTP_NON_AUTHORITATIVE_INFORMATION.
     *
     * @var int
     */
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;

    /**
     * HTTP_NO_CONTENT.
     *
     * @var int
     */
    const HTTP_NO_CONTENT = 204;

    /**
     * HTTP_RESET_CONTENT.
     *
     * @var int
     */
    const HTTP_RESET_CONTENT = 205;

    /**
     * HTTP_PARTIAL_CONTENT.
     *
     * @var int
     */
    const HTTP_PARTIAL_CONTENT = 206;

    /**
     * HTTP_MULTI_STATUS (RFC4918).
     *
     * @var int
     */
    const HTTP_MULTI_STATUS = 207;

    /**
     * HTTP_ALREADY_REPORTED (RFC5842).
     *
     * @var int
     */
    const HTTP_ALREADY_REPORTED = 208;

    /**
     * HTTP_IM_USED (RFC3229).
     *
     * @var int
     */
    const HTTP_IM_USED = 226;

    /**
     * HTTP_MULTIPLE_CHOICES.
     *
     * @var int
     */
    const HTTP_MULTIPLE_CHOICES = 300;

    /**
     * HTTP_MOVED_PERMANENTLY.
     *
     * @var int
     */
    const HTTP_MOVED_PERMANENTLY = 301;

    /**
     * HTTP_FOUND.
     *
     * @var int
     */
    const HTTP_FOUND = 302;

    /**
     * HTTP_SEE_OTHER.
     *
     * @var int
     */
    const HTTP_SEE_OTHER = 303;

    /**
     * HTTP_NOT_MODIFIED.
     *
     * @var int
     */
    const HTTP_NOT_MODIFIED = 304;

    /**
     * HTTP_USE_PROXY.
     *
     * @var int
     */
    const HTTP_USE_PROXY = 305;

    /**
     * HTTP_RESERVED.
     *
     * @var int
     */
    const HTTP_RESERVED = 306;

    /**
     * HTTP_TEMPORARY_REDIRECT.
     *
     * @var int
     */
    const HTTP_TEMPORARY_REDIRECT = 307;

    /**
     * HTTP_PERMANENTLY_REDIRECT (RFC7238).
     *
     * @var int
     */
    const HTTP_PERMANENTLY_REDIRECT = 308;

    /**
     * HTTP_BAD_REQUEST.
     *
     * @var int
     */
    const HTTP_BAD_REQUEST = 400;

    /**
     * HTTP_UNAUTHORIZED.
     *
     * @var int
     */
    const HTTP_UNAUTHORIZED = 401;

    /**
     * HTTP_PAYMENT_REQUIRED.
     *
     * @var int
     */
    const HTTP_PAYMENT_REQUIRED = 402;

    /**
     * HTTP_FORBIDDEN.
     *
     * @var int
     */
    const HTTP_FORBIDDEN = 403;

    /**
     * HTTP_NOT_FOUND.
     *
     * @var int
     */
    const HTTP_NOT_FOUND = 404;

    /**
     * HTTP_METHOD_NOT_ALLOWED.
     *
     * @var int
     */
    const HTTP_METHOD_NOT_ALLOWED = 405;

    /**
     * HTTP_NOT_ACCEPTABLE.
     *
     * @var int
     */
    const HTTP_NOT_ACCEPTABLE = 406;

    /**
     * HTTP_PROXY_AUTHENTICATION_REQUIRED.
     *
     * @var int
     */
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;

    /**
     * HTTP_REQUEST_TIMEOUT.
     *
     * @var int
     */
    const HTTP_REQUEST_TIMEOUT = 408;

    /**
     * HTTP_CONFLICT.
     *
     * @var int
     */
    const HTTP_CONFLICT = 409;

    /**
     * HTTP_GONE.
     *
     * @var int
     */
    const HTTP_GONE = 410;

    /**
     * HTTP_LENGTH_REQUIRED.
     *
     * @var int
     */
    const HTTP_LENGTH_REQUIRED = 411;

    /**
     * HTTP_PRECONDITION_FAILED.
     *
     * @var int
     */
    const HTTP_PRECONDITION_FAILED = 412;

    /**
     * HTTP_REQUEST_ENTITY_TOO_LARGE.
     *
     * @var int
     */
    const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;

    /**
     * HTTP_REQUEST_URI_TOO_LONG.
     *
     * @var int
     */
    const HTTP_REQUEST_URI_TOO_LONG = 414;

    /**
     * HTTP_UNSUPPORTED_MEDIA_TYPE.
     *
     * @var int
     */
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;

    /**
     * HTTP_REQUESTED_RANGE_NOT_SATISFIABLE.
     *
     * @var int
     */
    const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;

    /**
     * HTTP_EXPECTATION_FAILED.
     *
     * @var int
     */
    const HTTP_EXPECTATION_FAILED = 417;

    /**
     * HTTP_I_AM_A_TEAPOT (RFC2324).
     *
     * @var int
     */
    const HTTP_I_AM_A_TEAPOT = 418;

    /**
     * HTTP_MISDIRECTED_REQUEST (RFC7540).
     *
     * @var int
     */
    const HTTP_MISDIRECTED_REQUEST = 421;

    /**
     * HTTP_UNPROCESSABLE_ENTITY (RFC4918).
     *
     * @var int
     */
    const HTTP_UNPROCESSABLE_ENTITY = 422;

    /**
     * HTTP_LOCKED (RFC4918).
     *
     * @var int
     */
    const HTTP_LOCKED = 423;

    /**
     * HTTP_FAILED_DEPENDENCY (RFC4918).
     *
     * @var int
     */
    const HTTP_FAILED_DEPENDENCY = 424;

    /**
     * HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL (RFC2817).
     *
     * @var int
     */
    const HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL = 425;

    /**
     * HTTP_UPGRADE_REQUIRED (RFC2817).
     *
     * @var int
     */
    const HTTP_UPGRADE_REQUIRED = 426;

    /**
     * HTTP_PRECONDITION_REQUIRED (RFC6585).
     *
     * @var int
     */
    const HTTP_PRECONDITION_REQUIRED = 428;

    /**
     * HTTP_TOO_MANY_REQUESTS (RFC6585).
     *
     * @var int
     */
    const HTTP_TOO_MANY_REQUESTS = 429;

    /**
     * HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE (RFC6585).
     *
     * @var int
     */
    const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;

    /**
     * HTTP_UNAVAILABLE_FOR_LEGAL_REASONS.
     *
     * @var int
     */
    const HTTP_UNAVAILABLE_FOR_LEGAL_REASONS = 451;

    /**
     * HTTP_INTERNAL_SERVER_ERROR.
     *
     * @var int
     */
    const HTTP_INTERNAL_SERVER_ERROR = 500;

    /**
     * HTTP_NOT_IMPLEMENTED.
     *
     * @var int
     */
    const HTTP_NOT_IMPLEMENTED = 501;

    /**
     * HTTP_BAD_GATEWAY.
     *
     * @var int
     */
    const HTTP_BAD_GATEWAY = 502;

    /**
     * HTTP_SERVICE_UNAVAILABLE.
     *
     * @var int
     */
    const HTTP_SERVICE_UNAVAILABLE = 503;

    /**
     * HTTP_GATEWAY_TIMEOUT.
     *
     * @var int
     */
    const HTTP_GATEWAY_TIMEOUT = 504;

    /**
     * HTTP_VERSION_NOT_SUPPORTED.
     *
     * @var int
     */
    const HTTP_VERSION_NOT_SUPPORTED = 505;

    /**
     * HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL (RFC2295).
     *
     * @var int
     */
    const HTTP_VARIANT_ALSO_NEGOTIATES_EXPERIMENTAL = 506;

    /**
     * HTTP_INSUFFICIENT_STORAGE (RFC4918).
     *
     * @var int
     */
    const HTTP_INSUFFICIENT_STORAGE = 507;

    /**
     * HTTP_LOOP_DETECTED (RFC5842).
     *
     * @var int
     */
    const HTTP_LOOP_DETECTED = 508;

    /**
     * HTTP_NOT_EXTENDED (RFC2774).
     *
     * @var int
     */
    const HTTP_NOT_EXTENDED = 510;

    /**
     * HTTP_NETWORK_AUTHENTICATION_REQUIRED (RFC6585).
     *
     * @var int
     */
    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;

    /**
     * 响应头.
     *
     * @var \Leevel\Http\ResponseHeaderBag
     */
    public ResponseHeaderBag $headers;

    /**
     * 原生响应内容.
     *
     * @var mixed
     */
    public $original;

    /**
     * 状态码.
     *
     * @see http://www.iana.org/assignments/http-status-codes/
     *
     * @var array
     */
    public static array $statusTexts = [
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
     * 响应内容.
     *
     * @var string
     */
    protected ?string $content = null;

    /**
     * HTTP 协议版本.
     *
     * @var string
     */
    protected string $protocolVersion;

    /**
     * 状态码.
     *
     * @var int
     */
    protected int $statusCode;

    /**
     * 状态码内容.
     *
     * @var string
     */
    protected string $statusText;

    /**
     * 字符编码.
     *
     * @var string
     */
    protected ?string $charset = null;

    /**
     * 是否为 JSON.
     *
     * @var bool
     */
    protected bool $isJson = false;

    /**
     * 构造函数.
     *
     * - This class borrows heavily from the Symfony4 Framework and is part of the symfony package.
     *
     * @see Symfony\Component\HttpFoundation (https://github.com/symfony/symfony)
     *
     * @param string $content
     */
    public function __construct($content = '', int $status = 200, array $headers = [])
    {
        $this->headers = new ResponseHeaderBag($headers);
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->setProtocolVersion('1.0');
    }

    /**
     * 创建一个响应.
     *
     * @param mixed $content
     *
     * @return static
     */
    public static function create($content = '', int $status = 200, array $headers = []): self
    {
        return new static($content, $status, $headers);
    }

    /**
     * 发送 HTTP 响应.
     *
     * @return \Leevel\Http\Response
     */
    public function send(): self
    {
        $this->sendHeaders();
        $this->sendContent();

        return $this;
    }

    /**
     * 发送响应头.
     *
     * @return \Leevel\Http\Response
     */
    public function sendHeaders(): self
    {
        if (headers_sent()) {
            return $this;
        }

        // @codeCoverageIgnoreStart
        foreach ($this->headers->all() as $name => $value) {
            header($name.': '.$value, false, $this->statusCode);
        }

        // 状态码
        header(sprintf('HTTP/%s %s %s', $this->protocolVersion, $this->statusCode, $this->statusText), true, $this->statusCode);

        // COOKIE
        foreach ($this->getCookies() as $item) {
            setcookie(...$item);
        }

        return $this;
        // @codeCoverageIgnoreEnd
    }

    /**
     * 发送响应内容.
     *
     * @return \Leevel\Http\Response
     */
    public function sendContent(): self
    {
        echo $this->content;

        return $this;
    }

    /**
     * 设置内容.
     *
     * @param mixed $content
     *
     * @throws \UnexpectedValueException
     *
     * @return \Leevel\Http\Response
     */
    public function setContent($content): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->original = $content;

        if ($this->contentShouldJson($content)) {
            $this->setHeader('Content-Type', 'application/json');
            $this->isJson = true;
            $content = $this->contentToJson($content);
        }

        if (null !== $content &&
            !is_scalar($content) &&
            !is_callable([$content, '__toString'])) {
            $e = sprintf('The Response content must be a scalar or object implementing __toString(), %s given.', gettype($content));

            throw new UnexpectedValueException($e);
        }

        $this->content = (string) $content;

        return $this;
    }

    /**
     * 附加内容.
     *
     * @return \Leevel\Http\Response
     */
    public function appendContent(?string $content = null): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->content = $this->getContent().$content;

        return $this;
    }

    /**
     * 设置响应头.
     *
     * @return \Leevel\Http\Response
     */
    public function setHeader(string $key, string $value, bool $replace = true): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (true === $replace || !$this->headers->has($key)) {
            $this->headers->set($key, $value);
        }

        return $this;
    }

    /**
     * 批量设置响应头.
     *
     * @return \Leevel\Http\Response
     */
    public function withHeaders(array $headers): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        foreach ($headers as $key => $value) {
            $this->headers->set($key, $value);
        }

        return $this;
    }

    /**
     * 设置 COOKIE 别名.
     *
     * @param null|array|string $value
     *
     * @return \Leevel\Http\Response
     */
    public function cookie(string $name, $value = null, array $option = []): self
    {
        return $this->setCookie($name, $value, $option);
    }

    /**
     * 设置 COOKIE.
     *
     * @param null|array|string $value
     *
     * @return \Leevel\Http\Response
     */
    public function setCookie(string $name, $value = null, array $option = []): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->headers->setCookie($name, $value, $option);

        return $this;
    }

    /**
     * 批量设置 COOKIE.
     *
     * @return \Leevel\Http\Response
     */
    public function withCookies(array $cookies, array $option = []): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        foreach ($cookies as $key => $value) {
            $this->setCookie($key, $value, $option);
        }

        return $this;
    }

    /**
     * 获取 COOKIE.
     */
    public function getCookies(): array
    {
        return $this->headers->getCookies();
    }

    /**
     * 取回 JSON 数据.
     *
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
     * 设置 JSON 数据.
     *
     * @param mixed $data
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Http\Response
     */
    public function setData($data = [], ?int $encodingOptions = null): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->original = $data;

        if (null === $encodingOptions) {
            $encodingOptions = JSON_UNESCAPED_UNICODE;
        }

        if ($data instanceof IArray) {
            $data = json_encode($data->toArray(), $encodingOptions);
        } elseif (is_object($data) && $data instanceof IJson) {
            $data = $data->toJson($encodingOptions);
        } elseif (is_object($data) && $data instanceof JsonSerializable) {
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
     * 获取内容.
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * 获取内容.
     *
     * @return mixed
     */
    public function content()
    {
        return $this->getContent();
    }

    /**
     * 获取原始内容.
     *
     * @return mixed
     */
    public function getOriginal()
    {
        return $this->original;
    }

    /**
     * 设置 HTTP 协议版本 (1.0 or 1.1).
     *
     * @return \Leevel\Http\Response
     */
    public function setProtocolVersion(string $protocolVersion): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->protocolVersion = $protocolVersion;

        return $this;
    }

    /**
     * 获取 HTTP 协议版本.
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * 设置响应状态码.
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Http\Response
     */
    public function setStatusCode(int $code, ?string $text = null): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->statusCode = $code;

        if ($this->isInvalid()) {
            $e = sprintf('The HTTP status code %s is not valid.', $code);

            throw new InvalidArgumentException($e);
        }

        if (null === $text) {
            $this->statusText = self::$statusTexts[$code] ?? 'unknown status';

            return $this;
        }

        $this->statusText = $text;

        return $this;
    }

    /**
     * 获取状态码.
     */
    public function status(): int
    {
        return $this->getStatusCode();
    }

    /**
     * 获取状态码.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * 设置编码.
     *
     * @return \Leevel\Http\Response
     */
    public function setCharset(string $charset): self
    {
        return $this->charset($charset);
    }

    /**
     * 设置设置.
     *
     * @return \Leevel\Http\Response
     */
    public function charset(string $charset): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->charset = $charset;

        return $this;
    }

    /**
     * 获取编码.
     *
     * @return string
     */
    public function getCharset(): ?string
    {
        return $this->charset;
    }

    /**
     * 设置过期时间.
     *
     * @return \Leevel\Http\Response
     */
    public function setExpires(?DateTime $datetime = null): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (null === $datetime) {
            $this->headers->remove('Expires');

            return $this;
        }

        $this->setHeader('Expires', $this->normalizeDateTime($datetime));

        return $this;
    }

    /**
     * 设置最后修改时间.
     *
     * @return \Leevel\Http\Response
     */
    public function setLastModified(?DateTime $datetime = null): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (null === $datetime) {
            $this->headers->remove('Last-Modified');

            return $this;
        }

        $this->setHeader('Last-Modified', $this->normalizeDateTime($datetime));

        return $this;
    }

    /**
     * 设置缓存.
     *
     * @return \Leevel\Http\Response
     */
    public function setCache(int $minutes): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $date = new DateTime();
        $date->modify('+'.$minutes.'minutes');
        $this->setExpires($date);
        $this->setHeader('Cache-Control', 'max-age='.($minutes * 60));

        return $this;
    }

    /**
     * 设置响应未修改.
     *
     * @return \Leevel\Http\Response
     */
    public function setNotModified(): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->setStatusCode(304, self::$statusTexts[304]);

        return $this;
    }

    /**
     * 设置响应内容类型.
     *
     * @return \Leevel\Http\Response
     */
    public function setContentType(string $contentType, ?string $charset = null): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        if (null === $charset) {
            $charset = $this->getCharset();
        }

        if (null === $charset) {
            $this->setHeader('Content-Type', $contentType);
        } else {
            $this->setHeader('Content-Type', $contentType.'; charset='.$charset);
        }

        return $this;
    }

    /**
     * 设置响应内容长度.
     *
     * @return \Leevel\Http\Response
     */
    public function setContentLength(int $contentLength): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->setHeader('Content-Length', (string) $contentLength);

        return $this;
    }

    /**
     * 设置自定义标识符.
     *
     * @return \Leevel\Http\Response
     */
    public function setEtag(string $etag): self
    {
        if ($this->checkFlowControl()) {
            return $this;
        }

        $this->setHeader('Etag', $etag);

        return $this;
    }

    /**
     * 响应是否为 JSON.
     */
    public function isJson(): bool
    {
        return $this->isJson;
    }

    /**
     * 响应是否为无效的.
     */
    public function isInvalid(): bool
    {
        return $this->statusCode < 100 || $this->statusCode >= 600;
    }

    /**
     * 是否为信息性响应.
     */
    public function isInformational(): bool
    {
        return $this->statusCode >= 100 && $this->statusCode < 200;
    }

    /**
     * 是否为正确响应.
     */
    public function isSuccessful(): bool
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * 是否为重定向响应.
     */
    public function isRedirection(): bool
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * 是否为客户端错误响应.
     */
    public function isClientError(): bool
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * 是否为服务端错误响应.
     */
    public function isServerError(): bool
    {
        return $this->statusCode >= 500 && $this->statusCode < 600;
    }

    /**
     * 是否为正常响应.
     */
    public function isOk(): bool
    {
        return 200 === $this->statusCode;
    }

    /**
     * 是否为受限响应.
     */
    public function isForbidden(): bool
    {
        return 403 === $this->statusCode;
    }

    /**
     * 是否为 404 NOT FOUND.
     */
    public function isNotFound(): bool
    {
        return 404 === $this->statusCode;
    }

    /**
     * 是否为表单重定向响应.
     */
    public function isRedirect(?string $location = null): bool
    {
        return in_array($this->statusCode, [
            201,
            301,
            302,
            303,
            307,
            308,
        ], true)
            && (null === $location ?: $location === $this->headers->get('Location'));
    }

    /**
     * 是否为空响应.
     */
    public function isEmpty(): bool
    {
        return in_array($this->statusCode, [
            204,
            304,
        ], true);
    }

    /**
     * 格式化响应时间.
     */
    protected function normalizeDateTime(DateTime $datetime): string
    {
        $date = clone $datetime;
        $date->setTimezone(new DateTimeZone('UTC'));

        return $date->format('D, d M Y H:i:s').' GMT';
    }

    /**
     * 内容转换为 JSON.
     *
     * @param mixed $content
     */
    protected function contentToJson($content): string
    {
        if ($content instanceof IJson) {
            return $content->toJson();
        }

        if ($content instanceof IArray) {
            $content = $content->toArray();
        }

        return json_encode($content, JSON_UNESCAPED_UNICODE);
    }

    /**
     * 可以转换为 JSON.
     *
     * @param mixed $content
     */
    protected function contentShouldJson($content): bool
    {
        return $content instanceof IJson ||
               $content instanceof IArray ||
               $content instanceof ArrayObject ||
               $content instanceof JsonSerializable ||
               is_array($content);
    }
}
