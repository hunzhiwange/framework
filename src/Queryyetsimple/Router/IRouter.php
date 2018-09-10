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

namespace Leevel\Router;

use Leevel\Http\IRequest;
use Leevel\Http\IResponse;

/**
 * 路由解析接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.03.08
 *
 * @version 1.0
 */
interface IRouter
{
    /**
     * 应用参数名.
     *
     * @var string
     */
    const APP = '_app';

    /**
     * 控制器参数名.
     *
     * @var string
     */
    const CONTROLLER = '_c';

    /**
     * 方法参数名.
     *
     * @var string
     */
    const ACTION = '_a';

    /**
     * 解析参数名.
     *
     * @var string
     */
    const PARAMS = '_params';

    /**
     * 解析变量名.
     *
     * @var string
     */
    const VARS = '_vars';

    /**
     * 解析中间件名.
     *
     * @var string
     */
    const MIDDLEWARES = '_middlewares';

    /**
     * 控制器前缀
     *
     * @var string
     */
    const PREFIX = '_prefix';

    /**
     * 匹配基础路径.
     *
     * @var string
     */
    const BASEPATH = '_basepath';

    /**
     * restful show.
     *
     * @var string
     */
    const RESTFUL_SHOW = 'show';

    /**
     * restful store.
     *
     * @var string
     */
    const RESTFUL_STORE = 'store';

    /**
     * restful update.
     *
     * @var string
     */
    const RESTFUL_UPDATE = 'update';

    /**
     * restful destroy.
     *
     * @var string
     */
    const RESTFUL_DESTROY = 'destroy';

    /**
     * 默认应用.
     *
     * @var string
     */
    const DEFAULT_APP = 'app';

    /**
     * 默认首页控制器.
     *
     * @var string
     */
    const DEFAULT_HOME_CONTROLLER = 'home';

    /**
     * 默认首页方法.
     *
     * @var string
     */
    const DEFAULT_HOME_ACTION = 'handle';

    /**
     * 默认替换参数[字符串].
     *
     * @var string
     */
    const DEFAULT_REGEX = '\S+';

    /**
     * 分发请求到路由.
     *
     * @param \Leevel\Http\IRequest $request
     *
     * @return \Leevel\Http\IResponse
     */
    public function dispatch(IRequest $request): IResponse;

    /**
     * 初始化请求
     */
    public function initRequest();

    /**
     * 设置匹配路由
     * 绕过路由解析，可以用于高性能 RPC 快速匹配资源.
     *
     * @param array $matchedData
     */
    public function setMatchedData(array $matchedData): void;

    /**
     * 穿越中间件.
     *
     * @param \Leevel\Http\IRequest $passed
     * @param array                 $passedExtend
     */
    public function throughMiddleware(IRequest $passed, array $passedExtend = []);

    /**
     * 设置控制器相对目录.
     *
     * @param string $controllerDir
     */
    public function setControllerDir(string $controllerDir);

    /**
     * 返回控制器相对目录.
     *
     * @param string $controllerDir
     */
    public function getControllerDir();

    /**
     * 设置路由.
     *
     * @param array $routers
     */
    public function setRouters(array $routers);

    /**
     * 取得当前路由.
     *
     * @return array
     */
    public function getRouters();

    /**
     * 设置基础路径.
     *
     * @param array $basepaths
     */
    public function setBasepaths(array $basepaths);

    /**
     * 添加基础路径.
     *
     * @param array $basepaths
     */
    public function addBasepaths(array $basepaths);

    /**
     * 取得基础路径.
     *
     * @return array
     */
    public function getBasepaths();

    /**
     * 设置路由分组.
     *
     * @param array $groups
     */
    public function setGroups(array $groups);

    /**
     * 添加路由分组.
     *
     * @param array $groups
     */
    public function addGroups(array $groups);

    /**
     * 取得路由分组.
     *
     * @return array
     */
    public function getGroups();

    /**
     * 设置中间件分组.
     *
     * @param array $middlewareGroups
     */
    public function setMiddlewareGroups(array $middlewareGroups);

    /**
     * 取得中间件分组.
     *
     * @return array
     */
    public function getMiddlewareGroups();

    /**
     * 设置全局中间件.
     *
     * @param array $middlewares
     */
    public function setGlobalMiddlewares(array $middlewares);

    /**
     * 取得全局中间件.
     *
     * @return array
     */
    public function getGlobalMiddlewares();

    /**
     * 设置中间件别名.
     *
     * @param array $middlewareAlias
     */
    public function setMiddlewareAlias(array $middlewareAlias);

    /**
     * 取得中间件别名.
     *
     * @return array
     */
    public function getMiddlewareAlias();

    /**
     * 匹配路径.
     *
     * @param string $path
     * @param bool   $ignoreMiddleware
     *
     * @return array
     */
    public function matchePath(string $path, bool $ignoreMiddleware = false): array;
}
