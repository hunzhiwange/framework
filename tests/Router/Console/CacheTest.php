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

namespace Tests\Router\Console;

use Leevel\Di\IContainer;
use Leevel\Kernel\IProject;
use Leevel\Router\Console\Cache;
use Leevel\Router\IRouter;
use Tests\Console\BaseCommand;
use Tests\TestCase;

/**
 * cache test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.19
 *
 * @version 1.0
 */
class CacheTest extends TestCase
{
    use BaseCommand;

    public function testBaseUse()
    {
        $cacheFile = __DIR__.'/router_cache.php';

        $routerData = [
            'basepaths' => [
                'foo',
                'bar',
            ],
            'groups'      => [],
            'routers'     => [],
            'middlewares' => [],
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

        $this->assertContains(
            sprintf('Router file %s cache successed.', $cacheFile),
            $result
        );

        $this->assertSame($routerData, (array) (include $cacheFile));

        unlink($cacheFile);
    }

    public function testExists()
    {
        $cacheFile = __DIR__.'/router_cache2.php';

        $routerData = [
            'basepaths' => [
                'foo',
                'bar',
            ],
            'groups'      => [],
            'routers'     => [],
            'middlewares' => [],
        ];

        file_put_contents($cacheFile, 'hello');

        $this->assertTrue(is_file($cacheFile));
        $this->assertSame('hello', file_get_contents($cacheFile));

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'router:cache',
            ],
            function ($container) use ($cacheFile, $routerData) {
                $this->initContainerService($container, $cacheFile, $routerData);
            }
        );

        $this->assertContains(
            sprintf('Router file %s cache successed.', $cacheFile),
            $result
        );

        // 如果换成文件存在，则包含警告信息
        $this->assertContains(
            sprintf('Router cache file %s is already exits.', $cacheFile),
            $result
        );

        $this->assertSame($routerData, (array) (include $cacheFile));

        unlink($cacheFile);
    }

    public function testDirNotExists()
    {
        $cacheFile = __DIR__.'/dirNotExists/router_cache.php';

        $routerData = [
            'basepaths' => [
                'foo',
                'bar',
            ],
            'groups'      => [],
            'routers'     => [],
            'middlewares' => [],
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

        $this->assertContains(
            sprintf('Router file %s cache successed.', $cacheFile),
            $result
        );

        $this->assertSame($routerData, (array) (include $cacheFile));

        unlink($cacheFile);
        rmdir(dirname($cacheFile));
    }

    public function testDirWriteable()
    {
        $dirname = __DIR__.'/dirWriteable';
        $cacheFile = $dirname.'/router_cache.php';

        $routerData = [
            'basepaths' => [
                'foo',
                'bar',
            ],
            'groups'      => [],
            'routers'     => [],
            'middlewares' => [],
        ];

        // 设置目录只读
        // 7 = 4+2+1 分别代表可读可写可执行
        mkdir($dirname, 0444);

        $this->assertDirectoryExists($dirname);

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'router:cache',
            ],
            function ($container) use ($cacheFile, $routerData) {
                $this->initContainerService($container, $cacheFile, $routerData);
            }
        );

        $this->assertContains('not', $result);

        rmdir($dirname);
    }

    public function testParentDirWriteable()
    {
        $dirname = __DIR__.'/parentDirWriteable/sub';
        $cacheFile = $dirname.'/router_cache.php';

        $routerData = [
            'basepaths' => [
                'foo',
                'bar',
            ],
            'groups'      => [],
            'routers'     => [],
            'middlewares' => [],
        ];

        // 设置目录只读
        // 7 = 4+2+1 分别代表可读可写可执行
        mkdir(dirname($dirname), 0444);

        $this->assertDirectoryExists(dirname($dirname));

        $this->assertDirectoryNotExists($dirname);

        $result = $this->runCommand(
            new Cache(),
            [
                'command' => 'router:cache',
            ],
            function ($container) use ($cacheFile, $routerData) {
                $this->initContainerService($container, $cacheFile, $routerData);
            }
        );

        $this->assertContains('Unable to create the', $result);

        rmdir(dirname($dirname));
    }

    protected function initContainerService(IContainer $container, string $cacheFile, array $routerData)
    {
        // 注册 project
        $project = $this->createMock(IProject::class);

        $this->assertInstanceof(IProject::class, $project);

        $project->method('routerCachedPath')->willReturn($cacheFile);
        $this->assertEquals($cacheFile, $project->routerCachedPath());

        $container->singleton(IProject::class, $project);

        // 注册 router
        $router = $this->createMock(IRouter::class);

        $this->assertInstanceof(IRouter::class, $router);

        $router->method('getBasepaths')->willReturn($routerData['basepaths']);
        $this->assertEquals($routerData['basepaths'], $router->getBasepaths());

        $router->method('getGroups')->willReturn($routerData['groups']);
        $this->assertEquals($routerData['groups'], $router->getGroups());

        $router->method('getRouters')->willReturn($routerData['routers']);
        $this->assertEquals($routerData['routers'], $router->getRouters());

        $router->method('getGlobalMiddlewares')->willReturn($routerData['middlewares']);
        $this->assertEquals($routerData['middlewares'], $router->getGlobalMiddlewares());

        $container->singleton(IRouter::class, $router);
    }
}
