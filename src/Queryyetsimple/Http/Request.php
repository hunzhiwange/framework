<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Http;

use ArrayAccess;
use RuntimeException;
use Queryyetsimple\{
    Option\TClass,
    Support\TMacro,
    Cookie\ICookie,
    Support\IArray,
    Session\ISession
};

/**
 * http 请求
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2016.11.19
 * @version 1.0
 */
class Request implements IArray, ArrayAccess
{
    use TClass;

    use TMacro {
        __call as macroCall;
    }

    /**
     * GET Bag
     *
     * @var \Queryyetsimple\Http\Bag
     */
    public $query;

    /**
     * POST Bag
     *
     * @var \Queryyetsimple\Http\Bag
     */
    public $request;

    /**
     * 路由解析后的参数
     *
     * @var \Queryyetsimple\Http\Bag
     */
    public $params;

    /**
     * COOKIE Bag
     *
     * @var \Queryyetsimple\Http\Bag
     */
    public $cookie;

    /**
     * FILE Bag
     *
     * @var \Queryyetsimple\Http\FileBag
     */
    public $files;

    /**
     * SERVER Bag
     *
     * @var \Queryyetsimple\Http\ServerBag
     */
    public $server;

    /**
     * HEADER Bag
     *
     * @var \Queryyetsimple\Http\HeaderBag
     */
    public $headers;

    /**
     * 内容
     * 
     * @var string|resource|false|null
     */
    protected $content;

    /**
     * 基础 url
     *
     * @var string
     */
    protected $baseUrl;

    /**
     * 请求 url
     *
     * @var string
     */
    protected $requestUri;

    /**
     * 请求类型
     *
     * @var string
     */
    protected $method;

    /**
     * public URL
     *
     * @var string
     */
    protected $publicUrl;

    /**
     * pathInfo
     *
     * @var string
     */
    protected $pathInfo;

    /**
     * 应用名字
     *
     * @var string
     */
    protected $app;

    /**
     * 控制器名字
     *
     * @var string
     */
    protected $controller;

    /**
     * 方法名字
     *
     * @var string
     */
    protected $action;

    /**
     * 当前语言
     *
     * @var string
     */
    protected $language;

    /**
     * 服务器 url 重写支持 pathInfo
     *
     * Nginx
     * location @rewrite {
     *     rewrite ^/(.*)$ /index.php?_url=/$1;
     * }
     *
     * @var string
     */
    const PATHINFO_URL = '_url';

    /**
     * METHOD_HEAD
     * 
     * @var string
     */
    const METHOD_HEAD = 'HEAD';

    /**
     * METHOD_GET
     * 
     * @var string
     */
    const METHOD_GET = 'GET';

    /**
     * METHOD_POST
     * 
     * @var string
     */
    const METHOD_POST = 'POST';

    /**
     * METHOD_PUT
     * 
     * @var string
     */
    const METHOD_PUT = 'PUT';

    /**
     * METHOD_PATCH
     * 
     * @var string
     */
    const METHOD_PATCH = 'PATCH';

    /**
     * METHOD_DELETE
     * 
     * @var string
     */
    const METHOD_DELETE = 'DELETE';

    /**
     * METHOD_PURGE
     * 
     * @var string
     */
    const METHOD_PURGE = 'PURGE';

    /**
     * METHOD_OPTIONS
     * 
     * @var string
     */
    const METHOD_OPTIONS = 'OPTIONS';

    /**
     * METHOD_TRACE
     * 
     * @var string
     */
    const METHOD_TRACE = 'TRACE';

    /**
     * METHOD_CONNECT
     * 
     * @var string
     */
    const METHOD_CONNECT = 'CONNECT';

    /**
     * 配置
     *
     * @var array
     */
    protected $option = [
        'var_method' => '_method',
        'var_ajax' => '_ajax',
        'var_pjax' => '_pjax',
        'html_suffix' => '.html',
        'rewrite' => false,
        'public' => 'http://public.foo.bar'
    ];

