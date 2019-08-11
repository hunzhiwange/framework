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

use Leevel\Di\Container;
use Leevel\Kernel\IApp;
use Leevel\Kernel\Proxy\App as ProxyApp;

if (!function_exists('app')) {
    /**
     * 返回 IOC 容器.
     *
     * @return \Leevel\Kernel\IApp
     * @codeCoverageIgnore
     */
    function app(): IApp
    {
        return Container::singletons()->make('app');
    }
}

if (!function_exists('leevel')) {
    /**
     * 返回 IOC 容器.
     *
     * @return \Leevel\Di\Container
     * @codeCoverageIgnore
     */
    function leevel(): Container
    {
        return Container::singletons();
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

/**
 * 代理 app.
 */
class App extends ProxyApp
{
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
     * @throws \BadMethodCallException
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

        $e = sprintf('Method `%s` is not exits.', $method);

        throw new BadMethodCallException($e);
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
