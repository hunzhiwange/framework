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
namespace Leevel\Router\Match;

use Leevel\Http\Request;
use Leevel\Router\Router;

/**
 * 路由 url 匹配
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.02.15
 * @version 1.0
 */
class Url
{

    /**
     * Router
     * 
     * @var \Leevel\Router\Router
     */
    protected $router;
    
    /** 
     * HTTP Request
     * 
     * @var \Leevel\Http\Request
     */
    protected $request;

    /**
     * 是否匹配 URL
     * 
     * @var boolean
     */
    protected $matcheUrl = false;

    /**
     * 匹配基础路径
     * 
     * @var string
     */
    protected $matcheBasepath;

    /**
     * 匹配数据项
     *
     * @param \Leevel\Router\Router $router
     * @param \Leevel\Http\Request $request
     * @return array
     */
    public function matche(Router $router, Request $request)
    {
        $urlRouters = $router->getRouters();

        if (! $urlRouters) {
            return [];
        }

        $this->request = $request;
        $this->router = $router;

        // 验证是否存在请求方法
        $method = strtolower($request->getMethod());
        if (! isset($urlRouters[$method])) {
            return [];
        }
        $urlRouters = $urlRouters[$method];

        $result = [];
        
        // 匹配基础路径
        $basepaths = $router->getBasepaths();
        $pathInfo = $request->getPathInfo();
        foreach ($basepaths as $path) {
            if (strpos($pathInfo, $path) === 0) {
                $pathInfo = substr($pathInfo, strlen($path));
                $this->matcheBasepath = $path;
                break;
            }
        }

        // 匹配首字母
        $firstLetter = $pathInfo[1];
        if (isset($urlRouters[$firstLetter])) {
            $urlRouters = $urlRouters[$firstLetter];
        } elseif (isset($urlRouters['_'])) {
            $urlRouters = $urlRouters['_'];
        } else {
            return [];
        }

        // 匹配分组
        $groups = $router->getGroups();
        $matchGroup = false;
        foreach ($groups as $group) {
            if (strpos($pathInfo, $group) === 0) {
                $urlRouters = $urlRouters[$group];
                $matchGroup = true;
                break;
            }
        }

        if ($matchGroup === false) {
            $urlRouters = $urlRouters['_'];
        }

        // 直接匹配成功
        $pathInfo = $this->matcheBasepath . $pathInfo;
        if (isset($urlRouters[$pathInfo])) {
            $urlRouters = $urlRouters[$pathInfo];
            return $this->matcheSuccessed($urlRouters);
        }

        // 匹配路由
        foreach ($urlRouters as $routers) {
            $matcheVars = $this->matcheVariable($routers, $pathInfo);

            if ($this->matcheUrl === true) {
                $result = $this->matcheSuccessed($routers, $matcheVars);
                break;
            }
        }

        return $result ?: [];
    }

    /**
     * url 匹配成功处理
     * 
     * @param array $routers
     * @param array $matcheVars
     * @return array|false
     */
    protected function matcheSuccessed(array $routers, array $matcheVars = [])
    {
        // 协议匹配
        if ($this->matcheScheme($routers['scheme']) === false) {
            return false;
        }

        // 域名匹配
        if (($domainVars = $this->matcheDomain($routers)) === false) {
            return false;
        }

        $result = $this->router->parseNodeUrl($routers['bind']);
        $exendParams = $result['params'];
        $result['params'] = [];

        // 域名匹配参数 {subdomain}.{domain}
        if ($domainVars) {
            $result['params'] = $domainVars;
        }

        // 路由匹配参数 /v1/pet/{id}
        if ($matcheVars) {
            $result['params'] = array_merge($result['params'], $matcheVars);
        }

        // 额外参数 ['extend1' => 'foo']
        if ($routers['params']) {
            $result['params'] = array_merge($result['params'], $routers['params']);
        }

        // 路由自身 foo/bar?foo=bar 自带参数
        if ($exendParams) {
            $result['params'] = array_merge($result['params'], $exendParams);
        }

        // 基础路径匹配 /v1
        $result['params'][Router::BASEPATH] = $this->matcheBasepath;

        $result[Router::PARAMS] = $result['params'];
        unset($result['params']);

        // 中间件
        $result[Router::MIDDLEWARES] = $routers['middlewares'];

        // 路由绑定
        $result[Router::BIND] = $routers['bind'];

        return $result;
    }

    /**
     * 协议匹配
     * 
     * @param string $scheme
     * @return boolean
     */
    protected function matcheScheme($scheme) {
        if ($scheme && $this->request->getScheme() !== $scheme) {
            return false;
        }

        return true;
    }

    /**
     * 域名匹配
     * 
     * @param array $routers
     * @return boolean|array
     */
    protected function matcheDomain(array $routers) {
        $domainVars = [];

        if ($routers['domain']) {
            //$host = $this->request->getHttpHost();
            $host = $this->request->getHost();

            if ($routers['domain_regex']) {
                if(! preg_match($routers['domain_regex'], $host, $matches)) {
                    return false;
                } else {
                    array_shift($matches);

                    foreach ($routers['domain_var'] as $var) {
                        $value = array_shift($matches);

                        $domainVars[$var] = $value;
                        $this->addVariable($var, $value);
                    }
                }
            } elseif ($host !== $routers['domain']) {
                return false;
            }
        }

        return $domainVars;
    }

    /**
     * 变量匹配处理
     * 
     * @param array $routers
     * @param string $pathInfo
     * @return array
     */
    protected function matcheVariable(array $routers, string $pathInfo)
    {
        $result = [];

        if (preg_match($routers['regex'], $pathInfo, $matches)) {
            array_shift($matches);

            foreach ($routers['var'] as $var) {
                $value = array_shift($matches);

                $result[$var] = $value;
                $this->addVariable($var, $value);
            }

            $this->matcheUrl = true;
        }

        return $result;
    }

    /**
     * 添加解析变量
     * 
     * @param string $name
     * @param mixed $value
     * @return void
     */
    protected function addVariable(string $name, $value)
    {
        $this->router->addVariable($name, $value);
    }
}