    /**
     * 构造函数
     * 
     * @param array $query
     * @param array $request
     * @param array $params
     * @param array $cookie
     * @param array $files
     * @param array $server
     * @param string $content
     * @param array $option
     */
    public function __construct(array $query = [], array $request = [], array $params = [], array $cookie = [], array $files = [], array $server = [], $content = null, array $option = [])
    {
        $this->reset($query, $request, $params, $cookie, $files, $server, $content);
        $this->options($option);
    }

    /**
     * 重置或者初始化
     * 
     * @param array $query
     * @param array $request
     * @param array $params
     * @param array $cookie
     * @param array $files
     * @param array $server
     * @param string $content
     * @param array $option
     */
    public function reset(array $query = [], array $request = [], array $params = [], array $cookie = [], array $files = [], array $server = [], $content = null)
    {
        $this->query = new Bag($query);
        $this->request = new Bag($request);
        $this->params = new Bag($params);
        $this->cookie = new Bag($cookie);
        $this->files = new FileBag($files);
        $this->server = new ServerBag($server);
        $this->headers = new HeaderBag($this->server->getHeaders());

        $this->content = $content;
        $this->baseUrl = null;
        $this->requestUri = null;
        $this->method = null;
        $this->publicUrl = null;
        $this->pathInfo = null;
        $this->app = null;
        $this->controller = null;
        $this->action = null;
        $this->language = null;
    }

    /**
     * 全局变量创建一个 Request
     *
     * @param array $options
     * @return static
     */
    public static function createFromGlobals(array $option = [])
    {
        $request = new static($_GET, $_POST, [], $_COOKIE, $_FILES, $_SERVER, null, $option);

        if (0 === strpos($request->headers->get('CONTENT_TYPE'), 'application/x-www-form-urlencoded')
            && in_array(strtoupper($request->server->get('REQUEST_METHOD', 'GET')), ['PUT', 'DELETE', 'PATCH'])
        ) {
            parse_str($request->getContent(), $data);
            $request->request = new Bag($data);
        }

        return $request;
    }

    /**
     * 获取参数
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if ($this !== $result = $this->params->get($key, $this)) {
            return $result;
        }

        if ($this !== $result = $this->query->get($key, $this)) {
            return $result;
        }

        if ($this !== $result = $this->request->get($key, $this)) {
            return $result;
        }

        return $default;
    }

    /**
     * PHP 运行模式命令行, 兼容 swoole http service
     * Swoole http 服务器也以命令行运行
     * 
     * @link http://php.net/manual/zh/function.php-sapi-name.php
     * @return boolean
     */
    public function isCli()
    {
        if($this->server->get('SERVER_SOFTWARE') == 'swoole-http-server') {
            return false;
        }

        return $this->isRealCli();
    }

    /**
     * PHP 运行模式命令行
     * 
     * @link http://php.net/manual/zh/function.php-sapi-name.php
     * @return boolean
     */
    public function isRealCli()
    {
        return PHP_SAPI == 'cli';
    }

    /**
     * PHP 运行模式 cgi
     *
     * @link http://php.net/manual/zh/function.php-sapi-name.php
     * @return boolean
     */
    public function isCgi()
    {
        return substr(PHP_SAPI, 0, 3) == 'cgi';
    }

    /**
     * 是否为 Ajax 请求行为
     *
     * @return boolean
     */
    public function isAjax()
    {
        $field = $this->getOption('var_ajax');

        if ($this->request->has($field) || $this->query->has($field)) {
            return true;
        }

        return $this->isRealAjax();
    }

    /**
     * 是否为 Ajax 请求行为真实
     *
     * @return boolean
     */
    public function isRealAjax()
    {
        return $this->headers->get('X_REQUESTED_WITH') === 'xmlhttprequest';
    }

    /**
     * 是否为 Pjax 请求行为
     *
     * @return boolean
     */
    public function isPjax()
    {
        $field = $this->getOption('var_pjax');

        if ($this->request->has($field) || $this->query->has($field)) {
            return true;
        }

        return $this->isRealPjax();
    }

    /**
     * 是否为 Pjax 请求行为真实
     *
     * @return boolean
     */
    public function isRealPjax()
    {
        return ! is_null($this->headers->get('X_PJAX'));
    }

