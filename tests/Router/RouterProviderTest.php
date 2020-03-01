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

namespace Tests\Router;

use Leevel\Di\Container;
use Leevel\Kernel\App;
use Leevel\Router\Router;
use Leevel\Router\RouterProvider;
use Leevel\Router\Url;
use Tests\TestCase;

class RouterProviderTest extends TestCase
{
    protected function tearDown(): void
    {
        $file = __DIR__.'/router_cached.php';
        if (is_file($file)) {
            unlink($file);
        }
    }

    public function testBaseUse(): void
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
                    'groups'      => $router->getGroups(),
                    'routers'     => $router->getRouters(),
                ]
            )
        );

        Container::singletons()->clear();
    }

    public function testRouterIsCache(): void
    {
        $container = Container::singletons();
        $app = new App($container, '');
        $app->setAppPath(__DIR__.'/Apps/AppScanRouter');
        $app->setPath(__DIR__.'/Apps/AppScanRouter');
        $app->setRouterCachedPath(__DIR__.'/router_cached.php');

        $container->instance('app', $app);
        $container->singleton('router', $router = $this->createRouter($container));

        $routerData = file_get_contents(__DIR__.'/Apps/AppScanRouter/data.json');
        $routerData = '<?php return '.var_export(json_decode($routerData, true), true).';';
        file_put_contents(
            __DIR__.'/router_cached.php',
            $routerData,
        );

        $provider = new RouterProvider1($container);

        $this->assertNull($provider->register());
        $this->assertNull($provider->bootstrap());

        $data = file_get_contents(__DIR__.'/Apps/AppScanRouter/data.json');
        $this->varJson(
                [
                    'base_paths'  => $router->getBasePaths(),
                    'groups'      => $router->getGroups(),
                    'routers'     => $router->getRouters(),
                ]
            );
        $this->assertSame(
            $data,
            $this->varJson(
                [
                    'base_paths'  => $router->getBasePaths(),
                    'groups'      => $router->getGroups(),
                    'routers'     => $router->getRouters(),
                ]
            )
        );

        Container::singletons()->clear();
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
    protected string $controllerDir = 'Tests\\Router\\Apps';

    protected array $middlewareGroups = [
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

    protected array $middlewareAlias = [
        'demo1' => 'Tests\\Router\\Middlewares\\Demo1',
        'demo2' => 'Tests\\Router\\Middlewares\\Demo2',
        'demo3' => 'Tests\\Router\\Middlewares\\Demo3',
    ];

    protected array $basePaths = [];

    protected array $groups = [
        'pet'     => [],
        'store'   => [],
        'user'    => [],
        '/api/v1' => [
            'middlewares' => 'group1',
        ],
        '/api/v2' => [
            'middlewares' => 'group2',
        ],
        '/api/v3' => [
            'middlewares' => 'demo1,demo3:30,world',
        ],
        '/api/v3' => [
            'middlewares' => ['demo1', 'group3'],
        ],
        '/api/v4' => [
            'middlewares' => 'notFound',
        ],
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
