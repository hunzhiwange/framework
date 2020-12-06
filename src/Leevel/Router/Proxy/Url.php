<?php

declare(strict_types=1);

namespace Leevel\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Router\Url as BaseUrl;

/**
 * 代理 url.
 *
 * @method static string make(string $url, array $params = [], string $subdomain = 'www', null|bool|string $suffix = null) 生成路由地址.
 * @method static \Leevel\Http\Request getRequest()                                                       返回 HTTP 请求.
 * @method static string getDomain()                                                                      获取域名.
 */
class Url
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
    public static function proxy(): BaseUrl
    {
        return Container::singletons()->make('url');
    }
}
