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

if (!function_exists('run_with_extension')) {
    /**
     * 是否以扩展方式运行.
     *
     * @return bool
     */
    function run_with_extension()
    {
        return project()->runwithExtension();
    }
}

if (!function_exists('api')) {
    /**
     * 是否为 API.
     *
     * @return bool
     */
    function api()
    {
        return project()->api();
    }
}

if (!function_exists('phpui')) {
    /**
     * 是否为 PHPUI.
     *
     * @return bool
     */
    function phpui()
    {
        return 'phpui' === env('app_mode', false);
    }
}

if (!function_exists('console')) {
    /**
     * 是否为 Console.
     *
     * @return bool
     */
    function console()
    {
        return project()->console();
    }
}

if (!function_exists('dd')) {
    /**
     * 调试一个变量.
     *
     * @param mixed $var
     * @param bool  $simple
     *
     * @return mixed
     */
    function dd($var, $simple = false)
    {
        return Dump::dump($var, $simple);
    }
}

if (!function_exists('dump')) {
    /**
     * 调试一个变量.
     *
     * @param mixed $var
     * @param bool  $simple
     *
     * @return mixed
     */
    function dump($var, $simple = false)
    {
        return dd($var, $simple);
    }
}

if (!function_exists('env')) {
    /**
     * 取得项目的环境变量.支持 boolean, empty 和 null.
     *
     * @param string $name
     * @param mixed  $defaults
     *
     * @return mixed
     */
    function env($name, $defaults = null)
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
                    $name = value($defaults);
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

if (!function_exists('prev_url')) {
    /**
     * 上一次访问 URL 地址
     *
     * @return string
     */
    function prev_url()
    {
        return project('request')->header('referer') ?: project('session')->prevUrl();
    }
}

if (!function_exists('__')) {
    /**
     * 语言包.
     *
     * @param array $arr
     *
     * @return string
     */
    function __(...$arr)
    {
        static $i18n;

        if (null === $i18n) {
            if (!is_object($i18n = project('i18n'))) {
                $i18n = __sprintf();
            } else {
                $i18n = [$i18n, 'getText'];
            }
        }

        return call_user_func_array($i18n, $arr);
    }
}

if (!function_exists('__sprintf')) {
    /**
     * lang.
     *
     * @param array $arr
     *
     * @return string
     */
    function __sprintf(...$arr)
    {
        return sprintf(...$arr);
    }
}

if (!function_exists('gettext')) {
    /**
     * 语言包.
     *
     * @param array $arr
     *
     * @return string
     */
    function gettext(...$arr)
    {
        return __(...$arr);
    }
}

if (!function_exists('value')) {
    /**
     * 返回默认值
     *
     * @param array $arr
     *
     * @return mixed
     */
    function value(...$arr)
    {
        if (0 === count($arr)) {
            return;
        }

        $value = array_shift($arr);

        return !is_string($value) && is_callable($value) ?
            call_user_func_array($value, $arr) :
            $value;
    }
}

if (!function_exists('log')) {
    /**
     * 记录错误消息.
     *
     * @param string $level
     * @param mixed  $message
     * @param array  $context
     * @param bool   $write
     */
    function log($level, $message, array $context = [], $write = false)
    {
        project('log')->{$write ? 'write' : 'log'}($level, $message, $context);
    }
}

if (!function_exists('debug')) {
    /**
     * 记录错误消息 debug.
     *
     * @param mixed $message
     * @param array $context
     * @param bool  $write
     */
    function debug($message, array $context = [], $write = false)
    {
        log(ILog::DEBUG, $message, $context, $write);
    }
}

if (!function_exists('info')) {
    /**
     * 记录错误消息 info.
     *
     * @param mixed $message
     * @param array $context
     * @param bool  $write
     */
    function info($message, array $context = [], $write = false)
    {
        log(ILog::INFO, $message, $context, $write);
    }
}

if (!function_exists('notice')) {
    /**
     * 记录错误消息 notice.
     *
     * @param mixed $message
     * @param array $context
     * @param bool  $write
     */
    function notice($message, array $context = [], $write = false)
    {
        log(ILog::NOTICE, $message, $context, $write);
    }
}

if (!function_exists('warning')) {
    /**
     * 记录错误消息 warning.
     *
     * @param mixed $message
     * @param array $context
     * @param bool  $write
     */
    function warning($message, array $context = [], $write = false)
    {
        log(ILog::WARNING, $message, $context, $write);
    }
}

if (!function_exists('error')) {
    /**
     * 记录错误消息 error.
     *
     * @param mixed $message
     * @param array $context
     * @param bool  $write
     */
    function error($message, array $context = [], $write = false)
    {
        log(ILog::ERROR, $message, $context, $write);
    }
}

if (!function_exists('critical')) {
    /**
     * 记录错误消息 critical.
     *
     * @param mixed $message
     * @param array $context
     * @param bool  $write
     */
    function critical($message, array $context = [], $write = false)
    {
        log(ILog::CRITICAL, $message, $context, $write);
    }
}

if (!function_exists('alert')) {
    /**
     * 记录错误消息 alert.
     *
     * @param mixed $message
     * @param array $context
     * @param bool  $write
     */
    function alert($message, array $context = [], $write = false)
    {
        log(ILog::ALERT, $message, $context, $write);
    }
}

if (!function_exists('emergency')) {
    /**
     * 记录错误消息 emergency.
     *
     * @param mixed $message
     * @param array $context
     * @param bool  $write
     */
    function emergency($message, array $context = [], $write = false)
    {
        log(ILog::EMERGENCY, $message, $context, $write);
    }
}

if (!function_exists('option')) {
    /**
     * 设置或者获取 option 值
     *
     * @param array|string $key
     * @param mixed        $defaults
     *
     * @return mixed
     */
    function option($key = null, $defaults = null)
    {
        if (null === $key) {
            return project('option');
        }

        if (is_array($key)) {
            return project('option')->set($key);
        }

        return project('option')->get($key, $defaults);
    }
}

if (!function_exists('cache')) {
    /**
     * 设置或者获取 cache 值
     *
     * @param array|string $key
     * @param mixed        $defaults
     *
     * @return mixed
     */
    function cache($key = null, $defaults = null)
    {
        if (null === $key) {
            return project('cache');
        }

        if (is_array($key)) {
            return project('cache')->put($key);
        }

        return project('cache')->get($key, $defaults);
    }
}

if (!function_exists('path')) {
    /**
     * 取得项目路径.
     *
     * @param string $path
     *
     * @return string
     */
    function path($path = '')
    {
        return project()->path().
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('path_application')) {
    /**
     * 取得项目应用路径.
     *
     * @param string $path
     *
     * @return string
     */
    function path_application($path = '')
    {
        return project()->pathApplication().
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('path_common')) {
    /**
     * 取得项目公共路径.
     *
     * @param string $path
     *
     * @return string
     */
    function path_common($path = '')
    {
        return project()->pathCommon().
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('path_runtime')) {
    /**
     * 取得项目缓存路径.
     *
     * @param string $path
     *
     * @return string
     */
    function path_runtime($path = '')
    {
        return project()->pathRuntime().
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('path_storage')) {
    /**
     * 取得项目附件路径.
     *
     * @param string $path
     *
     * @return string
     */
    function path_storage($path = '')
    {
        return project()->pathStorage().
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('path_option')) {
    /**
     * 取得项目配置路径.
     *
     * @param string $path
     *
     * @return string
     */
    function path_option($path = '')
    {
        return project()->pathOption().
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('path_application')) {
    /**
     * 取得项目当前应用路径.
     *
     * @param string $path
     * @param string $app
     *
     * @return string
     */
    function path_an_application(string $path = '', ?string $app = null)
    {
        return project()->pathAnApplication($app).
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('path_theme')) {
    /**
     * 取得项目当前应用主题路径.
     *
     * @param string $path
     *
     * @return string
     */
    function path_theme($path = '')
    {
        return project()->pathApplicationTheme().
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('path_file_cache')) {
    /**
     * 取得项目当前应用文件缓存路径.
     *
     * @param string $path
     *
     * @return string
     */
    function path_file_cache($path = '')
    {
        return project()->pathApplicationCache('file').
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('path_log_cache')) {
    /**
     * 取得项目当前应用日志缓存路径.
     *
     * @param string $path
     *
     * @return string
     */
    function path_log_cache($path = '')
    {
        return project()->pathApplicationCache('log').
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('path_swoole_cache')) {
    /**
     * 取得项目当前应用 swoole 缓存路径.
     *
     * @param string $path
     *
     * @return string
     */
    function path_swoole_cache($path = '')
    {
        return project()->pathApplicationCache('swoole').
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('path_table_cache')) {
    /**
     * 取得项目当前应用数据表缓存路径.
     *
     * @param string $path
     *
     * @return string
     */
    function path_table_cache($path = '')
    {
        return project()->pathApplicationCache('table').
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('path_router_cache')) {
    /**
     * 取得项目当前应用路由缓存路径.
     *
     * @param string $path
     *
     * @return string
     */
    function path_router_cache($path = 'router.php')
    {
        return project()->pathApplicationCache('router').
            ($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}

if (!function_exists('is_ajax_request')) {
    /**
     * 是否为 ajax 请求
     *
     * @return bool
     */
    function is_ajax_request()
    {
        $request = app('request');

        if ($request->isAjax() && !$request->isPjax()) {
            return true;
        }

        return false;
    }
}

if (!function_exists('set_ajax_request')) {
    /**
     * 强制设置是否为 ajax 请求
     *
     * @param bool $status
     *
     * @return bool
     * @note 返回上一次是否为 ajax 请求
     */
    function set_ajax_request($status = true)
    {
        $old = is_ajax_request();

        app('request')->setPost(app('option')->get('var_ajax'), $status);

        return $old;
    }
}
