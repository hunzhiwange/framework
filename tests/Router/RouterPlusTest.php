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
use Leevel\Di\IContainer;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Http\Request;
use Leevel\Router\Router;
use Leevel\Router\RouterProvider;
use Leevel\Support\Facade;
use Tests\TestCase;

/**
 * routerPlus test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.18
 *
 * @version 1.0
 */
class RouterPlusTest extends TestCase
{
    public function testBaseUse()
    {
        $pathInfo = '/:tests/Plus/base-use';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';

        $container = new ContainerPlus();

        $request = $this->createRequest($pathInfo, $params, $method);
        $router = $this->createRouter($container);

        $router->setControllerDir($controllerDir);

        $result = $router->dispatch($request);

        $this->assertInstanceof(IResponse::class, $result);

        $this->assertSame('hello plus base use', $result->getContent());
    }

    public function testBaseRouterData()
    {
        $container = new ContainerPlus();

        $container->singleton('router', $router = $this->createRouter($container));

        $provider = new RouterProviderPlus($container);

        $this->assertNull($provider->register());
        $this->assertNull($provider->bootstrap());

        $data = file_get_contents(__DIR__.'/Apps/AppForPlus/data.json');

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

    public function testBaseRouterData111()
    {
        $pathInfo = '/api/v1/petLeevel/hello';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = new ContainerPlus();

        $request = $this->createRequest($pathInfo, $params, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderPlus($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);

        $this->assertInstanceof(IResponse::class, $result);

        $this->assertSame('hello plus for petLeevel, params petId is hello', $result->getContent());

        // 静态属性会保持住，可能受到其它单元测试的影响
        Facade::remove('project');
        Facade::remove('url');
        Facade::remove('router');
        Facade::setContainer(null);
    }

    protected function createRouter(Container $container): Router
    {
        $router = new Router($container);

        $container->singleton('project', new ContainerPlus());
        $container->singleton('url', new UrlPlus());
        $container->singleton('router', $router);

        // 静态属性会保持住，可能受到其它单元测试的影响
        Facade::remove('project');
        Facade::remove('url');
        Facade::remove('router');

        Facade::setContainer($container);

        return $router;
    }

    protected function createRequest(string $pathInfo, array $params, string $method): IRequest
    {
        $request = new Request([], [], $params);

        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        return $request;
    }
}

class ContainerPlus extends Container
{
    public function routerCachedPath(): string
    {
        return __DIR__.'/router_cached.php';
    }

    public function appPath()
    {
        return __DIR__.'/Apps/AppForPlus';
    }
}

class RouterProviderPlus extends RouterProvider
{
    protected $controllerDir = 'Router\\Controllers';

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

class UrlPlus
{
    public function getDomain()
    {
        return 'queryphp.com';
    }
}
