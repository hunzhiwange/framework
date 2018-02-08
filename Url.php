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
namespace Queryyetsimple\Router;

use Queryyetsimple\Option\TClass;

/**
 * url 生成
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.01.10
 * @version 1.0
 */
class Url
{
    use TClass;

    /**
     * 路由解析后的 app
     * 
     * @var string
     */
    protected $app = 'home';

    /**
     * 路由解析后的 controller
     * 
     * @var string
     */
    protected $controller = 'index';

    /**
     * 路由解析后的 action
     * 
     * @var string
     */
    protected $action = 'index';

    /**
     * url 入口
     * 
     * @var string
     */
    protected $urlEnter;

    /**
     * 解析后的 MVC 参数
     * 
     * @var array
     */
    protected $parseMvc = [];

    /**
     * http 前缀
     * 
     * @var string
     */
    protected $httpPrefix;

    /**
     * http 后缀
     * 
     * @var string
     */
    protected $httpSuffix;

    /**
     * 生成 url 默认配置
     * 
     * @var array
     */
    protected $makeOption = [
        'suffix' => true,
        'normal' => false,
        'subdomain' => 'www'
    ];

    /**
     * 配置
     *
     * @var array
     */
    protected $option = [
        'default_app' => 'home',
        'default_controller' => 'index',
        'default_action' => 'index',
        'model' => 'pathinfo',
        'html_suffix' => '.html',
        'router_domain_top' => '',
        'make_subdomain_on' => true
    ];

    /**
     * 构造函数
     *
     * @param array $option
     * @return void
     */
    public function __construct(array $option = [])
    {
        $this->options($option);
    }

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
    public function make($url, $params = [], $option = [])
    {
        $option = array_merge($this->makeOption, $option);

        $custom =$this->isCustom($url);

        $this->parseMvc = $this->parseMvc($url, $params, $custom);

        if ($this->isNotNormal($option['normal'], $custom)) {
            if ($custom === false) {
                $url = $this->pathinfoUrl();
            } else {
                $url = $this->customUrl($url);
            }

            $url = $this->withSuffix($url, $option['suffix']);
        } else {
            $url = $this->normalUrl($option['normal']);
        }

        $url = $this->urlWithDomain($url, $option['subdomain']);

        return $url;
    }

    /**
     * 设置路由 app
     *
     * @param string $app
     * @return $this
     */
    public function setApp($app) {
        $this->app = $app;
        return $this;
    }

    /**
     * 设置路由 controller
     *
     * @param string $controller
     * @return $this
     */
    public function setController($controller) {
        $this->controller = $controller;
        return $this;
    }

    /**
     * 设置路由 action
     *
     * @param string $action
     * @return $this
     */
    public function setAction($action) {
        $this->action = $action;
        return $this;
    }

    /**
     * 设置路由 URL 入口
     *
     * @param string $urlEnter
     * @return $this
     */
    public function setUrlEnter($urlEnter) {
        $this->urlEnter = $urlEnter;
        return $this;
    }

    /**
     * pathinfo url 解析
     * 
     * @return string
     */
    protected function pathinfoUrl() 
    {
        $params = $this->parseMvc['params'];

        // 额外参数
        $paramUrl = '/';
        foreach ($params as $key => $val) {
            if (! is_scalar($val)) {
                if (is_array($val) && $val) {
                    $paramUrl .= implode('/', $val) . '/';
                }
                continue;
            }
            $paramUrl .= $key . '/' . urlencode($val) . '/';
        }
        $paramUrl = substr($paramUrl, 0, - 1);

        // 分析 url
        $url = $this->parseEnter() . (! $this->equalDefault('app') ? '/' . $this->parseMvc['app'] . '/' : '/');

        if ($paramUrl) {
            $url .= $this->parseMvc['controller'] . '/' . $this->parseMvc['action'] . $paramUrl;
        } else {
            $tmp = '';

            if (! $this->equalDefault('controller') || ! $this->equalDefault('action')) {
                $tmp .= $this->parseMvc['controller'];
            }
            if (! $this->equalDefault('action')) {
                $tmp .= '/' . $this->parseMvc['action'];
            }

            if ($tmp == '') {
                $url = rtrim($url, '/' . '/');
            } else {
                $url .= $tmp;
            }

            unset($tmp);
        }

        return $url;
    }

    /**
     * 自定义 URL
     * 
     * @param string $url
     * @return string
     */
    protected function customUrl(string $url) 
    {
        $params = $this->parseMvc['params'];

        if (strpos($url, '{') !== false) {
            $url = preg_replace_callback("/{(.+?)}/", function ($match) use (&$params) {
                if (isset($params[$match[1]])) {
                    $result = $params[$match[1]];
                    unset($params[$match[1]]);
                } else {
                    $result = $match[0];
                }

                return $result;
            }, $url);
        }

        // 额外参数
        $paramUrl = '/';
        foreach ($params as $key => $val) {
            if (! is_scalar($val)) {
                if (is_array($val) && $val) {
                    $paramUrl .= implode('/', $val) . '/';
                }
                continue;
            }
            $paramUrl .= $key . '/' . urlencode($val) . '/';
        }
        $paramUrl = substr($paramUrl, 0, - 1);

        $url .= $paramUrl;

        return $url;
    }

