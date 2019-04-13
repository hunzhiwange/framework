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

use Leevel\Leevel\App;
use Leevel\Support\Fn;
use Leevel\Support\FunctionNotFoundException;
use function Leevel\Support\Str\un_camelize;

include dirname(__DIR__).'/Support/Str/un_camelize.php';

if (!function_exists('fn')) {
    /**
     * 自动导入函数.
     *
     * @param \Closure|string $fn
     * @param array           $args
     * @param mixed           $fn
     *
     * @return mixed
     */
    function fn($fn, ...$args)
    {
        static $instance, $loaded = [];

        if (is_string($fn) && in_array($fn, $loaded, true)) {
            return $fn(...$args);
        }

        if (null === $instance) {
            $instance = new Fn();
        }

        $result = $instance->__invoke($fn, ...$args);

        if (is_string($fn)) {
            $loaded[] = $fn;
        }

        return $result;
    }
}

if (!function_exists('h')) {
    /**
     * 助手函数调用.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    function h(string $method, ...$args)
    {
        $map = [
            'benchmark' => 'Debug',
            'dd'        => 'Debug',
            'drr'       => 'Debug',
            'dump'      => 'Debug',
            'decrypt'   => 'Encryption',
            'encrypt'   => 'Encryption',
            'gettext'   => 'I18n',
            'app'       => 'Kernel',
            'url'       => 'Router',
            'flash'     => 'Session',
            'env'       => 'Support',
            'value'     => 'Support',
        ];

        $component = $map[$method] ?? ucfirst($method);
        $fn = sprintf('\\Leevel\\%s\\Helper\\%s', $component, $method);

        return fn($fn, ...$args);
    }
}

if (!function_exists('app')) {
    /**
     * 返回应用容器或者注入.
     *
     * @param null|string $service
     * @param array       $args
     *
     * @return \Leevel\Leevel\App|mixed
     */
    function app(?string $service = null, array $args = [])
    {
        return fn('Leevel\\Kernel\\Helper\\app', $service, $args);
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
     */
    function __(string $text, ...$arr): string
    {
        return fn('Leevel\\I18n\\Helper\\gettext', $text, ...$arr);
    }
}

if (!function_exists('spl_object_id')) {
    /**
     * 兼容 7.2 spl_object_id.
     *
     * @param object $obj
     *
     * @return string
     */
    function spl_object_id($obj): string
    {
        return spl_object_hash($obj);
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
        $unCamelize = un_camelize($method);

        try {
            return h($unCamelize, ...$args);
        } catch (FunctionNotFoundException $th) {
            return App::singletons()->{$method}(...$args);
        }
    }
}
