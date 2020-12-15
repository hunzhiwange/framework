<?php

declare(strict_types=1);

namespace Leevel\Router;

use Leevel\Kernel\Proxy\App;
use Leevel\Router\Proxy\Router;
use Leevel\Router\Proxy\Url;

/**
 * 注解路由扫描.
 */
class ScanRouter
{
    /**
     * 注解路由分析.
     */
    protected AnnotationRouter $annotationRouter;

    /**
     * 构造函数.
     */
    public function __construct(MiddlewareParser $middlewareParser, array $basePaths = [], array $groups = [])
    {
        $this->annotationRouter = new AnnotationRouter(
            $middlewareParser,
            $this->getDomain(),
            $basePaths,
            $groups
        );

        foreach ([$this->routePath(), $this->appPath()] as $path) {
            $this->annotationRouter->addScandir($path);
        }

        $this->setControllerDir($this->getControllerDir());
    }

    /**
     * 设置控制器相对目录.
     */
    public function setControllerDir(string $controllerDir): void
    {
        $this->annotationRouter->setControllerDir($controllerDir);
    }

    /**
     * 响应.
     */
    public function handle(): array
    {
        return $this->annotationRouter->handle();
    }

    /**
     * 获取控制器相对目录.
     */
    protected function getControllerDir(): string
    {
        return Router::getControllerDir();
    }

    /**
     * 获取顶级域名.
     */
    protected function getDomain(): string
    {
        return Url::getDomain();
    }

    /**
     * 获取应用目录.
     */
    protected function appPath(): string
    {
        return App::appPath();
    }

    /**
     * 获取路由目录.
     */
    protected function routePath(): string
    {
        return App::path('router');
    }
}
