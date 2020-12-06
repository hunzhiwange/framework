<?php

declare(strict_types=1);

namespace Leevel\Http;

/**
 * 基础 HTTP 响应.
 */
trait BaseResponse
{
    /**
     * 设置响应头.
     */
    public function setHeader(string $key, string|array $values, bool $replace = true): void
    {
        $this->headers->set($key, $values, $replace);
    }

    /**
     * 批量设置响应头.
     */
    public function withHeaders(array $headers, bool $replace = true): void
    {
        foreach ($headers as $key => $value) {
            $this->headers->set($key, $value, $replace);
        }
    }

    /**
     * 设置 COOKIE.
     */
    public function setCookie(string $name, ?string $value = null, array $option = []): void
    {
        $this->headers->setCookie(CookieUtils::makeCookie($name, $value, $option));
    }

    /**
     * 批量设置 COOKIE.
     */
    public function withCookies(array $cookies, array $option = []): void
    {
        foreach ($cookies as $key => $value) {
            $this->setCookie($key, $value, $option);
        }
    }
}
