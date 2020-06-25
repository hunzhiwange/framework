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

use Leevel\Di\Provider;

/**
 * 路由服务提供者.
 */
abstract class RouterProvider extends Provider
{
    /**
     * 控制器相对目录.
     *
     * @var string
     */
    protected string $controllerDir = '';

    /**
     * 中间件分组.
     *
     * - 分组可以很方便地批量调用组件.
     *
     * @var array
     */
    protected array $middlewareGroups = [];

    /**
     * 中间件别名.
     *
     * - HTTP 中间件提供一个方便的机制来过滤进入应用程序的 HTTP 请求
     * - 例外在应用执行结束后响应环节也会调用 HTTP 中间件.
     *
     * @var array
     */
    protected array $middlewareAlias = [];

    /**
     * 基础路径.
     *
     * @var array
     */
    protected array $basePaths = [];

    /**
     * 分组.
     *
     * @var array
     */
    protected array $groups = [];

    /**
     * 路由.
     *
     * @var \Leevel\Router\IRouter
     */
    protected IRouter $router;

    /**
     * bootstrap.
     */
    public function bootstrap(): void
    {
        $this->router = $this->container['router'];

        $this->setControllerDir();
        $this->setMiddleware();

        if ($this->isRouterCached()) {
            $this->importCachedRouters();
        } else {
            $this->loadRouters();
        }
    }

    /**
     * 注册一个提供者.
     */
    public function register(): void
    {
        $this->container->singleton(self::class, $this);
    }

    /**
     * 可用服务提供者.
     */
    public static function providers(): array
    {
        return [
            self::class,
        ];
    }

    /**
     * 返回路由.
     */
    public function getRouters(): array
    {
        return (new ScanRouter($this->makeMiddlewareParser(), $this->basePaths, $this->groups))->handle();
    }

    /**
     * 导入路由缓存.
     */
    protected function importCachedRouters(): void
    {
        $routers = include $this->getRouterCachePath();
        $this->setRoutersData($routers);
    }

    /**
     * 注册路由.
     */
    protected function loadRouters(): void
    {
        $this->setRoutersData($this->getRouters());
    }

    /**
     * 生成中间件分析器.
     *
     * @return \Leevel\Router\MiddlewareParser
     */
    protected function makeMiddlewareParser(): MiddlewareParser
    {
        return new MiddlewareParser($this->router);
    }

    /**
     * 设置路由数据.
     */
    protected function setRoutersData(array $routers): void
    {
        $this->router->setBasePaths($routers['base_paths']);
        $this->router->setGroups($routers['groups']);
        $this->router->setRouters($routers['routers']);
    }

    /**
     * 路由是否已经缓存.
     */
    protected function isRouterCached(): bool
    {
        return is_file($this->getRouterCachePath());
    }

    /**
     * 获取路由缓存地址.
     */
    protected function getRouterCachePath(): string
    {
        return $this->container->make('app')->routerCachedPath();
    }

    /**
     * 设置应用控制器目录.
     */
    protected function setControllerDir(): void
    {
        if ($this->controllerDir) {
            $this->router->setControllerDir($this->controllerDir);
        }
    }

    /**
     * 设置中间件.
     */
    protected function setMiddleware(): void
    {
        if ($this->middlewareGroups) {
            $this->router->setMiddlewareGroups($this->middlewareGroups);
        }

        if ($this->middlewareAlias) {
            $this->router->setMiddlewareAlias($this->middlewareAlias);
        }
    }
}