    /**
     * 普通 url 生成
     * 
     * @param bool $normal
     * @return string
     */
    protected function normalUrl(bool $normal) 
    {
        $params = $this->parseMvc['params'];

        $paramUrl = '';
        foreach ($params as $key => $val) {
            if (! is_scalar($val)) {
                if (is_array($val) && $val) {
                    $paramUrl .= implode('/', $val) . '/';
                }
                continue;
            }
            $paramUrl .= $key . '=' . urlencode($val) . '&';
        }
        $paramUrl = rtrim($paramUrl, '&');

        $tmp = [];
        if ($normal || ! $this->equalDefault('app')) {
            $tmp[] = Router::APP . '=' . $this->parseMvc['app'];
        }

        if (! $this->equalDefault('controller')) {
            $tmp[] = Router::CONTROLLER . '=' . $this->parseMvc['controller'];
        }
        if (! $this->equalDefault('action')) {
            $tmp[] = Router::ACTION . '=' . $this->parseMvc['action'];
        }
        if ($paramUrl) {
            $tmp[] = $paramUrl;
        }
        if (! empty($tmp)) {
            $tmp = '?' . implode('&', $tmp);
        }

        $url = $this->parseEnter($normal) . $tmp;

        unset($tmp);

        return $url;
    }

    /**
     * 解析 MVC 参数
     * 
     * @param string $url
     * @param array $params
     * @param bool $custom
     * @return array
     */
    protected function parseMvc(string $url, array $params, bool $custom) 
    {
        if ( $custom) {
            return [
                'params' => $params
            ];
        }

        if ($url != '') {
            if (! strpos($url, '://')) {
                $url = $this->app . '://' . $url;
            }
            $parse = parse_url($url);
        } else {
            $parse = [];
        }

        // app、controller 和 action
        $app = $parse['scheme'] ?? $this->app; 
        $controller = $action = null;
        
        $mvc = [
            Router::APP => 'app',
            Router::CONTROLLER => 'controller',
            Router::ACTION => 'action'
        ];

        foreach($mvc as $key => $item) {
            if (isset($params[$key])) {
                $$item = $params[$key];
                unset($params[$key]);
            }
        }

        if (isset($parse['path'])) {
            if (! $controller) {
                $controller = $parse['host'] ?? $this->controller;
            }

            if (! $action) {
                $action = substr($parse['path'], 1);
            }
        } else {
            if (! $controller) {
                $controller = $parse['host'] ?? $this->controller;
            }

            if (! $action) {
                $action = $this->action;
            }
        }

        // 如果指定了查询参数
        if (isset($parse['query'])) {
            $tmp = [];
            parse_str($parse['query'], $tmp);
            $params = array_merge($tmp, $params);
        }

        return [
            'app' => $app,
            'controller' => $controller,
            'action' => $action,
            'params' => $params
        ];
    }

    /**
     * 返回完整 URL 地址
     *
     * @param string $url
     * @param string $domain
     * @return string
     */
    protected function urlWithDomain(string $url, string $domain)
    {
        if ($this->getOption('make_subdomain_on') !== true || ! $this->getOption('router_domain_top') || 
            ! $domain) {
            return $url;
        }

        if (is_null($this->httpPrefix)) {
            $this->httpPrefix = $this->isSsl() ? 'https://' : 'http://';
            $this->httpSuffix = $this->getOption('router_domain_top');
        }

        return $this->httpPrefix . ($domain && $domain != '*' ? $domain . '.' : '') . $this->httpSuffix . $url;
    }

    /**
     * 是否启用 https
     *
     * @return boolean
     */
    public function isSsl()
    {
        if (isset($_SERVER['HTTPS']) && ('1' == $_SERVER['HTTPS'] || 'on' == strtolower($_SERVER['HTTPS']))) {
            return true;
        } elseif (isset($_SERVER['SERVER_PORT']) && ('443' == $_SERVER['SERVER_PORT'])) {
            return true;
        }
        return false;
    }

    /**
     * 是否非默认 url
     * 
     * @param bool $normal
     * @param bool $custom
     * @return boolean
     */
    protected function isNotNormal(bool $normal, bool $custom) {
        return ($this->getOption('model') == 'pathinfo' && $normal === false) || $custom === true;
    }

    /**
     * url 带后缀
     * 
     * @param string $url
     * @param string|boolean $suffix
     * @return string
     */
    protected function withSuffix($url, $suffix) {
        if ($suffix && $url) {
            $url .= $suffix === true ? $this->getOption('html_suffix') : $suffix;
        }

        return $url;
    }

    /**
     * 是否为默认 app、controller 或则 action
     * 
     * @param string $type
     * @return boolean
     */
    protected function equalDefault(string $type) {
        return $this->getOption('default_' . $type) === $this->parseMvc[$type];
    }

    /**
     * 分析入口文件
     * 
     * @param boolean $normal
     * @return string
     */
    protected function parseEnter(bool $normal = false) {
        return $normal === true || $this->urlEnter !== '/' ? $this->urlEnter : '';
    }

    /**
     * 以 “/” 开头的为自定义 URL
     * 
     * @param string $url
     * @return boolean
     */
    protected function isCustom(string $url) {
        return 0 === strpos($url, '/');
    }
}
