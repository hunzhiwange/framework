<?php

declare(strict_types=1);

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
     */
    public const APP = ':app';

    /**
     * 控制器前缀
     */
    public const PREFIX = ':prefix';

    /**
     * 控制器参数名.
     */
    public const CONTROLLER = ':controller';

    /**
     * 方法参数名.
     */
    public const ACTION = ':action';

    /**
     * 绑定资源.
     */
    public const BIND = ':bind';

    /**
     * 解析参数名.
     */
    public const ATTRIBUTES = ':attributes';

    /**
     * 解析变量名.
     */
    public const VARS = ':vars';

    /**
     * 解析中间件名.
     */
    public const MIDDLEWARES = ':middlewares';

    /**
     * restful show.
     */
    public const RESTFUL_SHOW = 'show';

    /**
     * restful store.
     */
    public const RESTFUL_STORE = 'store';

    /**
     * restful update.
     */
    public const RESTFUL_UPDATE = 'update';

    /**
     * restful destroy.
     */
    public const RESTFUL_DESTROY = 'destroy';

    /**
     * restful index.
     */
    public const RESTFUL_INDEX = 'index';

    /**
     * restful regex.
     *
     * @todo 支持自定义 restful regex
     */
    public const RESTFUL_REGEX = '\d+';

    /**
     * restful id.
     */
    public const RESTFUL_ID = 'id';

    /**
     * 默认应用.
     */
    public const DEFAULT_APP = 'app';

    /**
     * 默认首页控制器.
     */
    public const DEFAULT_CONTROLLER = 'home';

    /**
     * 默认替换参数[字符串].
     */
    public const DEFAULT_REGEX = '\S+';

    /**
     * 路由匹配项.
     */
    public const MATCHED = [
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
     * 穿越终止中间件.
     */
    public function throughTerminateMiddleware(Request $request, Response $response): void;

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
    public function setBasePaths(array $basepaths): void;

    /**
     * 取得基础路径.
     */
    public function getBasePaths(): array;

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
