<?php

declare(strict_types=1);

namespace Tests\Router\Console;

use Leevel\Di\IContainer;
use Leevel\Kernel\IApp;
use Leevel\Router\Console\Cache;
use Leevel\Router\RouterProvider;
use Tests\Console\BaseCommand;
use Tests\TestCase;

class CacheTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse(): void
    {
        $cacheFile = __DIR__.'/router_cache.php';

        $routerData = [
            'base_paths'   => [],
            'groups'       => [],
            'routers'      => [],
        ];

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'router:cache',
            ],
            function ($container) use ($cacheFile, $routerData) {
                $this->initContainerService($container, $cacheFile, $routerData);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('Start to cache router.'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Router cache successed at %s.', $cacheFile)),
            $result
        );

        $this->assertSame($routerData, (array) (include $cacheFile));

        unlink($cacheFile);
    }

    public function testDirNotExists(): void
    {
        $cacheFile = __DIR__.'/dirNotExists/router_cache.php';

        $routerData = [
            'base_paths'   => [],
            'groups'       => [],
            'routers'      => [],
        ];

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'router:cache',
            ],
            function ($container) use ($cacheFile, $routerData) {
                $this->initContainerService($container, $cacheFile, $routerData);
            }
        );

        $result = $this->normalizeContent($result);

        $this->assertStringContainsString(
            $this->normalizeContent('Start to cache router.'),
            $result
        );

        $this->assertStringContainsString(
            $this->normalizeContent(sprintf('Router cache successed at %s.', $cacheFile)),
            $result
        );

        $this->assertSame($routerData, (array) (include $cacheFile));

        unlink($cacheFile);
        rmdir(dirname($cacheFile));
    }

    protected function initContainerService(IContainer $container, string $cacheFile, array $routerData): void
    {
        $app = $this->createMock(IApp::class);
        $this->assertInstanceof(IApp::class, $app);

        $app->method('routerCachedPath')->willReturn($cacheFile);
        $this->assertEquals($cacheFile, $app->routerCachedPath());

        $container->singleton(IApp::class, $app);

        // 注册 routerProvider
        $router = $this->createMock(RouterProvider::class);

        $this->assertInstanceof(RouterProvider::class, $router);

        $router->method('getRouters')->willReturn($routerData);
        $this->assertEquals($routerData, $router->getRouters());

        $container->singleton(RouterProvider::class, $router);
    }
}
