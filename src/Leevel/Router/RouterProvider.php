<?php

declare(strict_types=1);

namespace Leevel\Router;

use Leevel\Di\Provider;
use Leevel\Kernel\IApp;

/**
 * 路由服务提供者.
 */
abstract class RouterProvider extends Provider
{
    /**
     * 控制器相对目录.
     */
    protected ?string $controllerDir = null;

    /**
     * 中间件分组.
     *
     * - 分组可以很方便地批量调用组件.
     */
    protected array $middlewareGroups = [];

    /**
     * 中间件别名.
     *
     * - HTTP 中间件提供一个方便的机制来过滤进入应用程序的 HTTP 请求
     * - 例外在应用执行结束后响应环节也会调用 HTTP 中间件.
     */
    protected array $middlewareAlias = [];

    /**
     * 基础路径.
     */
    protected array $basePaths = [];

    /**
     * 分组.
     */
    protected array $groups = [];

    /**
     * 路由.
     */
    protected IRouter $router; /** @phpstan-ignore-line */

    /**
     * 应用是否带有默认应用命名空间.
     */
    protected bool $withDefaultAppNamespace = false;

    /**
     * bootstrap.
     */
    public function bootstrap(): void
    {
        $this->router = $this->container['router'];

        $this->router->setWithDefaultAppNamespace($this->withDefaultAppNamespace);
        $this->setControllerDir();
        $this->setMiddleware();

        if ($this->isRouterCached()) {
            $this->importCachedRouters();
        } else {
            $this->loadRouters();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->container->singleton(self::class, $this);
    }

    /**
     * {@inheritDoc}
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
        return $this->makeScanRouter()->handle();
    }

    /**
     * 生成注解路由扫描.
     */
    protected function makeScanRouter(): ScanRouter
    {
        return new ScanRouter(
            $this->makeMiddlewareParser(),
            [$this->getAppPath()],
            $this->basePaths,
            $this->groups,
            $this->controllerDir,
        );
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
        /** @var IApp $app */
        $app = $this->container->make('app');

        return $app->routerCachedPath();
    }

    /**
     * 设置应用控制器目录.
     */
    protected function setControllerDir(): void
    {
        if (null !== $this->controllerDir) {
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

    /**
     * 获取应用目录.
     */
    protected function getAppPath(): string
    {
        /** @var IApp $app */
        $app = $this->container->make('app');

        return $app->appPath();
    }
}
