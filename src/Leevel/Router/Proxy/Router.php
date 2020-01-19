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

namespace Leevel\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Http\IResponse;
use Leevel\Http\Request;
use Leevel\Router\Router as BaseRouter;

/**
 * 代理 router.
 *
 * @codeCoverageIgnore
 */
class Router
{
    /**
     * call.
     *
     * @return mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 分发请求到路由.
     */
    public static function dispatch(Request $request): IResponse
    {
        return self::proxy()->dispatch($request);
    }

    /**
     * 初始化请求.
     */
    public static function initRequest(): void
    {
        self::proxy()->initRequest();
    }

    /**
     * 设置路由请求预解析结果.
     *
     * - 可以用于高性能 Rpc 和 Websocket 预匹配数据.
     */
    public static function setPreRequestMatched(Request $request, array $matchedData): void
    {
        self::proxy()->setPreRequestMatched($request, $matchedData);
    }

    /**
     * 穿越中间件.
     */
    public static function throughMiddleware(Request $passed, array $passedExtend = []): void
    {
        self::proxy()->throughMiddleware($passed, $passedExtend);
    }

    /**
     * 设置控制器相对目录.
     */
    public static function setControllerDir(string $controllerDir): void
    {
        self::proxy()->setControllerDir($controllerDir);
    }

    /**
     * 返回控制器相对目录.
     */
    public static function getControllerDir(): string
    {
        return self::proxy()->getControllerDir();
    }

    /**
     * 设置路由.
     */
    public static function setRouters(array $routers): void
    {
        self::proxy()->setRouters($routers);
    }

    /**
     * 取得当前路由.
     */
    public static function getRouters(): array
    {
        return self::proxy()->getRouters();
    }

    /**
     * 设置基础路径.
     */
    public static function setBasePaths(array $basePaths): void
    {
        self::proxy()->setBasePaths($basePaths);
    }

    /**
     * 取得基础路径.
     */
    public static function getBasePaths(): array
    {
        return self::proxy()->getBasePaths();
    }

    /**
     * 设置分组路径.
     */
    public static function setGroupPaths(array $groupPaths): void
    {
        self::proxy()->setGroupPaths($groupPaths);
    }

    /**
     * 取得分组路径.
     */
    public static function getGroupPaths(): array
    {
        return self::proxy()->getGroupPaths();
    }

    /**
     * 设置路由分组.
     */
    public static function setGroups(array $groups): void
    {
        self::proxy()->setGroups($groups);
    }

    /**
     * 取得路由分组.
     */
    public static function getGroups(): array
    {
        return self::proxy()->getGroups();
    }

    /**
     * 设置中间件分组.
     */
    public static function setMiddlewareGroups(array $middlewareGroups): void
    {
        self::proxy()->setMiddlewareGroups($middlewareGroups);
    }

    /**
     * 取得中间件分组.
     */
    public static function getMiddlewareGroups(): array
    {
        return self::proxy()->getMiddlewareGroups();
    }

    /**
     * 设置中间件别名.
     */
    public static function setMiddlewareAlias(array $middlewareAlias): void
    {
        self::proxy()->setMiddlewareAlias($middlewareAlias);
    }

    /**
     * 取得中间件别名.
     */
    public static function getMiddlewareAlias(): array
    {
        return self::proxy()->getMiddlewareAlias();
    }

    /**
     * 合并中间件.
     */
    public static function mergeMiddlewares(array $middlewares, array $newMiddlewares): array
    {
        return self::proxy()->mergeMiddlewares($middlewares, $newMiddlewares);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): BaseRouter
    {
        return Container::singletons()->make('router');
    }
}