    /**
     * 是否为手机访问
     *
     * @return boolean
     */
    public function isMobile()
    {
        $useAgent = $this->headers->get('USER_AGENT');
        $allHttp = $this->server->get('ALL_HTTP');

        // Pre-final check to reset everything if the user is on Windows
        if (strpos($useAgent, 'windows') !== false) {
            return false;
        }

        if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', $useAgent)) {
            return true;
        }

        if (strpos($this->headers->get('ACCEPT'), 'application/vnd.wap.xhtml+xml') !== false) {
            return true;
        }

        if ($this->headers->has('X_WAP_PROFILE') || $this->headers->has('PROFILE')) {
            return true;
        }

        if (in_array(strtolower(substr($useAgent, 0, 4)), [
            'w3c ',
            'acs-',
            'alav',
            'alca',
            'amoi',
            'audi',
            'avan',
            'benq',
            'bird',
            'blac',
            'blaz',
            'brew',
            'cell',
            'cldc',
            'cmd-',
            'dang',
            'doco',
            'eric',
            'hipt',
            'inno',
            'ipaq',
            'java',
            'jigs',
            'kddi',
            'keji',
            'leno',
            'lg-c',
            'lg-d',
            'lg-g',
            'lge-',
            'maui',
            'maxo',
            'midp',
            'mits',
            'mmef',
            'mobi',
            'mot-',
            'moto',
            'mwbp',
            'nec-',
            'newt',
            'noki',
            'oper',
            'palm',
            'pana',
            'pant',
            'phil',
            'play',
            'port',
            'prox',
            'qwap',
            'sage',
            'sams',
            'sany',
            'sch-',
            'sec-',
            'send',
            'seri',
            'sgh-',
            'shar',
            'sie-',
            'siem',
            'smal',
            'smar',
            'sony',
            'sph-',
            'symb',
            't-mo',
            'teli',
            'tim-',
            'tosh',
            'tsm-',
            'upg1',
            'upsi',
            'vk-v',
            'voda',
            'wap-',
            'wapa',
            'wapi',
            'wapp',
            'wapr',
            'webc',
            'winw',
            'winw',
            'xda',
            'xda-'
        ])) {
            return true;
        }

        if (strpos(strtolower($allHttp), 'operamini') !== false) {
            return true;
        }

        // But WP7 is also Windows, with a slightly different characteristic
        if (strpos($useAgent, 'windows phone') !== false) {
            return true;
        }

