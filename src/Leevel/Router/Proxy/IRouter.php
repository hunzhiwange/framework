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

namespace Leevel\Router\Proxy;

use Leevel\Http\IRequest;
use Leevel\Http\IResponse;

/**
 * 代理 router 接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.05.24
 *
 * @version 1.0
 *
 * @see \Leevel\Router\IRouter 请保持接口设计的一致性
 */
interface IRouter
{
    /**
     * 分发请求到路由.
     *
     * @param \Leevel\Http\IRequest $request
     *
     * @return \Leevel\Http\IResponse
     */
    public static function dispatch(IRequest $request): IResponse;

    /**
     * 初始化请求.
     */
    public static function initRequest(): void;

    /**
     * 设置路由请求预解析结果.
     *
     * - 可以用于高性能 Rpc 和 Websocket 预匹配数据.
     *
     * @param \Leevel\Http\IRequest $request
     * @param array                 $matchedData
     */
    public static function setPreRequestMatched(IRequest $request, array $matchedData): void;

    /**
     * 穿越中间件.
     *
     * @param \Leevel\Http\IRequest $passed
     * @param array                 $passedExtend
     */
    public static function throughMiddleware(IRequest $passed, array $passedExtend = []): void;

    /**
     * 设置控制器相对目录.
     *
     * @param string $controllerDir
     */
    public static function setControllerDir(string $controllerDir): void;

    /**
     * 返回控制器相对目录.
     *
     * @return string
     */
    public static function getControllerDir(): string;

    /**
     * 设置路由.
     *
     * @param array $routers
     */
    public static function setRouters(array $routers): void;

    /**
     * 取得当前路由.
     *
     * @return array
     */
    public static function getRouters(): array;

    /**
     * 设置基础路径.
     *
     * @param array $basepaths
     */
    public static function setBasepaths(array $basepaths): void;

    /**
     * 取得基础路径.
     *
     * @return array
     */
    public static function getBasepaths(): array;

    /**
     * 设置分组路径.
     *
     * @param array $groupPaths
     */
    public static function setGroupPaths(array $groupPaths): void;

    /**
     * 取得分组路径.
     *
     * @return array
     */
    public static function getGroupPaths(): array;

    /**
     * 设置路由分组.
     *
     * @param array $groups
     */
    public static function setGroups(array $groups): void;

    /**
     * 取得路由分组.
     *
     * @return array
     */
    public static function getGroups(): array;

    /**
     * 设置中间件分组.
     *
     * @param array $middlewareGroups
     */
    public static function setMiddlewareGroups(array $middlewareGroups): void;

    /**
     * 取得中间件分组.
     *
     * @return array
     */
    public static function getMiddlewareGroups(): array;

    /**
     * 设置中间件别名.
     *
     * @param array $middlewareAlias
     */
    public static function setMiddlewareAlias(array $middlewareAlias): void;

    /**
     * 取得中间件别名.
     *
     * @return array
     */
    public static function getMiddlewareAlias(): array;

    /**
     * 合并中间件.
     *
     * @param array $middlewares
     * @param array $newMiddlewares
     *
     * @return array
     */
    public static function mergeMiddlewares(array $middlewares, array $newMiddlewares): array;
}
