<?php

declare(strict_types=1);

use Leevel\Di\Container;
use Leevel\Kernel\Proxy\App as ProxyApp;

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

if (!function_exists('func')) {
    /**
     * 执行惰性加载函数.
     */
    function func(Closure $fn, ...$args): mixed
    {
        try {
            return $fn(...$args);
        } catch (Error $e) {
            if (false !== strpos($message = $e->getMessage(), $error = 'Call to undefined function ')) {
                class_exists($fnName = str_replace([$error, '()'], '', $message));
                if (!func_exists($fnName)) {
                    throw $e;
                }

                return $fn(...$args);
            }

            throw $e;
        }
    }
}

if (!function_exists('func_exists')) {
    /**
     * 判断惰性加载函数是否存在.
     *
     * - 系统会自动搜索并导入函数文件.
     */
    function func_exists(string $fn): bool
    {
        if (function_exists($fn)) {
            return true;
        }

        $virtualClass = '';
        foreach (['fn', 'prefix', 'index'] as $type) {
            switch ($type) {
                case 'fn':
                    $virtualClass = $fn;

                    break;
                case 'prefix':
                    if (false === $position = strpos($fn, '_')) {
                        continue 2;
                    }

                    $virtualClass = substr($fn, 0, $position);

                    break;
                default:
                    if (false === $position = strripos($fn, '\\')) {
                        continue 2;
                    }

                    $virtualClass = substr($fn, 0, $position).'\\index';

                    break;
            }

            class_exists($virtualClass);
            if (function_exists($fn)) {
                return true;
            }
        }

        return false;
    }
}
