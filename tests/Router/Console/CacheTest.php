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
