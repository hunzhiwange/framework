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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Router;

use Leevel\Di\Container;
use Leevel\Kernel\App;
use Leevel\Router\Router;
use Leevel\Router\RouterProvider;
use Leevel\Router\Url;
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
    protected function tearDown(): void
    {
        $file = __DIR__.'/router_cached.php';

        if (is_file($file)) {
            unlink($file);
        }
    }

    public function testBaseUse()
    {
        $container = Container::singletons();
        $app = new App($container, '');
        $app->setAppPath(__DIR__.'/Apps/AppScanRouter');
        $app->setPath(__DIR__.'/Apps/AppScanRouter');
        $app->setRouterCachedPath(__DIR__.'/router_cached.php');

        $container->instance('app', $app);
        $container->instance('router', $router = $this->createRouter($container));

        $provider = new RouterProvider1($container);

        $this->assertNull($provider->register());
        $this->assertNull($provider->bootstrap());

        $data = file_get_contents(__DIR__.'/Apps/AppScanRouter/data.json');

        $this->assertSame(
            $data,
            $this->varJson(
                [
                    'base_paths'  => $router->getBasePaths(),
                    'group_paths' => $router->getGroupPaths(),
                    'groups'      => $router->getGroups(),
                    'routers'     => $router->getRouters(),
                ]
            )
        );

        Container::singletons()->clear();
    }

    public function testRouterIsCache()
    {
        $container = Container::singletons();
        $app = new App($container, '');
        $app->setAppPath(__DIR__.'/Apps/AppScanRouter');
        $app->setPath(__DIR__.'/Apps/AppScanRouter');
        $app->setRouterCachedPath(__DIR__.'/router_cached.php');

        $container->instance('app', $app);
        $container->singleton('router', $router = $this->createRouter($container));

        file_put_contents(
            $routerCached = __DIR__.'/router_cached.php',
            file_get_contents(__DIR__.'/Apps/AppScanRouter/data.php')
        );

        $provider = new RouterProvider1($container);

        $this->assertNull($provider->register());
        $this->assertNull($provider->bootstrap());

        $data = file_get_contents(__DIR__.'/Apps/AppScanRouter/data.json');
        $this->varJson(
                [
                    'base_paths'  => $router->getBasePaths(),
                    'group_paths' => $router->getGroupPaths(),
                    'groups'      => $router->getGroups(),
                    'routers'     => $router->getRouters(),
                ]
            );
        $this->assertSame(
            $data,
            $this->varJson(
                [
                    'base_paths'  => $router->getBasePaths(),
                    'group_paths' => $router->getGroupPaths(),
                    'groups'      => $router->getGroups(),
                    'routers'     => $router->getRouters(),
                ]
            )
        );

        Container::singletons()->clear();

        unlink($routerCached);
    }

    protected function createRouter(Container $container): Router
    {
        $router = new Router($container);

        $container->singleton('app', $container);
        $container->singleton('url', new Url2());
        $container->singleton('router', $router);

        return $router;
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

    public function bootstrap(): void
    {
        parent::bootstrap();
    }

    public function getRouters(): array
    {
        return parent::getRouters();
    }
}

class Url2 extends Url
{
    public function __construct()
    {
    }

    public function getDomain(): string
    {
        return 'queryphp.com';
    }
}
