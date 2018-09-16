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

use Leevel\Http\IRequest;
use Leevel\Router\IRouter;

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
     * @var \Leevel\Router\IRouter
     */
    protected $router;

    /**
     * HTTP Request.
     *
     * @var \Leevel\Http\IRequest
     */
    protected $request;

    /**
     * 匹配中间件.
     *
     * @var array
     */
    protected $middlewares = [];

    /**
     * 匹配变量.
     *
     * @var array
     */
    protected $matchedVars = [];

    /**
     * 匹配数据项.
     *
     * @param \Leevel\Router\IRouter $router
     * @param \Leevel\Http\IRequest  $request
     *
     * @return array
     */
    public function matche(IRouter $router, IRequest $request): array
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
        $middlewares = [];
        $pathInfoSource = $pathInfo = rtrim($request->getPathInfo(), '/').'/';

        // 匹配基础路径
        foreach ($router->getBasePaths() as $item => $option) {
            if ('*' === $item) {
                if (isset($option['middlewares'])) {
                    $middlewares = $option['middlewares'];
                }
            } elseif (preg_match($item, $pathInfo, $matches)) {
                if (isset($option['middlewares'])) {
                    $middlewares = $this->mergeMiddlewares($middlewares, $option['middlewares']);
                }

                break;
            }
        }

        // 匹配分组路径
        foreach ($router->getGroupPaths() as $item => $option) {
            if (0 === strpos($pathInfo, $item)) {
                $pathInfo = substr($pathInfo, strlen($item));

                if (isset($option['middlewares'])) {
                    $middlewares = $this->mergeMiddlewares($middlewares, $option['middlewares']);
                }

                break;
            }
        }

        $this->middlewares = $middlewares;

        // 静态路由匹配
        if (isset($urlRouters['static'], $urlRouters['static'][$pathInfoSource])) {
            $urlRouters = $urlRouters['static'][$pathInfoSource];

            return $this->matcheSuccessed($urlRouters);
        }

        // 匹配首字母
        $firstLetter = $pathInfo[1];

        if (isset($urlRouters[$firstLetter])) {
            $urlRouters = $urlRouters[$firstLetter];
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
        if (!empty($routers['scheme']) &&
            false === $this->matcheScheme($routers['scheme'])) {
            return false;
        }

        // 域名匹配
        if (false === ($domainVars = $this->matcheDomain($routers))) {
            return false;
        }

        $result = $this->router->matchePath($routers['bind'], true);
        $exendParams = $result['params'] ?? [];
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
        if (isset($routers['params']) && is_array($routers['params'])) {
            $result['params'] = array_merge($result['params'], $routers['params']);
        }

        // 路由自身 foo/bar?foo=bar 自带参数
        if ($exendParams) {
            $result['params'] = array_merge($result['params'], $exendParams);
        }

        $result[IRouter::PARAMS] = $result['params'];

        // 中间件
        if (isset($routers['middlewares'])) {
            $this->middlewares = $this->mergeMiddlewares($this->middlewares, $routers['middlewares']);
        }

        $result[IRouter::MIDDLEWARES] = $this->middlewares;

        // 匹配的变量
        $result[IRouter::VARS] = $this->matchedVars;

        return $result;
    }

    /**
     * 合并中间件.
     *
     * @param array $middlewares
     * @param array $newMiddlewares
     *
     * @return array
     */
    protected function mergeMiddlewares(array $middlewares, array $newMiddlewares): array
    {
        return [
            'handle'    => array_unique(array_merge(
                $middlewares['handle'] ?? [],
                $newMiddlewares['handle'] ?? []
            )),
            'terminate' => array_unique(array_merge(
                $middlewares['terminate'] ?? [],
                $newMiddlewares['terminate'] ?? []
            )),
        ];
    }

    /**
     * 协议匹配.
     *
     * @param string $scheme
     *
     * @return bool
     */
    protected function matcheScheme(string $scheme): bool
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

        if (!empty($routers['domain'])) {
            $host = $this->request->getHost();

            if (!empty($routers['domain_regex'])) {
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
