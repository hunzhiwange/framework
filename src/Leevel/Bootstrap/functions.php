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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Leevel\Bootstrap\Project;
use Leevel\Debug\Dump;
use Leevel\Di\IContainer;
use Leevel\Log\ILog;

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
        return call_user_func_array([static::singletons(), $method], $args);
    }

    /**
     * 获取语言.
     *
     * @param string $text
     * @param array  $arr
     *
     * @return string
     */
    public static function __(string $text, ...$arr)
    {
        static $i18n;

        if (null === $i18n) {
            // @codeCoverageIgnoreStart
            if (!is_object($i18n = static::project('i18n'))) {
                $i18n = 'sprintf';
            // @codeCoverageIgnoreEnd
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
     * @return \Leevel\Bootstrap\Project|mixed
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
     * @return \Leevel\Bootstrap\Project|mixed
     */
    public static function app(?string $instance = null, array $args = [])
    {
        return static::project($instance, $args);
    }

    /**
     * 调试变量
     * 
     * @param  mixed $var
     * @param  array $moreVars
     * @return mixed
     */
    public static function dump($var, ...$moreVars)
    {
        return Dump::dump($var, ...$moreVars);
    }

    /**
     * 调试变量并中断
     * 
     * @param  mixed $var
     * @param  array $moreVars
     */
    public static function dd($var, ...$moreVars)
    {
        Dump::dumpDie($var, ...$moreVars);
    }

    /**
     * 调试栈信息
     */
    public static function backtrace()
    {
        Dump::backtrace();
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

        return static::project('logs')->log($level, $message, $context);
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
     *
     * @return mixed
     */
    public static function cache($key = null, $defaults = null)
    {
        if (null === $key) {
            return static::project('caches');
        }

        if (is_array($key)) {
            return static::project('caches')->put($key);
        }

        return static::project('caches')->get($key, $defaults);
    }

    /**
     * 加密字符串.
     *
     * @param string $value
     * @param int    $expiry
     *
     * @return string
     */
    public static function encrypt(string $value, ?int $expiry = null)
    {
        return static::project('encryption')->encrypt($value, $expiry);
    }

    /**
     * 解密字符串.
     *
     * @param string $value
     *
     * @return string
     */
    public static function decrypt(string $value)
    {
        return static::project('encryption')->decrypt($value);
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
     * @param null|string $key
     * @param mixed       $defaults
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
     * @param string $url
     * @param array  $params
     * @param string $subdomain
     * @param mixed  $suffix
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
    public static function gettext(string $text, ...$arr)
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
    function __(string $text, ...$arr)
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
    function gettext(string $text, ...$arr)
    {
        return Leevel::__($text, ...$arr);
    }
}

if (!function_exists('dump')) {
    /**
     * 调试变量
     * 
     * @param  mixed $var
     * @param  array $moreVars
     * @return mixed
     */
    function dump($var, ...$moreVars)
    {
        return Leevel::dump($var, ...$moreVars);
    }
}

if (!function_exists('dd')) {
    /**
     * 调试变量并中断
     * 
     * @param  mixed $var
     * @param  array $moreVars
     */
    function dd($var, ...$moreVars)
    {
        Leevel::dd($var, ...$moreVars);
    }
}

if (!function_exists('db')) {
    /**
     * 调试栈信息
     */
    function db()
    {
        Leevel::backtrace();
    }
}
