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
use Leevel\Http\Request;
use Leevel\Router\Url as BaseUrl;

/**
 * 代理 url.
 *
 * @codeCoverageIgnore
 */
class Url
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
     * 生成路由地址.
     *
     * @param null|bool|string $suffix
     */
    public static function make(string $url, array $params = [], string $subdomain = 'www', $suffix = null): string
    {
        return self::proxy()->make($url, $params, $subdomain, $suffix);
    }

    /**
     * 返回 HTTP 请求.
     */
    public static function getRequest(): Request
    {
        return self::proxy()->getRequest();
    }

    /**
     * 获取域名.
     */
    public static function getDomain(): string
    {
        return self::proxy()->getDomain();
    }

    /**
     * 代理服务.
     */
    public static function proxy(): BaseUrl
    {
        return Container::singletons()->make('url');
    }
}
