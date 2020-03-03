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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Router\Match;

use Leevel\Http\Request;
use Leevel\Router\IRouter;

/**
 * 注解路由匹配.
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
     */
    public function matche(IRouter $router, Request $request): array
    {
        $this->setRouterAndRequest($router, $request);

        return $this->matchMain();
    }

    /**
     * 主匹配.
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
     * @return array|false
     */
    protected function matcheStatic(array $router)
    {
        $pathInfo = $this->getPathInfo();
        if (isset($router['static'], $router['static'][$pathInfo])) {
            return $this->matcheSuccessed($router['static'][$pathInfo]);
        }

        return false;
    }

    /**
     * 匹配首字母.
     *
     * @return array|false
     */
    protected function matcheFirstLetter(string $pathInfo, array $router)
    {
        return $router[$pathInfo[1]] ?? false;
    }

    /**
     * 匹配路由分组.
     */
    protected function matcheGroups(string $pathInfo, array $router): array
    {
        $matchGroup = false;
        foreach ($this->router->getGroups() as $group) {
            if (0 === strpos($pathInfo, $group)) {
                $router = $router[$group];
                $matchGroup = true;

                break;
            }
        }

        if (false === $matchGroup) {
            $router = $router['_'] ?? [];
        }

        return $router;
    }

    /**
     * 匹配路由正则分组.
     *
     * @return array|false
     */
    protected function matcheRegexGroups(array $router)
    {
        $pathInfo = $this->getPathInfo();
        foreach ($router['regex'] as $key => $regex) {
            if (!preg_match($regex, $pathInfo, $matches)) {
                continue;
            }

            $matchedRouter = $router['map'][$key][count($matches)];
            $router = $router[$matchedRouter];

            return $this->matcheSuccessed($router, $this->matcheVariable($router, $matches));
        }

        return false;
    }

    /**
     * 注解路由匹配成功处理.
     */
    protected function matcheSuccessed(array $routers, array $matcheVars = []): array
    {
        // 协议匹配
        if (!empty($routers['scheme']) &&
            false === $this->matcheScheme($routers['scheme'])) {
            return [];
        }

        // 端口匹配
        if (!empty($routers['port']) &&
            false === $this->matchePort($routers['port'])) {
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
        $result['attributes'] = [];

        // 域名匹配参数 {subdomain}.{domain}
        if ($domainVars) {
            $result['attributes'] = $domainVars;
        }

        // 路由匹配参数 /v1/pet/{id}
        if ($matcheVars) {
            $result['attributes'] = array_merge($result['attributes'], $matcheVars);
        }

        // 额外参数 ['extend1' => 'foo']
        if (isset($routers['attributes']) && is_array($routers['attributes'])) {
            $result['attributes'] = array_merge($result['attributes'], $routers['attributes']);
        }

        $result[IRouter::ATTRIBUTES] = $result['attributes'];
        unset($result['attributes']);

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
     */
    protected function matcheScheme(string $scheme): bool
    {
        return $scheme === $this->request->getScheme();
    }

    /**
     * 端口匹配.
     */
    protected function matchePort(int $port): bool
    {
        return $port === (int) $this->request->getPort();
    }

    /**
     * 域名匹配.
     *
     * @return array|bool
     */
    protected function matcheDomain(array $router)
    {
        if (empty($router['domain'])) {
            return [];
        }

        if (!empty($router['domain_regex'])) {
            if (!preg_match($router['domain_regex'], $this->request->getHost(), $matches)) {
                return false;
            }

            $domainVars = [];
            array_shift($matches);
            foreach ($router['domain_var'] as $var) {
                $value = array_shift($matches);
                $domainVars[$var] = $value;
                $this->addVariable($var, $value);
            }

            return $domainVars;
        }

        return $this->request->getHost() !== $router['domain'] ? false : [];
    }

    /**
     * 变量匹配处理.
     */
    protected function matcheVariable(array $router, array $matches): array
    {
        $result = [];
        array_shift($matches);
        foreach ($router['var'] as $key => $var) {
            $result[$var] = $matches[$key];
            $this->addVariable($var, $matches[$key]);
        }

        return $result;
    }

    /**
     * 添加解析变量.
     *
     * @param mixed $value
     */
    protected function addVariable(string $name, $value): void
    {
        $this->matchedVars[$name] = $value;
    }

    /**
     * 查找 App.
     */
    protected function findApp(string $path): string
    {
        return array_shift(explode('\\', $path));
    }
}
