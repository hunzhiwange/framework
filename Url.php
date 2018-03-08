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

use Queryyetsimple\{
    Http\IRequest,
    Option\TClass
};

/**
 * url 生成
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.01.10
 * @version 1.0
 */
class Url implements IUrl
{
    use TClass;

    /**
     * HTTP 请求
     * 
     * @var \Queryyetsimple\Http\IRequest
     */
    protected $request;

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
     * @param \Queryyetsimple\Http\IRequest $request
     * @param array $option
     * @return void
     */
    public function __construct(IRequest $request, array $option = [])
    {
        $this->request = $request;

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
    public function make(string $url, array $params = [], array $option = [])
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

            if ($this->parseMvc['params']) {
                $url .= '?' . http_build_query($this->parseMvc['params']);
            }
        } else {
            $url = $this->normalUrl($option['normal']);
        }

        $url = $this->urlWithDomain($url, $option['subdomain']);

        return $url;
    }

    /**
     * 返回 HTTP 请求
     * 
     * @return \Queryyetsimple\Http\IRequest
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * pathinfo url 解析
     * 
     * @return string
     */
    protected function pathinfoUrl() 
    {
        $url = $this->parseEnter() . 
            (! $this->equalDefault('app') ? '/' . $this->parseMvc['app'] . '/' : '/');

        if ($this->parseMvc['prefix']) {
            $this->parseMvc['action'] = str_replace('\\', '/', $this->parseMvc['prefix']) . '/' . 
                $this->parseMvc['action'];
        }

        if (! $this->equalDefault('controller') || ! $this->equalDefault('action')) {
            $url .= $this->parseMvc['controller'];
        }

        if (! $this->equalDefault('action')) {
            $url .= '/' . $this->parseMvc['action'];
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
        if (strpos($url, '{') !== false) {
            $url = preg_replace_callback("/{(.+?)}/", function ($match) {
                if (isset($this->parseMvc['params'][$match[1]])) {
                    $result = $this->parseMvc['params'][$match[1]];
                    unset($this->parseMvc['params'][$match[1]]);
                } else {
                    $result = $match[0];
                }

                return $result;
            }, $url);
        }

        if (strpos($url, '?') !== false) {
            $tmp = explode('?', $url);
            $url = $tmp[0];

            parse_str($tmp[1], $tmpQuery);
            
            foreach ($this->parseMvc['params'] as $k => $v) {
                $tmpQuery[$k] = $v;
            }

            $this->parseMvc['params'] = $tmpQuery;
        }

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
        $querys = [];

        if ($normal || ! $this->equalDefault('app')) {
            $querys[IRouter::APP] = $this->parseMvc['app'];
        }

        if (! $this->equalDefault('controller')) {
            $querys[IRouter::CONTROLLER] = $this->parseMvc['controller'];
        }

        if (! $this->equalDefault('action')) {
            $querys[IRouter::ACTION] = $this->parseMvc['action'];
        }

        if ($this->parseMvc['prefix']) {
            $querys[IRouter::PREFIX] = $this->parseMvc['prefix'];
        }

        $params = $this->parseMvc['params'];

        foreach ($params as $key => $val) {
            $querys[$key] = $val;
        }

        $url = $this->parseEnter($normal) . ($querys ? '?' . http_build_query($querys) : '');

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
        if ($custom) {
            return [
                'params' => $params
            ];
        }

        if (! in_array($url, ['', '/'])) {
            if (! strpos($url, '://')) {
                $url = $this->request->app() . '://' . $url;
            }
            $parse = parse_url($url);
        } else {
            $parse = [];
        }

        $result = [
            'app' => '',
            'controller' => '',
            'action' => '',
            'params' => [],
            'prefix' => ''
        ];

        if ($url === '/') {
            $result['app'] = $this->getOption('default_app');
            $result['controller'] = $this->getOption('default_controller');
            $result['action'] = $this->getOption('default_action');
        }

        // app、controller 和 action
        $result['app'] = $parse['scheme'] ?? $this->request->app();
        
        $mvc = [
            IRouter::APP => 'app',
            IRouter::CONTROLLER => 'controller',
            IRouter::ACTION => 'action'
        ];

        foreach($mvc as $key => $item) {
            if (isset($params[$key])) {
                $result[$item] = $params[$key];
                unset($params[$key]);
            }
        }

        if (isset($parse['path'])) {
            if (! $result['controller']) {
                $result['controller'] = $parse['host'] ?? $this->request->controller();
            }

            if (! $result['action']) {
                $result['action'] = substr($parse['path'], 1);
            }
        } else {
            if (! $result['controller']) {
                $result['controller'] = $parse['host'] ?? $this->request->controller();
            }

            if (! $result['action']) {
                $result['action'] = $this->request->action();
            }
        }

        if (strpos($result['action'], '/') !== false) {
            $tmpAction = explode('/', $result['action']);
            $result['action'] = array_pop($tmpAction);
            $result['prefix'] = implode('\\', $tmpAction);
        }

        if (isset($parse['query'])) {
            parse_str($parse['query'], $tmpQuery);
            
            foreach ($params as $k => $v) {
                $tmpQuery[$k] = $v;
            }

            $result['params'] = $tmpQuery;
        } else {
            $result['params'] = $params;
        }

        return $result;
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
        if ($this->getOption('make_subdomain_on') !== true || 
            ! $this->getOption('router_domain_top') || 
            ! $domain) {
            return $url;
        }

        if (is_null($this->httpPrefix)) {
            $this->httpPrefix = $this->isSecure() ? 'https://' : 'http://';
            $this->httpSuffix = $this->getOption('router_domain_top');
        }

        return $this->httpPrefix . 
            ($domain && $domain != '*' ? $domain . '.' : '') . 
            $this->httpSuffix . 
            $url;
    }

    /**
     * 是否启用 https
     *
     * @return boolean
     */
    protected function isSecure()
    {
        return $this->request->isSecure();
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

        if ($suffix && $url && $url != '/') {
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
        $enter = $this->request->getEnter();

        return $normal === true || $enter !== '/' ? $enter : '';
    }

    /**
     * 以 “/” 开头的为自定义 URL
     * 
     * @param string $url
     * @return boolean
     */
    protected function isCustom(string $url) {
        return $url !== '/' &&  0 === strpos($url, '/');
    }
}
