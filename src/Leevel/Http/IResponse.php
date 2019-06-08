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

use DateTime;

/**
 * HTTP 响应接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.27
 * @see https://baike.baidu.com/item/HTTP%E7%8A%B6%E6%80%81%E7%A0%81/5053660?fr=aladdin
 * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
 *
 * @version 1.0
 */
interface IResponse
{
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
     * 创建一个响应.
     *
     * @param mixed $content
     * @param int   $status
     * @param array $headers
     *
     * @return static
     */
    public static function create($content = '', int $status = 200, array $headers = []): self;

    /**
     * 发送 HTTP 响应.
     *
     * @return \Leevel\Http\IResponse
     */
    public function send(): self;

    /**
     * 发送响应头.
     *
     * @return \Leevel\Http\IResponse
     */
    public function sendHeaders(): self;

    /**
     * 发送响应内容.
     *
     * @return \Leevel\Http\IResponse
     */
    public function sendContent(): self;

    /**
     * 设置内容.
     *
     * @param mixed $content
     *
     * @return \Leevel\Http\IResponse
     */
    public function setContent($content): self;

    /**
     * 附加内容.
     *
     * @param string $content
     *
     * @return \Leevel\Http\IResponse
     */
    public function appendContent(?string $content = null): self;

    /**
     * 设置响应头.
     *
     * @param string $key
     * @param string $value
     * @param bool   $replace
     *
     * @return \Leevel\Http\IResponse
     */
    public function setHeader(string $key, string $value, bool $replace = true): self;

    /**
     * 批量设置响应头.
     *
     * @param array $headers
     *
     * @return \Leevel\Http\IResponse
     */
    public function withHeaders(array $headers): self;

    /**
     * 设置 COOKIE 别名.
     *
     * @param string $name
     * @param string $value
     * @param array  $option
     *
     * @return \Leevel\Http\IResponse
     */
    public function cookie(string $name, string $value = '', array $option = []): self;

    /**
     * 设置 COOKIE.
     *
     * @param string $name
     * @param string $value
     * @param array  $option
     *
     * @return \Leevel\Http\IResponse
     */
    public function setCookie(string $name, string $value = '', array $option = []): self;

    /**
     * 批量设置 COOKIE.
     *
     * @param array $cookies
     * @param array $option
     *
     * @return \Leevel\Http\IResponse
     */
    public function withCookies(array $cookies, array $option = []): self;

    /**
     * 获取 COOKIE.
     *
     * @return array
     */
    public function getCookies(): array;

    /**
     * 取回 JSON 数据.
     *
     * @param bool $assoc
     * @param int  $depth
     *
     * @return mixed
     */
    public function getData(bool $assoc = true, int $depth = 512);

    /**
     * 设置 JSON 数据.
     *
     * @param mixed $data
     * @param int   $encodingOptions
     *
     * @return \Leevel\Http\IResponse
     */
    public function setData($data = [], ?int $encodingOptions = null): self;

    /**
     * 获取内容.
     *
     * @return mixed
     */
    public function getContent();

    /**
     * 获取内容.
     *
     * @return mixed
     */
    public function content();

    /**
     * 获取原始内容.
     *
     * @return mixed
     */
    public function getOriginal();

    /**
     * 设置 HTTP 协议版本 (1.0 or 1.1).
     *
     * @param string $protocolVersion
     *
     * @return \Leevel\Http\IResponse
     */
    public function setProtocolVersion(string $protocolVersion): self;

    /**
     * 获取 HTTP 协议版本.
     *
     * @return string
     */
    public function getProtocolVersion(): string;

    /**
     * 设置相应状态码.
     *
     * @param int    $code
     * @param string $text
     *
     * @return \Leevel\Http\IResponse
     */
    public function setStatusCode(int $code, ?string $text = null): self;

    /**
     * 获取状态码.
     *
     * @return int
     */
    public function status(): int;

    /**
     * 获取状态码.
     *
     * @return int
     */
    public function getStatusCode(): int;

    /**
     * 编码设置.
     *
     * @param string $charset
     *
     * @return \Leevel\Http\IResponse
     */
    public function setCharset(string $charset): self;

    /**
     * 编码设置.
     *
     * @param string $charset
     *
     * @return \Leevel\Http\IResponse
     */
    public function charset(string $charset): self;

    /**
     * 获取编码.
     *
     * @return string
     */
    public function getCharset(): ?string;

    /**
     * 设置过期时间.
     *
     * @param \DateTime $datetime
     *
     * @return \Leevel\Http\IResponse
     */
    public function setExpires(DateTime $datetime = null): self;

    /**
     * 设置最后修改时间.
     *
     * @param \DateTime $datetime
     *
     * @return \Leevel\Http\IResponse
     */
    public function setLastModified(DateTime $datetime = null): self;

    /**
     * 设置缓存.
     *
     * @param int $minutes
     *
     * @return \Leevel\Http\IResponse
     */
    public function setCache(int $minutes): self;

    /**
     * 设置响应未修改.
     *
     * @return \Leevel\Http\IResponse
     */
    public function setNotModified(): self;

    /**
     * 设置响应内容类型.
     *
     * @param string $contentType
     * @param string $charset
     *
     * @return \Leevel\Http\IResponse
     */
    public function setContentType(string $contentType, ?string $charset = null): self;

    /**
     * 设置响应内容长度.
     *
     * @param int $contentLength
     *
     * @return \Leevel\Http\IResponse
     */
    public function setContentLength(int $contentLength): self;

    /**
     * 设置自定义标识符.
     *
     * @param string $etag
     *
     * @return \Leevel\Http\IResponse
     */
    public function setEtag(string $etag): self;

    /**
     * 响应是否为 JSON.
     *
     * @return bool
     */
    public function isJson(): bool;

    /**
     * 响应是否正确.
     *
     * @return bool
     */
    public function isInvalid(): bool;

    /**
     * 是否为信息性响应.
     *
     * @return bool
     */
    public function isInformational(): bool;

    /**
     * 是否为正确响应.
     *
     * @return bool
     */
    public function isSuccessful(): bool;

    /**
     * 是否为重定向响应.
     *
     * @return bool
     */
    public function isRedirection(): bool;

    /**
     * 是否为客户端错误响应.
     *
     * @return bool
     */
    public function isClientError(): bool;

    /**
     * 是否为服务端错误响应.
     *
     * @return bool
     */
    public function isServerError(): bool;

    /**
     * 是否为正常响应.
     *
     * @return bool
     */
    public function isOk(): bool;

    /**
     * 是否为受限响应.
     *
     * @return bool
     */
    public function isForbidden(): bool;

    /**
     * 是否为 404 NOT FOUND.
     *
     * @return bool
     */
    public function isNotFound(): bool;

    /**
     * 是否为表单重定向响应.
     *
     * @return bool
     */
    public function isRedirect(?string $location = null): bool;

    /**
     * 是否为空响应.
     *
     * @return bool
     */
    public function isEmpty(): bool;
}
