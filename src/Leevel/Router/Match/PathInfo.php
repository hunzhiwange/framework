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
 * PathInfo 路由匹配.
 */
class PathInfo extends Match implements IMatch
{
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
        // 匹配 PathInfo
        $path = $this->normalizePath($this->matchePathInfo());

        // 应用
        list($result, $path) = $this->matcheApp($path);

        // Middleware
        $result[IRouter::MIDDLEWARES] = $this->middlewares;

        if (!$path) {
            return $result;
        }

        // MVC
        $result = array_merge($result, $this->matcheMvc($path));

        return $result;
    }

    /**
     * 格式化 PathInfo.
     */
    protected function normalizePath(string $pathInfo): array
    {
        $pathInfo = trim($pathInfo, '/');

        return $pathInfo ? explode('/', $pathInfo) : [];
    }

    /**
     * 匹配路由应用.
     */
    protected function matcheApp(array $path): array
    {
        $result = [];
        if ($path && $this->isFindApp($path[0])) {
            $result[IRouter::APP] = substr(array_shift($path), 1);
        }

        if ($restfulResult = $this->matcheRestful($path)) {
            return [array_merge($result, $restfulResult), []];
        }

        if (!$path) {
            $result[IRouter::CONTROLLER] = IRouter::DEFAULT_CONTROLLER;
        }

        return [$result, $path];
    }

    /**
     * 匹配路由 Mvc.
     */
    protected function matcheMvc(array $path): array
    {
        $result = [];
        if (1 === count($path)) {
            $result[IRouter::CONTROLLER] = array_pop($path);

            return $result;
        }

        if ($path) {
            $result[IRouter::ACTION] = array_pop($path);
        }

        if ($path) {
            $result[IRouter::CONTROLLER] = array_pop($path);
        }

        if ($path) {
            $result[IRouter::PREFIX] = $path;
        }

        return $result;
    }

    /**
     * 是否找到 app.
     */
    protected function isFindApp(string $path): bool
    {
        return 0 === strpos($path, ':');
    }

    /**
     * 匹配路由 Restful.
     */
    protected function matcheRestful(array $path): array
    {
        $restfulPath = implode('/', $path);
        $regex = '/^(\S+)\/('.IRouter::RESTFUL_REGEX.')(\/*\S*)$/';
        if (!preg_match($regex, $restfulPath, $matches)) {
            return [];
        }

        $result[IRouter::CONTROLLER] = $matches[1];
        $result[IRouter::ATTRIBUTES][IRouter::RESTFUL_ID] = $matches[2];
        if ('' !== $matches[3]) {
            $result[IRouter::ACTION] = substr($matches[3], 1);
        }

        return $result;
    }
}
