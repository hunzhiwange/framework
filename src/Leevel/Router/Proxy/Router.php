<?php

declare(strict_types=1);

namespace Leevel\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Router\Router as BaseRouter;

/**
 * 代理 router.
 *
 * @method static \Symfony\Component\HttpFoundation\Response dispatch(\Leevel\Http\Request $request)                                   分发请求到路由.
 * @method static void                                       initRequest()                                                             初始化请求.
 * @method static void                                       setPreRequestMatched(\Leevel\Http\Request $request, array $matchedData)   设置路由请求预解析结果.
 * @method static void                                       throughMiddleware(\Leevel\Http\Request $passed, array $passedExtend = []) 穿越中间件.
 * @method static void                                       setControllerDir(string $controllerDir)                                   设置控制器相对目录.
 * @method static string                                     getControllerDir()                                                        返回控制器相对目录.
 * @method static void                                       setRouters(array $routers)                                                设置路由.
 * @method static array                                      getRouters()                                                              取得当前路由.
 * @method static void                                       setBasePaths(array $basePaths)                                            设置基础路径.
 * @method static array                                      getBasePaths()                                                            取得基础路径.
 * @method static void                                       setGroups(array $groups)                                                  设置路由分组.
 * @method static array                                      getGroups()                                                               取得路由分组.
 * @method static void                                       setMiddlewareGroups(array $middlewareGroups)                              设置中间件分组.
 * @method static array                                      getMiddlewareGroups()                                                     取得中间件分组.
 * @method static void                                       setMiddlewareAlias(array $middlewareAlias)                                设置中间件别名.
 * @method static array                                      getMiddlewareAlias()                                                      取得中间件别名.
 * @method static array                                      mergeMiddlewares(array $middlewares, array $newMiddlewares)               合并中间件.
 */
class Router
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): BaseRouter
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('router');
    }
}
