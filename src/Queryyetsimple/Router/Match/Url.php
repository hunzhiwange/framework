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

namespace Leevel\Router\Match;

use Leevel\Http\Request;
use Leevel\Router\IRouter;
use Leevel\Router\Router;

/**
 * 路由 url 匹配.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.15
 *
 * @version 1.0
 */
class Url implements IMatch
{
    /**
     * Router.
     *
     * @var \Leevel\Router\Router
     */
    protected $router;

    /**
     * HTTP Request.
     *
     * @var \Leevel\Http\Request
     */
    protected $request;

    /**
     * 匹配基础路径.
     *
     * @var string
     */
    protected $matchedBasepath;

    /**
     * 匹配变量.
     *
     * @var array
     */
    protected $matchedVars = [];

    /**
     * 匹配数据项.
     *
     * @param \Leevel\Router\Router $router
     * @param \Leevel\Http\Request  $request
     *
     * @return array
     */
    public function matche(Router $router, Request $request): array
    {
        $urlRouters = $router->getRouters();

        if (!$urlRouters) {
            return [];
        }

        $this->request = $request;
        $this->router = $router;

        // 验证是否存在请求方法
        $method = strtolower($request->getMethod());
        if (!isset($urlRouters[$method])) {
            return [];
        }
        $urlRouters = $urlRouters[$method];

        $result = [];

        // 匹配基础路径
        $basepaths = $router->getBasepaths();
        $pathInfoSource = $pathInfo = $request->getPathInfo();

        foreach ($basepaths as $path) {
            if (0 === strpos($pathInfo, $path)) {
                $pathInfo = substr($pathInfo, strlen($path));
                $this->matchedBasepath = $path;

                break;
            }
        }

        // 静态路由匹配
        if (isset($urlRouters['static'], $urlRouters['static'][$pathInfoSource])) {
            $urlRouters = $urlRouters['static'][$pathInfoSource];

            return $this->matcheSuccessed($urlRouters);
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
            if (0 === strpos($pathInfo, $group)) {
                $urlRouters = $urlRouters[$group];
                $matchGroup = true;

                break;
            }
        }

        if (false === $matchGroup) {
            $urlRouters = $urlRouters['_'];
        }

        // 路由匹配
        foreach ($urlRouters['regex'] as $key => $regex) {
            if (!preg_match($regex, $pathInfoSource, $matches)) {
                continue;
            }

            $matchedRouter = $urlRouters['map'][$key][count($matches)];
            $routers = $urlRouters[$matchedRouter];

            $matcheVars = $this->matcheVariable($routers, $matches);

            return $this->matcheSuccessed($routers, $matcheVars);
        }

        return [];
    }

    /**
     * url 匹配成功处理.
     *
     * @param array $routers
     * @param array $matcheVars
     *
     * @return array|false
     */
    protected function matcheSuccessed(array $routers, array $matcheVars = [])
    {
        // 协议匹配
        if (false === $this->matcheScheme($routers['scheme'])) {
            return false;
        }

        // 域名匹配
        if (false === ($domainVars = $this->matcheDomain($routers))) {
            return false;
        }

        $result = $this->router->matchePath($routers['bind']);
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
        $result['params'][IRouter::BASEPATH] = $this->matchedBasepath;

        $result[IRouter::PARAMS] = $result['params'];
        unset($result['params']);

        // 中间件
        $result[IRouter::MIDDLEWARES] = $routers['middlewares'];

        // 匹配的变量
        $result[IRouter::VARS] = $this->matchedVars;

        return $result;
    }

    /**
     * 协议匹配.
     *
     * @param string $scheme
     *
     * @return bool
     */
    protected function matcheScheme(?string $scheme): bool
    {
        if ($scheme && $this->request->getScheme() !== $scheme) {
            return false;
        }

        return true;
    }

    /**
     * 域名匹配.
     *
     * @param array $routers
     *
     * @return array|bool
     */
    protected function matcheDomain(array $routers)
    {
        $domainVars = [];

        if ($routers['domain']) {
            //$host = $this->request->getHttpHost();
            $host = $this->request->getHost();

            if ($routers['domain_regex']) {
                if (!preg_match($routers['domain_regex'], $host, $matches)) {
                    return false;
                }
                array_shift($matches);

                foreach ($routers['domain_var'] as $var) {
                    $value = array_shift($matches);

                    $domainVars[$var] = $value;
                    $this->addVariable($var, $value);
                }
            } elseif ($host !== $routers['domain']) {
                return false;
            }
        }

        return $domainVars;
    }

    /**
     * 变量匹配处理.
     *
     * @param array $routers
     * @param array $matches
     *
     * @return array
     */
    protected function matcheVariable(array $routers, array $matches): array
    {
        $result = [];

        array_shift($matches);

        foreach ($routers['var'] as $key => $var) {
            $value = $matches[$key];

            $result[$var] = $matches[$key];
            $this->addVariable($var, $matches[$key]);
        }

        return $result;
    }

    /**
     * 添加解析变量.
     *
     * @param string $name
     * @param mixed  $value
     */
    protected function addVariable(string $name, $value): void
    {
        $this->matchedVars[$name] = $value;
    }
}
