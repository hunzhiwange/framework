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

namespace Tests\Router;

use Leevel\Di\Container;
use Leevel\Router\Router;
use Leevel\Router\RouterProvider;
use Leevel\Support\Facade;
use Tests\TestCase;

/**
 * routerProvider test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.17
 *
 * @version 1.0
 */
class RouterProviderTest extends TestCase
{
    public function testBaseUse()
    {
        $container = new Container1();

        $container->singleton('router', $router = $this->createRouter($container));

        $provider = new RouterProvider1($container);

        $this->assertNull($provider->register());
        $this->assertNull($provider->bootstrap());

        $data = file_get_contents(__DIR__.'/Apps/AppScanRouter/data.json');

        $this->assertSame(
            $data,
            $this->varJsonEncode(
                [
                    'base_paths'  => $router->getBasePaths(),
                    'group_paths' => $router->getGroupPaths(),
                    'groups'      => $router->getGroups(),
                    'routers'     => $router->getRouters(),
                ],
                __FUNCTION__
            )
        );

        // 静态属性会保持住，可能受到其它单元测试的影响
        Facade::remove('project');
        Facade::remove('url');
        Facade::remove('router');
        Facade::setContainer(null);
    }

    public function testRouterIsCache()
    {
        $container = new Container1();

        $container->singleton('router', $router = $this->createRouter($container));

        file_put_contents(
            $routerCached = __DIR__.'/router_cached.php',
            file_get_contents(__DIR__.'/Apps/AppScanRouter/data.php')
        );

        $provider = new RouterProvider1($container);

        $this->assertNull($provider->register());
        $this->assertNull($provider->bootstrap());

        $data = file_get_contents(__DIR__.'/Apps/AppScanRouter/data.json');

        $this->assertSame(
            $data,
            $this->varJsonEncode(
                [
                    'base_paths'  => $router->getBasePaths(),
                    'group_paths' => $router->getGroupPaths(),
                    'groups'      => $router->getGroups(),
                    'routers'     => $router->getRouters(),
                ],
                __FUNCTION__
            )
        );

        // 静态属性会保持住，可能受到其它单元测试的影响
        Facade::remove('project');
        Facade::remove('url');
        Facade::remove('router');
        Facade::setContainer(null);

        unlink($routerCached);
    }

    protected function createRouter(Container $container): Router
    {
        $router = new Router($container);

        $container->singleton('project', new Container1());
        $container->singleton('url', new Url2());
        $container->singleton('router', $router);

        // 静态属性会保持住，可能受到其它单元测试的影响
        Facade::remove('project');
        Facade::remove('url');
        Facade::remove('router');

        Facade::setContainer($container);

        return $router;
    }
}

class Container1 extends Container
{
    public function routerCachedPath(): string
    {
        return __DIR__.'/router_cached.php';
    }

    public function appPath()
    {
        return __DIR__.'/Apps/AppScanRouter';
    }
}

class RouterProvider1 extends RouterProvider
{
    protected $controllerDir = 'Tests\\Router\\Apps';

    protected $middlewareGroups = [
        'group1' => [
            'demo1',
            'demo2',
        ],

        'group2' => [
            'demo1',
            'demo3:10,world',
        ],

        'group3' => [
            'demo1',
            'demo2',
            'demo3:10,world',
        ],
    ];

    protected $middlewareAlias = [
        'demo1' => 'Tests\\Router\\Middlewares\\Demo1',
        'demo2' => 'Tests\\Router\\Middlewares\\Demo2',
        'demo3' => 'Tests\\Router\\Middlewares\\Demo3',
    ];

    public function bootstrap()
    {
        parent::bootstrap();
    }

    public function getRouters(): array
    {
        return parent::getRouters();
    }
}

class Url2
{
    public function getDomain()
    {
        return 'queryphp.com';
    }
}
