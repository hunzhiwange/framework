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

namespace Leevel\Router;

use Leevel\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * 路由接口.
 */
interface IRouter
{
    /**
     * 应用参数名.
     *
     * @var string
     */
    const APP = ':app';

    /**
     * 控制器前缀
     *
     * @var string
     */
    const PREFIX = ':prefix';

    /**
     * 控制器参数名.
     *
     * @var string
     */
    const CONTROLLER = ':controller';

    /**
     * 方法参数名.
     *
     * @var string
     */
    const ACTION = ':action';

    /**
     * 绑定资源.
     *
     * @var string
     */
    const BIND = ':bind';

    /**
     * 解析参数名.
     *
     * @var string
     */
    const ATTRIBUTES = ':attributes';

    /**
     * 解析变量名.
     *
     * @var string
     */
    const VARS = ':vars';

    /**
     * 解析中间件名.
     *
     * @var string
     */
    const MIDDLEWARES = ':middlewares';

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
     * restful index.
     *
     * @var string
     */
    const RESTFUL_INDEX = 'index';

    /**
     * restful regex.
     *
     * @todo 支持自定义 restful regex
     *
     * @var string
     */
    const RESTFUL_REGEX = '\d+';

    /**
     * restful id.
     *
     * @var string
     */
    const RESTFUL_ID = 'id';

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
    const DEFAULT_CONTROLLER = 'home';

    /**
     * 默认 OPTIONS 占位.
     *
     * @var string
     */
    const DEFAULT_OPTIONS = 'options';

    /**
     * 默认替换参数[字符串].
     *
     * @var string
     */
    const DEFAULT_REGEX = '\S+';

    /**
     * 路由匹配项.
     *
     * @var array
     */
    const MATCHED = [
        self::APP,
        self::PREFIX,
        self::CONTROLLER,
        self::ACTION,
        self::BIND,
        self::ATTRIBUTES,
        self::MIDDLEWARES,
        self::VARS,
    ];

    /**
     * 分发请求到路由.
     */
    public function dispatch(Request $request): Response;

    /**
     * 初始化请求.
     */
    public function initRequest(): void;

    /**
     * 设置路由请求预解析结果.
     *
     * - 可以用于高性能 Rpc 和 Websocket 预匹配数据.
     */
    public function setPreRequestMatched(Request $request, array $matchedData): void;

    /**
     * 穿越中间件.
     */
    public function throughMiddleware(Request $passed, array $passedExtend = []): void;

    /**
     * 设置控制器相对目录.
     */
    public function setControllerDir(string $controllerDir): void;

    /**
     * 返回控制器相对目录.
     */
    public function getControllerDir(): string;

    /**
     * 设置路由.
     */
    public function setRouters(array $routers): void;

    /**
     * 取得当前路由.
     */
    public function getRouters(): array;

    /**
     * 设置基础路径.
     */
    public function setBasepaths(array $basepaths): void;

    /**
     * 取得基础路径.
     */
    public function getBasepaths(): array;

    /**
     * 设置路由分组.
     */
    public function setGroups(array $groups): void;

    /**
     * 取得路由分组.
     */
    public function getGroups(): array;

    /**
     * 设置中间件分组.
     */
    public function setMiddlewareGroups(array $middlewareGroups): void;

    /**
     * 取得中间件分组.
     */
    public function getMiddlewareGroups(): array;

    /**
     * 设置中间件别名.
     */
    public function setMiddlewareAlias(array $middlewareAlias): void;

    /**
     * 取得中间件别名.
     */
    public function getMiddlewareAlias(): array;

    /**
     * 合并中间件.
     */
    public function mergeMiddlewares(array $middlewares, array $newMiddlewares): array;
}
