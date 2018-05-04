<?php
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
use Leevel\{
    Log\Ilog,
    Bootstrap\Project,
    Support\Debug\Dump
};

if (! function_exists('project')) {
    /**
     * 返回项目容器或者注入
     *
     * @param string|null $sInstance
     * @param array $arrArgs
     * @return \Leevel\Bootstrap\Project
     */
    function project($sInstance = null, $arrArgs = [])
    {
        if ($sInstance === null) {
            return project::singletons();
        } else {
            if (($objInstance = project::singletons()->make($sInstance, $arrArgs))) {
                return $objInstance;
            }
            throw new BadMethodCallException(sprintf('%s is not found in ioc container. ', $sInstance));
        }
    }
}

if (! function_exists('app')) {
    /**
     * 返回项目容器或者注入
     * project 别名函数
     *
     * @param string|null $sInstance
     * @param array $arrArgs
     * @return \Leevel\Bootstrap\Project
     */
    function app($sInstance = null, $arrArgs = [])
    {
        return project($sInstance, $arrArgs);
    }
}

if (! function_exists('run_with_extension')) {
    /**
     * 是否以扩展方式运行
     *
     * @return boolean
     */
    function run_with_extension()
    {
        return project()->runwithExtension();
    }
}

if (! function_exists('api')) {
    /**
     * 是否为 API
     *
     * @return boolean
     */
    function api()
    {
        return project()->api();
    }
}

if (! function_exists('phpui')) {
    /**
     * 是否为 PHPUI
     *
     * @return boolean
     */
    function phpui()
    {
        return env('app_mode', false) == 'phpui';
    }
}

if (! function_exists('console')) {
    /**
     * 是否为 Console
     *
     * @return boolean
     */
    function console()
    {
        return project()->console();
    }
}

if (! function_exists('dd')) {
    /**
     * 调试一个变量
     *
     * @param mixed $var
     * @param boolean $simple
     * @return mixed
     */
    function dd($var, $simple = false)
    {
        return Dump::dump($var, $simple);
    }
}

