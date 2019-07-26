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

use Leevel\Debug\Dump;
use Leevel\Di\Container;

if (!function_exists('hl')) {
    /**
     * 助手函数调用.
     *
     * @param string $method
     * @param array  ...$args
     *
     * @return mixed
     */
    function hl(string $method, ...$args)
    {
        $map = [
            'benchmark'   => 'Debug',
            'drr'         => 'Debug',
            'decrypt'     => 'Encryption',
            'encrypt'     => 'Encryption',
            'gettext'     => 'I18n',
            'app'         => 'Kernel',
            'url'         => 'Router',
            'cache_set'   => 'Cache',
            'cache_get'   => 'Cache',
            'log_record'  => 'Log',
            'option_set'  => 'Option',
            'option_get'  => 'Option',
            'session_set' => 'Session',
            'session_get' => 'Session',
            'flash'       => 'Session',
            'flash_set'   => 'Session',
            'flash_get'   => 'Session',
        ];

        $component = $map[$method] ?? ucfirst($method);
        $fn = 'Leevel\\'.$component.'\\Helper\\'.$method;

        return f($fn, ...$args);
    }
}

if (!function_exists('app')) {
    /**
     * 返回 IOC 容器或者容器中的服务.
     *
     * - auths:\Leevel\Auth\Manager
     * - caches:\Leevel\Cache\Manager
     * - databases:\Leevel\Database\Manager
     * - debug:\Leevel\Debug\Debug
     * - di:\Leevel\Di\Container
     * - encryption:\Leevel\Encryption\Encryption
     * - event:\Leevel\Event\Dispatch
     * - filesystems:\Leevel\Filesystem\Manager
     * - request:\Leevel\Http\Request
     * - i18n:\Leevel\I18n\I18n
     * - app:\Leevel\Kernel\App
     * - logs:\Leevel\Log\Manager
     * - mails:\Leevel\Mail\Manager
     * - option:\Leevel\Option\Option
     * - router:\Leevel\Router\Router
     * - url:\Leevel\Router\Url
     * - redirect:\Leevel\Router\Redirect
     * - response:\Leevel\Router\ResponseFactory
     * - redirect:\Leevel\Router\Redirect
     * - redirect:\Leevel\Router\Redirect
     * - view:\Leevel\Router\View
     * - sessions:\Leevel\Session\Manager
     * - throttler:\Leevel\Throttler\Throttler
     * - validate:\Leevel\Validate\Validate
     * - redirect:\Leevel\Router\Redirect
     * - redirect:\Leevel\Router\Redirect
     * - view.views:\Leevel\View\Manager
     * - redis.pool:\Leevel\Cache\Redis\RedisPool
     * - mysql.pool:\Leevel\Database\Mysql\MysqlPool
     *
     * @param string $service
     * @param array  $args
     *
     * @return \Leevel\Auth\Manager|\Leevel\Cache\Manager|\Leevel\Cache\Redis\RedisPool|\Leevel\Database\Manager|\Leevel\Database\Mysql\MysqlPool|\Leevel\Debug\Debug|\Leevel\Di\Container|\Leevel\Encryption\Encryption|\Leevel\Event\Dispatch|\Leevel\Filesystem\Manager|\Leevel\Http\Request|\Leevel\I18n\I18n|\Leevel\Kernel\App|\Leevel\Log\Manager|\Leevel\Mail\Manager|\Leevel\Option\Option|\Leevel\Router\Redirect|\Leevel\Router\Redirect|\Leevel\Router\Redirect|\Leevel\Router\Redirect|\Leevel\Router\Redirect\|\Leevel\Router\ResponseFactory|\Leevel\Router\Router|\Leevel\Router\Url|\Leevel\Router\View|\Leevel\Session\Manager|\Leevel\Throttler\Throttler|\Leevel\Validate\Validate|\Leevel\View\Manager|mixed
     * @codeCoverageIgnore
     */
    function app(?string $service = 'app', array $args = [])
    {
        $container = Container::singletons();

        if (null === $service) {
            return $container;
        }

        return $container->make($service);
    }
}

if (!function_exists('__')) {
    /**
     * 获取语言.
     *
     * @param string $text
     * @param array  ...$data
     *
     * @return string
     * @codeCoverageIgnore
     */
    function __(string $text, ...$data): string
    {
        /** @var \Leevel\I18n\I18n $service */
        $service = Container::singletons()->make('i18n');

        return $service->gettext($text, ...$data);
    }
}

if (!function_exists('dump')) {
    /**
     * 调试变量.
     *
     * @param mixed $var
     * @param array ...$moreVars
     *
     * @return mixed
     * @codeCoverageIgnore
     */
    function dump($var, ...$moreVars)
    {
        return Dump::dump($var, ...$moreVars);
    }
}

if (!function_exists('dd')) {
    /**
     * 调试变量并中断.
     *
     * @param mixed $var
     * @param array ...$moreVars
     * @codeCoverageIgnore
     */
    function dd($var, ...$moreVars): void
    {
        Dump::dumpDie($var, ...$moreVars);
    }
}

/**
 * 函数库.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2016.11.26
 *
 * @version 1.0
 */
class Leevel
{
    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        /** @var \Leevel\Kernel\App $app */
        $app = Container::singletons()->make('app');

        if (method_exists($app, $method)) {
            return $app->{$method}(...$args);
        }

        if (($container = $app->container()) &&
            method_exists($container, $method)) {
            return $container->{$method}(...$args);
        }

        $method = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $method));

        return hl($method, ...$args);
    }

    /**
     * 取得应用的环境变量.支持 boolean, empty 和 null.
     *
     * @param mixed      $name
     * @param null|mixed $defaults
     *
     * @return mixed
     */
    public static function env(string $name, $defaults = null)
    {
        return env($name, $defaults);
    }
}
