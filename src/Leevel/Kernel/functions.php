<?php

declare(strict_types=1);

use Leevel\Di\Container;
use Leevel\Kernel\Proxy\App as ProxyApp;
use Leevel\Kernel\RoadRunnerDump;

/**
 * 代理 app 别名.
 */
class App extends ProxyApp
{
}

/**
 * 代理 app 别名.
 */
class Leevel extends ProxyApp
{
}

if (!function_exists('__')) {
    /**
     * 获取语言.
     */
    function __(string $text, ...$data): string
    {
        /** @var \Leevel\I18n\I18n $service */
        $service = Container::singletons()->make('i18n');

        return $service->gettext($text, ...$data);
    }
}

if (!function_exists('url')) {
    /**
     * 生成路由地址.
     */
    function url(string $url, array $params = [], string $subdomain = 'www', $suffix = null): string
    {
        /** @var \Leevel\Router\IUrl $service */
        $service = Container::singletons()->make('url');

        return $service->make($url, $params, $subdomain, $suffix);
    }
}

if (!function_exists('rr_dump')) {
    /**
     * 调试 RoadRunner 变量.
     */
    function rr_dump(mixed $var, ...$moreVars): mixed
    {
        return RoadRunnerDump::handle($var, ...$moreVars);
    }
}
