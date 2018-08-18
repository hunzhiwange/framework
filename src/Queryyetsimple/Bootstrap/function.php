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
use Leevel\Log\ILog;
use Leevel\Support\Debug\Dump;

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
        $instance = Project::singletons();

        $callback = [
            $instance,
            $method,
        ];

        if (!is_callable($callback)) {
            throw new BadMethodCallException(
                sprintf('Method %s is not exits.', $method)
            );
        }

        return call_user_func_array($callback, $args);
    }

    /**
     * 返回项目容器或者注入.
     *
     * @param null|string $instance
     * @param array       $args
     *
     * @return \Leevel\Bootstrap\Project
     */
    public static function project($instance = null, $args = [])
    {
        if (null === $instance) {
            return Project::singletons();
        }

        return Project::singletons()->make($instance, $args);
    }

    /**
     * 返回项目容器或者注入
     * project 别名函数.
     *
     * @param null|string $instance
     * @param array       $args
     *
     * @return \Leevel\Bootstrap\Project
     */
    public static function app($instance = null, $args = [])
    {
        return static::project($instance, $args);
    }

    /**
     * 调试一个变量.
     *
     * @param mixed $var
     * @param bool  $simple
     */
    public static function dd($var, bool $simple = false)
    {
        Dump::dump($var, $simple);
    }

    /**
     * 取得项目的环境变量.支持 boolean, empty 和 null.
     *
     * @param string $name
     * @param mixed  $defaults
     *
     * @return mixed
     */
    public static function env($name, $defaults = null)
    {
        switch (true) {
            case array_key_exists($name, $_ENV):
                $name = $_ENV[$name];

                break;
            case array_key_exists($name, $_SERVER):
                $name = $_SERVER[$name];

                break;
            default:
                $name = getenv($name);

                if (false === $name) {
                    $name = static::value($defaults);
                }
        }

        if (is_string($name)) {
            $name = strtolower($name);
        }

        switch ($name) {
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

        if ($name && strlen($name) > 1 &&
            '"' === $name[0] &&
            '"' === $name[strlen($name) - 1]) {
            return substr($name, 1, -1);
        }

        return $name;
    }

    /**
     * 返回默认值
     *
     * @param mixed $value
     * @param array $arr
     *
     * @return mixed
     */
    public static function value($value, ...$arr)
    {
        return !is_string($value) && is_callable($value) ?
            call_user_func_array($value, $arr) : $value;
    }

    /**
     * 日志.
     *
     * @param string $level
     * @param mixed  $message
     * @param array  $context
     * @param string $level
     * @param bool   $write
     */
    public static function log($message, array $context = [], string $level = ILog::INFO, bool $write = false)
    {
        static::project('log')->{$write ? 'write' : 'log'}($level, $message, $context);
    }

    /**
     * 设置或者获取 option 值
     *
     * @param array|string $key
     * @param mixed        $defaults
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
     * @param array|string $key
     * @param mixed        $defaults
     *
     * @return mixed
     */
    public static function cache($key = null, $defaults = null)
    {
        if (null === $key) {
            return static::project('cache');
        }

        if (is_array($key)) {
            return static::project('cache')->put($key);
        }

        return static::project('cache')->get($key, $defaults);
    }
}

if (!function_exists('project')) {
    /**
     * 返回项目容器或者注入.
     *
     * @param null|string $instance
     * @param array       $args
     *
     * @return \Leevel\Bootstrap\Project
     */
    function project($instance = null, $args = [])
    {
        if (null === $instance) {
            return Project::singletons();
        }

        return Project::singletons()->make($instance, $args);
    }
}

if (!function_exists('app')) {
    /**
     * 返回项目容器或者注入
     * project 别名函数.
     *
     * @param null|string $instance
     * @param array       $args
     *
     * @return \Leevel\Bootstrap\Project
     */
    function app($instance = null, $args = [])
    {
        return project($instance, $args);
    }
}

if (!function_exists('encrypt')) {
    /**
     * 加密字符串.
     *
     * @param string $value
     *
     * @return string
     */
    function encrypt($value)
    {
        return project('encryption')->encrypt($value);
    }
}

if (!function_exists('decrypt')) {
    /**
     * 解密字符串.
     *
     * @param string $value
     *
     * @return string
     */
    function decrypt($value)
    {
        return project('encryption')->decrypt($value);
    }
}

if (!function_exists('session')) {
    /**
     * 设置或者获取 session 值
     *
     * @param array|string $key
     * @param mixed        $defaults
     *
     * @return mixed
     */
    function session($key = null, $defaults = null)
    {
        if (null === $key) {
            return project('session');
        }

        if (is_array($key)) {
            return project('session')->put($key);
        }

        return project('session')->get($key, $defaults);
    }
}

if (!function_exists('flash')) {
    /**
     * 返回 flash.
     *
     * @param string $key
     * @param mixed  $defaults
     *
     * @return mixed
     */
    function flash($key, $defaults = null)
    {
        return project('session')->getFlash($key, $defaults);
    }
}

if (!function_exists('url')) {
    /**
     * 生成路由地址
     *
     * @param string $url
     * @param array  $params
     * @param string $subdomain
     * @param mixed  $suffix
     * @param mixed  $option
     *
     * @return string
     */
    function url($url, $params = [], $option = [], string $subdomain = 'www', $suffix = false): string
    {
        return project('url')->make($url, $params, $subdomain, $suffix);
    }
}

if (!function_exists('__')) {
    /**
     * 语言包.
     *
     * @param string $text
     * @param array  $arr
     *
     * @return string
     */
    function __(string $text, ...$arr)
    {
        static $i18n;

        if (null === $i18n) {
            if (!is_object($i18n = project('i18n'))) {
                $i18n = 'sprintf';
            } else {
                $i18n = [$i18n, 'getText'];
            }
        }

        array_unshift($arr, $text);

        return call_user_func_array($i18n, $arr);
    }
}

if (!function_exists('gettext')) {
    /**
     * 语言包.
     *
     * @param string $text
     * @param array  $arr
     *
     * @return string
     */
    function gettext(string $text, ...$arr)
    {
        return __($text, ...$arr);
    }
}
