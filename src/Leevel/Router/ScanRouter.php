<?php

declare(strict_types=1);

namespace Leevel\Router;

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
    public function __construct(
        MiddlewareParser $middlewareParser,
        array $scandir,
        string $domian = '',
        array $basePaths = [],
        array $groups = [],
        ?string $controllerDir = null,
    ) {
        $this->annotationRouter = new AnnotationRouter(
            $middlewareParser,
            $domian,
            $basePaths,
            $groups
        );

        foreach ($scandir as $path) {
            $this->annotationRouter->addScandir($path);
        }

        if (null !== $controllerDir) {
            $this->setControllerDir($controllerDir);
        }
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
}
