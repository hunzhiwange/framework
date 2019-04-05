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

use Leevel\Di\IContainer;
use Leevel\Leevel\Project;
use Leevel\Log\ILog;
use Leevel\Support\Fn;

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
        return (new Fn())(function () use ($method, $args) {
            $fn = '\\Leevel\\Leevel\\Helper\\'.$method;

            return $fn(...$args);
        });
    }

    /**
     * 获取语言.
     *
     * @param string $text
     * @param array  $arr
     *
     * @return string
     */
    public static function __(string $text, ...$arr): string
    {
        static $i18n;

        if (null === $i18n) {
            if (!is_object($i18n = static::project('i18n'))) { /** @codeCoverageIgnore */
                $i18n = 'sprintf'; // @codeCoverageIgnore
            } else {
                $i18n = [$i18n, 'gettext'];
            }
        }

        array_unshift($arr, $text);

        return call_user_func_array($i18n, $arr);
    }

    /**
     * 返回项目容器或者注入.
     *
     * @param null|string $instance
     * @param array       $args
     *
     * @return \Leevel\Leevel\Project|mixed
     */
    public static function project(?string $instance = null, array $args = [])
    {
        if (null === $instance) {
            return static::singletons();
        }

        return static::singletons()->make($instance, $args);
    }

    /**
     * 返回项目容器或者注入
     * project 别名函数.
     *
     * @param null|string $instance
     * @param array       $args
     *
     * @return \Leevel\Leevel\Project|mixed
     */
    public static function app(?string $instance = null, array $args = [])
    {
        return static::project($instance, $args);
    }

    /**
     * 取得项目的环境变量.支持 boolean, empty 和 null.
     *
     * @param mixed $name
     * @param mixed $defaults
     *
     * @return mixed
     */
    public static function env(string $name, $defaults = null)
    {
        if (false === $value = getenv($name)) {
            $value = static::value($defaults);
        }

        switch ($value) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
        }

        if (is_string($value) && strlen($value) > 1 &&
            '"' === $value[0] && '"' === $value[strlen($value) - 1]) {
            return substr($value, 1, -1);
        }

        return $value;
    }

    /**
     * 返回默认值
     *
     * @param mixed $value
     *
     * @return mixed
     */
    public static function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }

    /**
     * 日志.
     *
     * @param null|string $message = null
     * @param array       $context
     * @param string      $level
     *
     * @return mixed
     */
    public static function log(?string $message = null, array $context = [], string $level = ILog::INFO)
    {
        if (null === $message) {
            return static::project('logs');
        }

        static::project('logs')->log($level, $message, $context);
    }

    /**
     * 设置或者获取 option 值
     *
     * @param null|array|string $key
     * @param mixed             $defaults
     *
     * @return mixed
     */
    public static function option($key = null, $defaults = null)
    {
        if (null === $key) {
            return static::project('option');
        }

        if (is_array($key)) {
            return static::project('option')->set($key);
        }

        return static::project('option')->get($key, $defaults);
    }

    /**
     * 设置或者获取 cache 值
     *
     * @param null|array|string $key
     * @param mixed             $defaults
     * @param array             $option
     *
     * @return mixed
     */
    public static function cache($key = null, $defaults = null, array $option = [])
    {
        if (null === $key) {
            return static::project('caches');
        }

        if (is_array($key)) {
            return static::project('caches')->put($key, null, $option);
        }

        return static::project('caches')->get($key, $defaults, $option);
    }

    /**
     * 设置或者获取 session 值
     *
     * @param null|array|string $key
     * @param mixed             $defaults
     *
     * @return mixed
     */
    public static function session($key = null, $defaults = null)
    {
        if (null === $key) {
            return static::project('sessions');
        }

        if (is_array($key)) {
            return static::project('sessions')->put($key);
        }

        return static::project('sessions')->get($key, $defaults);
    }

    /**
     * 设置或者获取 flash 值.
     *
     * @param null|array|string $key
     * @param mixed             $defaults
     *
     * @return mixed
     */
    public static function flash($key = null, $defaults = null)
    {
        if (null === $key) {
            return static::project('sessions');
        }

        if (is_array($key)) {
            return static::project('sessions')->flashs($key);
        }

        return static::project('sessions')->getFlash($key, $defaults);
    }

    /**
     * 生成路由地址
     *
     * @param string      $url
     * @param array       $params
     * @param string      $subdomain
     * @param bool|string $suffix
     *
     * @return string
     */
    public static function url(string $url, array $params = [], string $subdomain = 'www', $suffix = null): string
    {
        return static::project('url')->make($url, $params, $subdomain, $suffix);
    }

    /**
     * 获取语言.
     *
     * @param string $text
     * @param array  $arr
     *
     * @return string
     */
    public static function gettext(string $text, ...$arr): string
    {
        return static::__($text, ...$arr);
    }

    /**
     * 返回容器.
     *
     * @return \Leevel\Di\IContainer
     * @codeCoverageIgnore
     */
    protected static function singletons(): IContainer
    {
        return Project::singletons();
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
        return Leevel::__($text, ...$arr);
    }
}

if (!function_exists('gettext')) {
    /**
     * 获取语言.
     *
     * @param string $text
     * @param array  $arr
     *
     * @return string
     */
    function gettext(string $text, ...$arr): string
    {
        return Leevel::__($text, ...$arr);
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
        return Leevel::dump($var, ...$moreVars);
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
        Leevel::dd($var, ...$moreVars);
    }
}

if (!function_exists('db')) {
    /**
     * 调试栈信息.
     */
    function db(): void
    {
        Leevel::backtrace();
    }
}

if (!function_exists('drr')) {
    /**
     * 调试 RoadRunner 变量.
     *
     * @param mixed $var
     * @param array $moreVars
     *
     * @return mixed
     */
    function drr($var, ...$moreVars)
    {
        return Leevel::drr($var, ...$moreVars);
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

if (!function_exists('debug_start')) {
    /**
     * Debug 标记.
     *
     * @param string $tag
     * @codeCoverageIgnore
     */
    function debug_start(string $tag): void
    {
        $key = 'LEEVEL_DEBUG_'.$tag;

        $GLOBALS[$key] = true;
    }
}

if (!function_exists('debug_on')) {
    /**
     * Debug 进行时.
     *
     * @param string   $tag
     * @param \Closure $call
     * @param array    ...$args
     * @codeCoverageIgnore
     */
    function debug_on(string $tag, Closure $call = null, ...$args): void
    {
        $key = 'LEEVEL_DEBUG_'.$tag;

        if (isset($GLOBALS[$key])) {
            if (null !== $call) {
                $call(...$args);
            } else {
                dump(sprintf('----- `%s` start -----', $tag));
                dump(...$args);
                dump(sprintf('----- `%s` end -----', $tag));
                echo PHP_EOL;
            }
        }
    }
}

if (!function_exists('debug_end')) {
    /**
     * 清理 Debug 标记.
     *
     * @param string $tag
     * @codeCoverageIgnore
     */
    function debug_end(string $tag): void
    {
        $key = 'LEEVEL_DEBUG_'.$tag;

        if (isset($GLOBALS[$key])) {
            unset($GLOBALS[$key]);
        }
    }
}

if (!function_exists('fn')) {
    /**
     * 自动导入函数.
     *
     * @param \Closure|string $call
     * @param array           $args
     * @param mixed           $fn
     *
     * @return mixed
     */
    function fn($fn, ...$args)
    {
        return (new Fn())($fn, ...$args);
    }
}
