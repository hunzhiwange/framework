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
use Leevel\Router\Url as BaseUrl;

/**
 * 代理 url.
 *
 * @method static string make(string $url, array $params = [], string $subdomain = 'www', $suffix = null) 生成路由地址.
 * @method static \Leevel\Http\Request getRequest()                                                       返回 HTTP 请求.
 * @method static string getDomain()                                                                      获取域名.
 */
class Url
{
    /**
     * call.
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): BaseUrl
    {
        return Container::singletons()->make('url');
    }
}
