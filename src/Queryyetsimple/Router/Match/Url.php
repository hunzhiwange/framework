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
namespace Queryyetsimple\Router\Match;

use Queryyetsimple\Http\Request;
use Queryyetsimple\Router\Router;

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
     * @var \Queryyetsimple\Router\Router
     */
    protected $router;
    
    /** 
     * HTTP Request
     * 
     * @var \Queryyetsimple\Http\Request
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
    protected $matches = [];

    /**
     * 匹配数据项
     *
     * @param \Queryyetsimple\Router\Router $route
     * @param \Queryyetsimple\Http\Request $request
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

        // 匹配路由
        foreach ($urlRouters as $rule => $item) {

            // 域名和 url 同时匹配
            if ($domainRouters && ! in_array($rule, $domainRouters)) {
                continue;
            }

            foreach ($item as $routers) {
                if ($routers['regex'] == $pathInfo) {
                    $this->matcheUrl = true;
                } else {
                    $this->variable($routers, $pathInfo);
                }

                // URL 匹配成功
                if ($this->matcheUrl === true) {
                    $result = $this->matcheSuccessed($routers);
                    break 2;
                }
            }
        }

        // 合并域名匹配数据
        $result = array_merge($router->getDomainData(), $result);

        return $result;
    }

    /**
     * url 匹配成功处理
     * 
     * @param array $routers
     * @return array
     */
    protected function matcheSuccessed(array $routers)
    {
        $result = $this->router->parseNodeUrl($routers['url']);

        // 额外参数
        if (is_array($routers['params']) && $routers['params']) {
            $result = array_merge($result, $routers['params']);
        }

        // 变量解析
        if (isset($routers['args'])) {
            array_shift($routers);

            foreach ($routers['args'] as $key => $name) {
                $value = $this->matches[$key];

                $result[$name] = $value;
                $this->addVariable($name, $value);
            }
        }

        return $result;
    }

    /**
     * 变量匹配处理
     * 
     * @param array $routers
     * @param string $pathInfo
     * @return void
     */
    protected function variable(array $routers, string $pathInfo)
    {
        list($rule, $routerVar) = $this->ruleRegex($routers);
        
        if (preg_match($rule, $pathInfo, $matches)) {
            $this->matches = $matches;
            
            $this->matcheUrl = true;
        }
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
        $this->request->params->set($name, $value);
    }

    /**
     * 路由正则
     * 
     * @param array $routers
     * @return array
     */
    protected function ruleRegex(array $routers)
    {
        $routerVar = [];
        
        $rule = $this->router->formatRegex($routers['regex']);
        
        $rule = preg_replace_callback("/{(.+?)}/", function ($matches) use($routers, &$routerVar) {
            $routerVar[] = $matches[1];
            return '(' . ($routers['where'][$matches[1]] ?? Router::DEFAULT_REGEX) . 
            ')';
        }, $rule);
        
        $rule = '/^\/' . $rule . (($routers['strict'] ?? $this->router->getOption('router_strict')) ? '$' : '') . '/';
        
        return [
            $rule, 
            $routerVar
        ];
    }
}
