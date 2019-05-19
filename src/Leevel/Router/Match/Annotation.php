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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Router\Match;

use Leevel\Http\IRequest;
use Leevel\Router\IRouter;

/**
 * 注解路由匹配.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.02.15
 *
 * @version 1.0
 */
class Annotation extends Match implements IMatch
{
    /**
     * 匹配变量.
     *
     * @var array
     */
    protected array $matchedVars = [];

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
        $this->setRouterAndRequest($router, $request);

        return $this->matchMain();
    }

    /**
     * 主匹配.
     *
     * @return array
     */
    protected function matchMain(): array
    {
        if (!($routers = $this->router->getRouters())) {
            return [];
        }

        // 匹配路由请求方法
        if (false === ($routers = $this->matcheMethod($routers))) {
            return [];
        }

        // 匹配 PathInfo
        $pathInfo = $this->matchePathInfo();

        // 静态路由匹配
        if (false !== ($result = $this->matcheStatic($routers))) {
            return $result;
        }

        // 匹配首字母
        if (false === ($routers = $this->matcheFirstLetter($pathInfo, $routers))) {
            return [];
        }

        // 匹配分组
        $routers = $this->matcheGroups($pathInfo, $routers);

        // 路由匹配
        if (false !== ($result = $this->matcheRegexGroups($routers))) {
            return $result;
        }

        return [];
    }

    /**
     * 匹配路由方法.
     *
     * @param array $routers
     *
     * @return array|false
     */
    protected function matcheMethod(array $routers)
    {
        $method = strtolower($this->request->getMethod());

        return $routers[$method] ?? false;
    }

    /**
     * 匹配静态路由.
     *
     * @param array $routers
     *
     * @return array|false
     */
    protected function matcheStatic(array $routers)
    {
        $pathInfo = $this->getPathInfo();

        if (isset($routers['static'], $routers['static'][$pathInfo])) {
            $routers = $routers['static'][$pathInfo];

            return $this->matcheSuccessed($routers);
        }

        return false;
    }

    /**
     * 匹配首字母.
     *
     * @param string $pathInfo
     * @param array  $routers
     *
     * @return array|false
     */
    protected function matcheFirstLetter(string $pathInfo, array $routers)
    {
        return $routers[$pathInfo[1]] ?? false;
    }

    /**
     * 匹配路由分组.
     *
     * @param string $pathInfo
     * @param array  $routers
     *
     * @return array
     */
    protected function matcheGroups(string $pathInfo, array $routers): array
    {
        $matchGroup = false;

        foreach ($this->router->getGroups() as $group) {
            if (0 === strpos($pathInfo, $group)) {
                $routers = $routers[$group];
                $matchGroup = true;

                break;
            }
        }

        if (false === $matchGroup) {
            $routers = $routers['_'] ?? [];
        }

        return $routers;
    }

    /**
     * 匹配路由正则分组.
     *
     * @param array $routers
     *
     * @return array|false
     */
    protected function matcheRegexGroups(array $routers)
    {
        $pathInfo = $this->getPathInfo();

        foreach ($routers['regex'] as $key => $regex) {
            if (!preg_match($regex, $pathInfo, $matches)) {
                continue;
            }

            $matchedRouter = $routers['map'][$key][count($matches)];
            $routers = $routers[$matchedRouter];
            $matcheVars = $this->matcheVariable($routers, $matches);

            return $this->matcheSuccessed($routers, $matcheVars);
        }

        return false;
    }

    /**
     * 注解路由匹配成功处理.
     *
     * @param array $routers
     * @param array $matcheVars
     *
     * @return array
     */
    protected function matcheSuccessed(array $routers, array $matcheVars = []): array
    {
        // 协议匹配
        if (!empty($routers['scheme']) &&
            false === $this->matcheScheme($routers['scheme'])) {
            return [];
        }

        // 域名匹配
        if (false === ($domainVars = $this->matcheDomain($routers))) {
            return [];
        }

        // 未绑定
        if (!$routers['bind']) {
            return [];
        }

        $result = [];

        $result[IRouter::BIND] = $routers['bind'];
        $result[IRouter::APP] = $this->findApp($routers['bind']);

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

        $result[IRouter::PARAMS] = $result['params'];
        unset($result['params']);

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
            // ignore the port
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

    /**
     * 查找 App.
     *
     * @param string $path
     *
     * @return string
     */
    protected function findApp(string $path): string
    {
        $tmp = explode('\\', $path);

        return array_shift($tmp);
    }
}