        return false;
    }

    /**
     * 是否为 HEAD 请求行为
     *
     * @return boolean
     */
    public function isHead()
    {
        return $this->getMethod() == static::METHOD_HEAD;
    }

    /**
     * 是否为 GET 请求行为
     *
     * @return boolean
     */
    public function isGet()
    {
        return $this->getMethod() == static::METHOD_GET;
    }

    /**
     * 是否为 POST 请求行为
     *
     * @return boolean
     */
    public function isPost()
    {
        return $this->getMethod() == static::METHOD_POST;
    }

    /**
     * 是否为 PUT 请求行为
     *
     * @return boolean
     */
    public function isPut()
    {
        return $this->getMethod() == static::METHOD_PUT;
    }

    /**
     * 是否为 PATCH 请求行为
     *
     * @return boolean
     */
    public function isPatch()
    {
        return $this->getMethod() == static::METHOD_PATCH;
    }

    /**
     * 是否为 PURGE 请求行为
     *
     * @return boolean
     */
    public function isPurge()
    {
        return $this->getMethod() == static::METHOD_PURGE;
    }

    /**
     * 是否为 OPTIONS 请求行为
     *
     * @return boolean
     */
    public function isOptions()
    {
        return $this->getMethod() == static::METHOD_OPTIONS;
    }

    /**
     * 是否为 TRACE 请求行为
     *
     * @return boolean
     */
    public function isTrace()
    {
        return $this->getMethod() == static::METHOD_TRACE;
    }

    /**
     * 是否为 CONNECT 请求行为
     *
     * @return boolean
     */
    public function isConnect()
    {
        return $this->getMethod() == static::METHOD_CONNECT;
    }

    /**
     * 获取 IP 地址
     *
     * @return string
     */
    public function getClientIp()
    {
        return $this->headers->get('CLIENT_IP', $this->server->get('REMOTE_ADDR', '0.0.0.0'));
    }

    /**
     * 请求类型
     *
     * @return string
     */
    public function getMethod()
    {
        if (! is_null($this->method)) {
            return $this->method;
        }

        $this->method = strtoupper($this->server->get('REQUEST_METHOD', 'GET'));

        if ('POST' === $this->method) {
            if ($method = $this->headers->get('X-HTTP-METHOD-OVERRIDE')) {
                $this->method = strtoupper($method);
            } else {
                $field = $this->getOption('var_method');

                $this->method = strtoupper($this->request->get($field, $this->query->get($field, 'POST')));
            }
        }

        return $this->method;
    }

    /**
     * 设置请求类型
     *
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = null;
        $this->server->set('REQUEST_METHOD', $method);

        return $this;
    }

    /**
     * 实际请求类型
     *
     * @return string
     */
    public function getRealMethod()
    {
        return strtoupper($this->server->get('REQUEST_METHOD', 'GET'));
    }

    /**
     * 取回应用名
     *
     * @return string
     */
    public function app()
    {
        return $this->app;
    }

    /**
     * 取回控制器名
     *
     * @return string
     */
    public function controller()
    {
        return $this->controller;
    }

    /**
     * 取回方法名
     *
     * @return string
     */
    public function action()
    {
        return $this->action;
    }

    /**
     * 取得节点
     *
     * @return string
     */
    public function getNode()
    {
        return $this->app() . '://' . $this->controller() . '/' . $this->action();
    }

    /**
     * 设置应用名
     *
     * @param string $app
     * @return $this
     */
    public function setApp($app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * 设置控制器名
     *
     * @param string $controller
     * @return $this
     */
    public function setController($controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * 设置方法名
     *
     * @param string $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * 返回当前的语言
     *
     * @return string|null
     */
    public function language()
    {
        return $this->language;
    }

    /**
     * 设置当前的语言
     *
     * @param string $language
     * @return $this
     */
    public function setLanguage($language)
    {
        $this->language = $language;
        
        return $this;
    }

    /**
     * 返回网站公共文件目录
     *
     * @return string
     */
    public function getPublicUrl()
    {
        if (! is_null($this->publicUrl)) {
            return $this->publicUrl;
        }

        return $this->publicUrl = $this->getOption('public');
    }

    /**
     * 设置网站公共文件目录
     *
     * @param string $publicUrl
     * @return $this
     */
    public function setPublicUrl($publicUrl)
    {
        $this->publicUrl = $publicUrl;

        return $this;
    }

    /**
     * 返回 root URL
     *
     * @return string
     */
    public function getRoot()
    {
        return rtrim($this->getSchemeAndHttpHost() . $this->getBaseUrl(), '/');
    }

    /**
     * 返回入口文件
     *
     * @return string
     */
    public function getScriptNameRewrite()
    {
        $scriptName = $this->getScriptName();

        if ($this->getOption('rewrite') !== true) {
            return $scriptName;
        }

        $scriptName = dirname($scriptName);
        if ($scriptName == '\\') {
            $scriptName = '/';
        }

        return $scriptName;
    }

    /**
     * 取得脚本名字
     *
     * @return string
     */
    public function getScriptName()
    {
        return $this->server->get('SCRIPT_NAME', $this->server->get('ORIG_SCRIPT_NAME', ''));
    }

    /**
     * 是否启用 https
     *
     * @return boolean
     */
    public function isSecure()
    {
        if (in_array($this->server->get('HTTPS'), ['1', '1'])) {
            return true;
        } elseif ($this->server->get('SERVER_PORT') == '443') {
            return true;
        }

        return false;
    }

    /**
     * 取得 http host
     *
     * @return string
     */
    public function getHttpHost()
    {
        $scheme = $this->getScheme();
        $port = $this->getPort();

        if (('http' == $scheme && 80 == $port) || ('https' == $scheme && 443 == $port)) {
            return $this->getHost();
        }

        return $this->getHost() . ':' . $port;
    }

    /**
     * 获取 host
     *
     * @return boolean
     */
    public function getHost()
    {
        return $this->headers->get('X_FORWARDED_HOST', $this->headers->get('HOST', ''));
    }

    /**
     * 取得 Scheme 和 Host
     *
     * @return string
     */
    public function getSchemeAndHttpHost()
    {
        return $this->getScheme() . '://' . $this->getHost();
    }

    /**
     * 返回当前 URL 地址
     *
     * @return string
     */
    public function getUri()
    {
        if (null !== $queryString = $this->getQueryString()) {
            $queryString = '?' . $queryString;
        }

        return $this->getSchemeAndHttpHost() . $this->getBaseUrl() . $this->getPathInfo() . $queryString;
    }

    /**
     * 服务器端口
     *
     * @return integer
     */
    public function getPort()
    {
        $port = $this->server->get('SERVER_PORT');

        if (! $port) {
            $port = 'https' === $this->getScheme() ? 443 : 80;
        }

        return $port;
    }

    /**
     * 返回 scheme
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    /**
     * 取回查询参数
     *
     * @return string|null
     */
    public function getQueryString()
    {
        $queryString = $this->normalizeQueryString($this->server->get('QUERY_STRING'));

        return '' === $queryString ? null : $queryString;
    }

    /**
     * 设置 pathInfo
     *
     * @param string $pathInfo
     * @return $this
     */
    public function setPathInfo($pathInfo)
    {
        $this->pathInfo = $pathInfo;

        return $this;
    }

    /**
     * pathInfo 兼容性分析
     *
     * @return string
     */
    public function getPathInfo()
    {
        if (! is_null($this->pathInfo)) {
            return $this->pathInfo;
        }

        $pathInfo = $this->server->get('PATH_INFO');
        if ($pathInfo) {
            return $this->parsePathInfo($pathInfo);
        }

        // 服务器重写
        if (! empty($_GET[static::PATHINFO_URL])) {
            $pathInfo = $this->parsePathInfo($_GET[static::PATHINFO_URL]);
            unset($_GET[static::PATHINFO_URL]);
            return $pathInfo;
        }

        // 分析基础 url
        $baseUrl = $this->getBaseUrl();

        // 分析请求参数
        if (null === ($requestUri = $this->getRequestUri())) {
            return $this->parsePathInfo('');
        }

        if (($pos = strpos($requestUri, '?')) > 0) {
            $requestUri = substr($requestUri, 0, $pos);
        }

        if ((null !== $baseUrl) && (false === ($pathInfo = substr($requestUri, strlen($baseUrl))))) {
            $pathInfo = '';
        } elseif (null === $baseUrl) {
            $pathInfo = $requestUri;
        }

        return $this->parsePathInfo($pathInfo);
    }

    /**
     * 分析基础 url
     *
     * @return string
     */
    public function getBaseUrl()
    {
        if (! is_null($this->baseUrl)) {
            return $this->baseUrl;
        }

        // 兼容分析
        $fileName = basename($this->server->get('SCRIPT_FILENAME'));

        if (basename($this->server->get('SCRIPT_NAME')) === $fileName) {
            $url = $this->server->get('SCRIPT_NAME');
        } elseif (basename($this->server->get('PHP_SELF')) === $fileName) {
            $url = $this->server->get('PHP_SELF');
        } elseif (basename($this->server->get('ORIG_SCRIPT_NAME')) === $fileName) {
            $url = $this->server->get('ORIG_SCRIPT_NAME');
        } else {
            $path = $this->server->get('PHP_SELF');
            $segs = explode('/', trim($fileName, '/'));
            $segs = array_reverse($segs);
            $index = 0;
            $maxCount = count($segs);

            $url = '';
            do {
                $seg = $segs[$index];
                $url = '/' . $seg . $url;
                ++ $index;
            } while (($maxCount > $index) && (false !== ($pos = strpos($path, $url))) && (0 != $pos));
        }

        // 比对请求
        $requestUri = $this->getRequestUri();

        if (0 === strpos($requestUri, $url)) {
            return $this->baseUrl = $url;
        }

        if (0 === strpos($requestUri, dirname($url))) {
            return $this->baseUrl = rtrim(dirname($url), '/') . '/';
        }

        if (! strpos($requestUri, basename($url))) {
            return '';
        }

        if ((strlen($requestUri) >= strlen($url)) && ((false !== ($pos = strpos($requestUri, $url))) && ($pos !== 0))) {
            $url = substr($requestUri, 0, $pos + strlen($url));
        }

        return $this->baseUrl = rtrim($url, '/') . '/';
    }

    /**
     * 请求参数
     *
     * @return string
     */
    public function getRequestUri()
    {
        if (! is_null($this->requestUri)) {
            return $this->requestUri;
        }

        // For IIS
        $_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? $_SERVER["HTTP_X_REWRITE_URL"];

        if (isset($_SERVER['HTTP_X_REWRITE_URL'])) {
            $url = $_SERVER['HTTP_X_REWRITE_URL'];
        } elseif (isset($_SERVER['REQUEST_URI'])) {
            $url = $_SERVER['REQUEST_URI'];
        } elseif (isset($_SERVER['ORIG_PATH_INFO'])) {
            $url = $_SERVER['ORIG_PATH_INFO'];
            if (! empty($_SERVER['QUERY_STRING'])) {
                $url .= '?' . $_SERVER['QUERY_STRING'];
            }
        } else {
            $url = '';
        }

        return $this->requestUri = $url;
    }

    /**
     * 判断字符串是否为数字
     *
     * @param string $strSearch
     * @since bool
     */
    protected function isInt($mixValue)
    {
        if (is_int($mixValue)) {
            return true;
        }

        return ctype_digit(strval($mixValue));
    }

    /**
     * 设置单个环境变量
     *
     * @param string $strName
     * @param string|null $mixValue
     * @return void
     */
    protected function setEnvironmentVariable($strName, $mixValue = null)
    {
        if (is_bool($mixValue)) {
            putenv($strName . '=' . ($mixValue ? '(true)' : '(false)'));
        } elseif (is_null($mixValue)) {
            putenv($strName . '(null)');
        } else {
            putenv($strName . '=' . $mixValue);
        }
    }

    /**
     * pathinfo 处理
     *
     * @param string $pathInfo
     * @return string
     */
    protected function parsePathInfo($pathInfo)
    {
        if ($pathInfo && $this->getOption('html_suffix')) {
            $sSuffix = substr($this->getOption('html_suffix'), 1);
            $pathInfo = preg_replace('/\.' . $sSuffix . '$/', '', $pathInfo);
        }

        $pathInfo = empty($pathInfo) ? '/' : $pathInfo;

        return $pathInfo;
    }

    /**
     * 格式化查询参数
     * 
     * @param string $queryString
     * @return string
     */
    protected function normalizeQueryString($queryString)
    {
        if ('' == $queryString) {
            return '';
        }

        $parts = [];

        foreach (explode('&', $queryString) as $item) {
            if (strpos($item, static::PATHINFO_URL . '=') === 0) {
                continue;
            }

            $parts[] = $item;
        }

        return implode('&', $parts);
    }

    /**
     * 对象转数组
     *
     * @return array
     */
    public function toArray()
    {
        return $this->allAll();
    }

    /**
     * 实现 ArrayAccess::offsetExists
     *
     * @param string $strKey
     * @return mixed
     */
    public function offsetExists($strKey)
    {
        return array_key_exists($strKey, $this->allAll());
    }

    /**
     * 实现 ArrayAccess::offsetGet
     *
     * @param string $strKey
     * @return mixed
     */
    public function offsetGet($strKey)
    {
        return data_get($this->allAll(), $strKey);
    }

    /**
     * 实现 ArrayAccess::offsetSet
     *
     * @param string $strKey
     * @param mixed $mixValue
     * @return mixed
     */
    public function offsetSet($strKey, $mixValue)
    {
        $this->getMethod() == 'GET' ? $this->setGet($strKey, $mixValue) : $this->setRequest($strKey, $mixValue);
    }

    /**
     * 实现 ArrayAccess::offsetUnset
     *
     * @param string $strKey
     * @return void
     */
    public function offsetUnset($strKey)
    {
    }

    /**
     * 是否存在输入值
     *
     * @param string $strKey
     * @return bool
     */
    public function __isset($strKey)
    {
        return ! is_null($this->__get($strKey));
    }

    /**
     * 获取输入值
     *
     * @param string $strKey
     * @return mixed
     */
    public function __get($strKey)
    {
        $arrAll = $this->allAll();
        return $arrAll[$strKey] ?? null;
    }
}