if (! function_exists('env')) {
    /**
     * 取得项目的环境变量.支持 boolean, empty 和 null.
     *
     * @param string $strName
     * @param mixed $mixDefault
     * @return mixed
     */
    function env($strName, $mixDefault = null)
    {
        switch (true) {
            case array_key_exists($strName, $_ENV):
                $strName = $_ENV[$strName];
                break;
            case array_key_exists($strName, $_SERVER):
                $strName = $_SERVER[$strName];
                break;
            default:
                $strName = getenv($strName);
                if ($strName === false) {
                    $strName = value($mixDefault);
                }
        }

        switch (strtolower($strName)) {
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

        if (strlen($strName) > 1 && $strName[0] == '"' && $strName[strlen($strName) - 1] == '"') {
            return substr($strName, 1, - 1);
        }

        return $strName;
    }
}

if (! function_exists('encrypt')) {
    /**
     * 加密字符串
     *
     * @param string $strValue
     * @return string
     */
    function encrypt($strValue)
    {
        return project('encryption')->encrypt($strValue);
    }
}

if (! function_exists('decrypt')) {
    /**
     * 解密字符串
     *
     * @param string $strValue
     * @return string
     */
    function decrypt($strValue)
    {
        return project('encryption')->decrypt($strValue);
    }
}

if (! function_exists('session')) {
    /**
     * 设置或者获取 session 值
     *
     * @param array|string $mixKey
     * @param mixed $mixDefault
     * @return mixed
     */
    function session($mixKey = null, $mixDefault = null)
    {
        if (is_null($mixKey)) {
            return project('session');
        }

        if (is_array($mixKey)) {
            return project('session')->put($mixKey);
        }

        return project('session')->get($mixKey, $mixDefault);
    }
}

if (! function_exists('flash')) {
    /**
     * 返回 flash
     *
     * @param string $strKey
     * @param mixed $mixDefault
     * @return mixed
     */
    function flash($strKey, $mixDefault = null)
    {
        return project('session')->getFlash($strKey, $mixDefault);
    }
}

if (! function_exists('url')) {
    /**
     * 生成路由地址
     *
     * @param string $url
     * @param array $params
     * @param array $option
     * @sub boolean suffix 是否包含后缀
     * @sub boolean normal 是否为普通 url
     * @sub string subdomain 子域名
     * @return string
     */
    function url($url, $params = [], $option = [])
    {
        return project('url')->make($url, $params, $option);
    }
}

if (! function_exists('prev_url')) {
    /**
     * 上一次访问 URL 地址
     *
     * @return string
     */
    function prev_url()
    {
        return project('request')->header('referer') ?  : project('session')->prevUrl();
    }
}

if (! function_exists('__')) {
    /**
     * 语言包
     *
     * @param array $arr
     * @return string
     */
    function __(...$arr)
    {
        static $i18n;

        if (is_null($i18n)) {
            $i18n = project('i18n');
        }

        return $i18n->{'getText'}(...$arr);
    }
}

if (! function_exists('gettext')) {
    /**
     * 语言包
     *
     * @param array $arr
     * @return string
     */
    function gettext(...$arr)
    {
        return __(...$arr);
    }
}

if (! function_exists('value')) {
    /**
     * 返回默认值
     *
     * @param array $arr
     * @return mixed
     */
    function value(...$arr)
    {
        if(count($arr) === 0) return;
        $mixValue = array_shift($arr);
        return ! is_string($mixValue) && is_callable($mixValue) ? call_user_func_array($mixValue, $arr) : $mixValue;
    }
}

if (! function_exists('log')) {
    /**
     * 记录错误消息
     *
     * @param string $strLevel
     * @param mixed $mixMessage
     * @param array $arrContext
     * @param boolean $booWrite
     * @return void
     */
    function log($strLevel, $mixMessage, array $arrContext = [], $booWrite = false)
    {
        project('log')->{$booWrite ? 'write' : 'log'}($strLevel, $mixMessage, $arrContext);
    }
}

if (! function_exists('debug')) {
    /**
     * 记录错误消息 debug
     *
     * @param mixed $mixMessage
     * @param array $arrContext
     * @param boolean $booWrite
     * @return void
     */
    function debug($mixMessage, array $arrContext = [], $booWrite = false)
    {
        log(ILog::DEBUG, $mixMessage, $arrContext, $booWrite);
    }
}

if (! function_exists('info')) {
    /**
     * 记录错误消息 info
     *
     * @param mixed $mixMessage
     * @param array $arrContext
     * @param boolean $booWrite
     * @return void
     */
    function info($mixMessage, array $arrContext = [], $booWrite = false)
    {
        log(ILog::INFO, $mixMessage, $arrContext, $booWrite);
    }
}

if (! function_exists('notice')) {
    /**
     * 记录错误消息 notice
     *
     * @param mixed $mixMessage
     * @param array $arrContext
     * @param boolean $booWrite
     * @return void
     */
    function notice($mixMessage, array $arrContext = [], $booWrite = false)
    {
        log(ILog::NOTICE, $mixMessage, $arrContext, $booWrite);
    }
}

if (! function_exists('warning')) {
    /**
     * 记录错误消息 warning
     *
     * @param mixed $mixMessage
     * @param array $arrContext
     * @param boolean $booWrite
     * @return void
     */
    function warning($mixMessage, array $arrContext = [], $booWrite = false)
    {
        log(ILog::WARNING, $mixMessage, $arrContext, $booWrite);
    }
}

if (! function_exists('error')) {
    /**
     * 记录错误消息 error
     *
     * @param mixed $mixMessage
     * @param array $arrContext
     * @param boolean $booWrite
     * @return void
     */
    function error($mixMessage, array $arrContext = [], $booWrite = false)
    {
        log(ILog::ERROR, $mixMessage, $arrContext, $booWrite);
    }
}

if (! function_exists('critical')) {
    /**
     * 记录错误消息 critical
     *
     * @param mixed $mixMessage
     * @param array $arrContext
     * @param boolean $booWrite
     * @return void
     */
    function critical($mixMessage, array $arrContext = [], $booWrite = false)
    {
        log(ILog::CRITICAL, $mixMessage, $arrContext, $booWrite);
    }
}

if (! function_exists('alert')) {
    /**
     * 记录错误消息 alert
     *
     * @param mixed $mixMessage
     * @param array $arrContext
     * @param boolean $booWrite
     * @return void
     */
    function alert($mixMessage, array $arrContext = [], $booWrite = false)
    {
        log(ILog::ALERT, $mixMessage, $arrContext, $booWrite);
    }
}

if (! function_exists('emergency')) {
    /**
     * 记录错误消息 emergency
     *
     * @param mixed $mixMessage
     * @param array $arrContext
     * @param boolean $booWrite
     * @return void
     */
    function emergency($mixMessage, array $arrContext = [], $booWrite = false)
    {
        log(ILog::EMERGENCY, $mixMessage, $arrContext, $booWrite);
    }
}

if (! function_exists('option')) {
    /**
     * 设置或者获取 option 值
     *
     * @param array|string $mixKey
     * @param mixed $mixDefault
     * @return mixed
     */
    function option($mixKey = null, $mixDefault = null)
    {
        if (is_null($mixKey)) {
            return project('option');
        }

        if (is_array($mixKey)) {
            return project('option')->set($mixKey);
        }

        return project('option')->get($mixKey, $mixDefault);
    }
}

if (! function_exists('cache')) {
    /**
     * 设置或者获取 cache 值
     *
     * @param array|string $mixKey
     * @param mixed $mixDefault
     * @return mixed
     */
    function cache($mixKey = null, $mixDefault = null)
    {
        if (is_null($mixKey)) {
            return project('cache');
        }

        if (is_array($mixKey)) {
            return project('cache')->put($mixKey);
        }

        return project('cache')->get($mixKey, $mixDefault);
    }
}

if (! function_exists('path')) {
    /**
     * 取得项目路径
     *
     * @param string $path
     * @return string
     */
    function path($path = '')
    {
        return project()->path() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (! function_exists('path_application')) {
    /**
     * 取得项目应用路径
     *
     * @param string $path
     * @return string
     */
    function path_application($path = '')
    {
        return project()->pathApplication() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (! function_exists('path_common')) {
    /**
     * 取得项目公共路径
     *
     * @param string $path
     * @return string
     */
    function path_common($path = '')
    {
        return project()->pathCommon() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (! function_exists('path_runtime')) {
    /**
     * 取得项目缓存路径
     *
     * @param string $path
     * @return string
     */
    function path_runtime($path = '')
    {
        return project()->pathRuntime() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (! function_exists('path_storage')) {
    /**
     * 取得项目附件路径
     *
     * @param string $path
     * @return string
     */
    function path_storage($path = '')
    {
        return project()->pathStorage() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (! function_exists('path_option')) {
    /**
     * 取得项目配置路径
     *
     * @param string $path
     * @return string
     */
    function path_option($path = '')
    {
        return project()->pathOption() . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (! function_exists('path_application')) {
    /**
     * 取得项目当前应用路径
     *
     * @param string $path
     * @param string $app
     * @return string
     */
    function path_an_application(string $path = '', ?string $app = null)
    {
        return project()->pathAnApplication($app) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (! function_exists('path_theme')) {
    /**
     * 取得项目当前应用主题路径
     *
     * @param string $path
     * @return string
     */
    function path_theme($path = '')
    {
        return project()->pathApplicationDir('theme') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (! function_exists('path_i18n')) {
    /**
     * 取得项目当前应用国际化路径
     *
     * @param string $path
     * @return string
     */
    function path_i18n($path = '')
    {
        return project()->pathApplicationDir('i18n') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (! function_exists('path_file_cache')) {
    /**
     * 取得项目当前应用文件缓存路径
     *
     * @param string $path
     * @return string
     */
    function path_file_cache($path = '')
    {
        return project()->pathApplicationCache('file') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (! function_exists('path_log_cache')) {
    /**
     * 取得项目当前应用日志缓存路径
     *
     * @param string $path
     * @return string
     */
    function path_log_cache($path = '')
    {
        return project()->pathApplicationCache('log') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (! function_exists('path_swoole_cache')) {
    /**
     * 取得项目当前应用 swoole 缓存路径
     *
     * @param string $path
     * @return string
     */
    function path_swoole_cache($path = '')
    {
        return project()->pathApplicationCache('swoole') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (! function_exists('path_table_cache')) {
    /**
     * 取得项目当前应用数据表缓存路径
     *
     * @param string $path
     * @return string
     */
    function path_table_cache($path = '')
    {
        return project()->pathApplicationCache('table') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (! function_exists('path_router_cache')) {
    /**
     * 取得项目当前应用路由缓存路径
     *
     * @param string $path
     * @return string
     */
    function path_router_cache($path = 'router.php')
    {
        return project()->pathApplicationCache('router') . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

if (! function_exists('is_ajax_request')) {
    /**
     * 是否为 ajax 请求
     *
     * @return boolean
     */
    function is_ajax_request()
    {
        $oRequest = app('request');
        if ($oRequest->isAjax() && ! $oRequest->isPjax()) {
            return true;
        }
        return false;
    }
}

if (! function_exists('set_ajax_request')) {
    /**
     * 强制设置是否为 ajax 请求
     *
     * @param boolean $booStatus
     * @return boolean
     * @note 返回上一次是否为 ajax 请求
     */
    function set_ajax_request($booStatus = true)
    {
        $booOld = is_ajax_request();
        app('request')->setPost(app('option')->get('var_ajax'), $booStatus);
        return $booOld;
    }
}
