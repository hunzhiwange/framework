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
     * @param array  $args
     *
     * @return mixed
     */
    function hl(string $method, ...$args)
    {
        $map = [
            'benchmark' => 'Debug',
            'drr'       => 'Debug',
            'decrypt'   => 'Encryption',
            'encrypt'   => 'Encryption',
            'gettext'   => 'I18n',
            'app'       => 'Kernel',
            'url'       => 'Router',
            'flash'     => 'Session',
        ];

        $component = $map[$method] ?? ucfirst($method);
        $fn = 'Leevel\\'.$component.'\\Helper\\'.$method;

        return fn($fn, ...$args);
    }
}

if (!function_exists('app')) {
    /**
     * 返回 IOC 容器或者容器中的服务.
     *
     * 提升工程师用户体验，为常用服务加入返回类型.
     *
     * @param string $service
     * @param array  $args
     * @codeCoverageIgnore
     */
    function app(?string $service = 'app', array $args = [])
    {
        $container = Container::singletons();

        if (null === $service) {
            return $container;
        }

        switch ($service) {
            case 'app':
                /** @var \Leevel\Kernel\IApp $app */
                $app = $container->make('app');

                return $app;
            case 'auths':
                /** @var \Leevel\Auth\Manager $auths */
                $auths = $container->make('auths');

                return $auths;
            default:
                return $container->make($service);
        }
    }
}

if (!function_exists('__')) {
    /**
     * 获取语言.
     *
     * @param string $text
     * @param array  $arr
     *
     * @return string
     * @codeCoverageIgnore
     */
    function __(string $text, ...$arr): string
    {
        return Container::singletons()
            ->make('i18n')
            ->gettext($text, ...$arr);
    }
}

if (!function_exists('dump')) {
    /**
     * 调试变量.
     *
     * @param mixed $var
     * @param array $moreVars
     *
     * @return mixed
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
     * @param array $moreVars
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
        /** @var \Leevel\Kernel\IApp $app */
        $app = Container::singletons()->make('app');
        $container = $app->container();

        if (method_exists($app, $method)) {
            return $app->{$method}(...$args);
        }
        if (method_exists($container, $method)) {
            return $container->{$method}(...$args);
        }

        $method = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $method));

        return hl($method, ...$args);
    }

    /**
     * 取得应用的环境变量.支持 boolean, empty 和 null.
     *
     * @param mixed $name
     * @param mixed $defaults
     *
     * @return mixed
     */
    public static function env(string $name, $defaults = null)
    {
        return env($name, $defaults);
    }
}
