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
namespace Queryyetsimple\Router\Match;

use Queryyetsimple\Http\Request;
use Queryyetsimple\Router\Router;

/**
 * 路由域名匹配
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.02.12
 * @version 1.0
 */
class Domain
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
     * 是否匹配域名
     * 
     * @var boolean
     */
    protected $matcheDomain = false;
    
    /**
     * 匹配数据项
     *
     * @param \Queryyetsimple\Router\Router $route
     * @param \Queryyetsimple\Http\Request $request
     * @return array
     */
    public function matche(Router $router, Request $request)
    {
        $domains = $router->getDomains();
        $topLevelDomain = $router->getOption('router_domain_top');

        if ($router->getOption('router_domain_on') !== true || ! $domains || ! $topLevelDomain) {
            return [];
        }

        $this->request = $request;
        $this->router = $router;
        
        $host = $request->getHttpHost();
        
        foreach ($domains as $rule => $routers) {
            $rule = $this->rule($rule, $topLevelDomain);
            
            // 直接匹配成功
            if ($host === $rule) {
                $this->matcheDomain = true;
            }            

            // 变量支持
            elseif (strpos($rule, '{') !== false) {
                $this->variable($rule, $routers, $host);
            }
            
            // 域名匹配成功
            if ($this->matcheDomain === true) {
                return $this->matcheSuccessed($routers);
            }
        }
    }
    
    /**
     * 域名匹配成功处理
     * 
     * @param array $routers
     * @return array
     */
    protected function matcheSuccessed(array $routers)
    {
        if (isset($routers['rule'])) {
            $this->router->setDomainRouters(array_column($routers['rule'], 'url'));
            
            return [];
        } else {
            $result = $this->router->parseNodeUrl($routers['main']['url']);
            
            // 额外参数
            if (is_array($routers['main']['params']) && $routers['main']['params']) {
                $result = array_merge($result, $routers['main']['params']);
            }
            
            // 合并域名匹配数据
            $result = array_merge($this->router->getDomainData(), $result);
            
            return $result;
        }
    }
    
    /**
     * 变量匹配处理
     * 
     * @param string $rule
     * @param array $routers
     * @param string $host
     * @return void
     */
    protected function variable(string $rule, array $routers, string $host)
    {
        list($rule, $routerVar) = $this->ruleRegex($rule, $routers);
        
        if (preg_match($rule, $host, $matches)) {
            $this->matcheVariable($routerVar, $matches);
            
            $this->matcheDomain = true;
        }
    }
    
    /**
     * 匹配解析变量
     * 
     * @param array $routerVar
     * @param array $matches
     * @return void
     */
    protected function matcheVariable(array $routerVar, array $matches)
    {
        if ($routerVar) {
            array_shift($matches);
            
            foreach ($routerVar as $key => $name) {
                $this->addVariable($name, $matches[$key]);
            }
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
        $this->router->addDomainData($name, $value);
        $this->router->addVariable($name, $value);
        $this->request->params->set($name, $value);
    }
    
    /**
     * 路由正则
     *
     * @param string $rule
     * @param array $routers
     * @return array
     */
    protected function ruleRegex(string $rule, array $routers)
    {
        $routerVar = [];
        
        $rule = $this->router->formatRegex($rule);
        
        $rule = preg_replace_callback("/{(.+?)}/", function ($matches) use($routers, &$routerVar) {
            $routerVar[] = $matches[1];
            return '(' . ($routers['domain_where'][$matches[1]] ?? Router::DEFAULT_REGEX) . 
            ')';
        }, $rule);
        
        $rule = '/^' . $rule . '$/';
        
        return [
            $rule, 
            $routerVar
        ];
    }
    
    /**
     * 域名路由规则
     * 如果没有设置域名，则加上顶级域名
     * 
     * @param string $rule
     * @param string $topLevelDomain
     * @return string
     */
    protected function rule(string $rule, string $topLevelDomain)
    {
        if (strpos($rule, $topLevelDomain) === false) {
            $rule = $rule . '.' . $topLevelDomain;
        }
        
        return $rule;
    }
}
