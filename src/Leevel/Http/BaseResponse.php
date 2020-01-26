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
 * 基础 HTTP 响应.
 */
trait BaseResponse
{
    /**
     * 设置响应头.
     *
     * @param string|string[] $values
     */
    public function setHeader(string $key, $values, bool $replace = true): void
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
     *
     * @param null|array|string $value
     */
    public function setCookie(string $name, $value = null, array $option = []): void
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
