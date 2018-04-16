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
     * 匹配变量
     * 
     * @var array
     */
    protected $matcheVars = [];

    /**
     * 匹配数据项
     *
     * @param \Leevel\Router\Router $route
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

        $result = [];
        $pathInfo = $request->getPathInfo();
        $domainRouters = $router->getDomainRouters();
        $method = strtolower($request->getMethod());

        if (! isset($urlRouters[$method])) {
            return [];
        }

        $urlRouters = $urlRouters[$method];

        $basepaths = $router->getBasepaths();

        $basepath = '';
        foreach ($basepaths as $path) {
            if (strpos($pathInfo, $path) === 0) {
                $pathInfo = substr($pathInfo, strlen($path));
                $basepath = $path;
                break;
            }
        }

        $firstLetter = $pathInfo[1];

        if (isset($urlRouters[$firstLetter])) {
            $urlRouters = $urlRouters[$firstLetter];
        } elseif (isset($urlRouters['_'])) {
            $urlRouters = $urlRouters['_'];
        }

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

        $pathInfo = $basepath . $pathInfo;

        if (isset($urlRouters[$pathInfo])) {
            $urlRouters = $urlRouters[$pathInfo];

            $result = $this->matcheSuccessed($urlRouters);

            return $result;
        } else {

        }

        // 匹配路由
        foreach ($urlRouters as $rule => $routers) {
            $matcheVars = $this->variable($rule, $routers, $pathInfo);

            // URL 匹配成功
            if ($this->matcheUrl === true) {
                $result = $this->matcheSuccessed($routers,$matcheVars,$basepath);
                break;
            }
        }

        print_r($result);

        return $result;
    }

    /**
     * url 匹配成功处理
     * 
     * @param array $routers
     * @param array $matcheVars
     * @return array
     */
    protected function matcheSuccessed(array $routers, array $matcheVars=[],$basepath = '')
    {

        $domainVars = [];

        // 协议匹配
        if ($routers['scheme'] && $this->request->getScheme() !== $routers['scheme']) {
            return [];
        }

        // 域名匹配
        if ($routers['domain']) {
            //$host = $this->request->getHttpHost();
            $host = $this->request->getHost();

            if ($routers['domain_regex']) {
                if(! preg_match($routers['domain_regex'], $host, $matches)) {
                    return [];
                } else {
                    array_shift($matches);

                    foreach ($routers['domain_var'] as $var) {
                        $value = array_shift($matches);

                        $domainVars[$var] = $value;
                        $this->addVariable($var, $value);
                    }
                }
            } elseif ($host !== $routers['domain']) {
                return [];
            }
        }

        $result = $this->router->parseNodeUrl($routers['bind']);

        if ($domainVars) {
            $result[Router::PARAMS] = array_merge($domainVars, $result[Router::PARAMS]);
        }

        if ($matcheVars) {
            $result[Router::PARAMS] = array_merge($matcheVars, $result[Router::PARAMS]);
        }

        // 额外参数
        if ($routers[Router::PARAMS]) {
            $result[Router::PARAMS] = array_merge($result[Router::PARAMS], $routers[Router::PARAMS]);
        }

        if ($basepath) {
            $result[Router::PARAMS]['_basepath'] = $basepath;
        }

        return $result;
    }

    /**
     * 变量匹配处理
     * 
     * @param array $routers
     * @param string $pathInfo
     * @return array
     */
    protected function variable(string $rule, array $routers, string $pathInfo)
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
