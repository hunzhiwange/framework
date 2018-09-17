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
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Mvc\IView;
use Leevel\Router\IRouter;
use Leevel\Router\Router;
use Tests\TestCase;

/**
 * router test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.19
 *
 * @version 1.0
 */
class RouterTest extends TestCase
{
    public function testBaseUse()
    {
        $pathInfo = '/:tests';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';

        $request = $this->createRequest($pathInfo, $params, $method);
        $router = $this->createRouter();

        $router->setControllerDir($controllerDir);

        $result = $router->dispatch($request);

        $this->assertInstanceof(IResponse::class, $result);

        $this->assertSame('hello my home', $result->getContent());
    }

    public function testActionAsClass()
    {
        $pathInfo = '/:tests/hello/actionClass';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';

        $request = $this->createRequest($pathInfo, $params, $method);
        $router = $this->createRouter();

        $router->setControllerDir($controllerDir);

        $result = $router->dispatch($request);

        $this->assertInstanceof(IResponse::class, $result);

        $this->assertSame('hello action class', $result->getContent());
    }

    public function testActionConvert()
    {
        $pathInfo = '/:tests/hello/action_convert-foo_bar';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';

        $request = $this->createRequest($pathInfo, $params, $method);
        $router = $this->createRouter();

        $router->setControllerDir($controllerDir);

        $result = $router->dispatch($request);

        $this->assertInstanceof(IResponse::class, $result);

        $this->assertSame('hello action convert foo bar', $result->getContent());
    }

    public function testSubControllerDir()
    {
        $pathInfo = '/:tests/sub/world/foo';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';

        $request = $this->createRequest($pathInfo, $params, $method);
        $router = $this->createRouter();

        $router->setControllerDir($controllerDir);

        $result = $router->dispatch($request);

        $this->assertInstanceof(IResponse::class, $result);

        $this->assertSame('hello sub world foo', $result->getContent());
    }

    public function testSubControllerDir2()
    {
        $pathInfo = '/:tests/sub/world/foo/bar';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';

        $request = $this->createRequest($pathInfo, $params, $method);
        $router = $this->createRouter();

        $router->setControllerDir($controllerDir);

        $result = $router->dispatch($request);

        $this->assertInstanceof(IResponse::class, $result);

        $this->assertSame('hello sub world foo bar', $result->getContent());
    }

    public function testControllerExtendsBase()
    {
        $pathInfo = '/:tests/hello/extendsBase';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';

        $request = $this->createRequest($pathInfo, $params, $method);
        $router = new Router($container = new Container());

        $container->singleton(IView::class, function () {
            return $this->createMock(IView::class);
        });

        $router->setControllerDir($controllerDir);

        $result = $router->dispatch($request);

        $this->assertInstanceof(IResponse::class, $result);

        $this->assertSame('hello extends base', $result->getContent());
    }

    /**
     * @dataProvider getRestfulData
     *
     * @param string $method
     * @param mixed  $action
     */
    public function testRestful(string $method, string $action)
    {
        $pathInfo = '/:tests/restful/5';
        $params = [];
        $method = $method;
        $controllerDir = 'Router\\Controllers';

        $request = $this->createRequest($pathInfo, $params, $method);
        $router = $this->createRouter();

        $router->setControllerDir($controllerDir);

        $result = $router->dispatch($request);

        $this->assertInstanceof(IResponse::class, $result);

        $this->assertSame('hello for restful '.$action, $result->getContent());
    }

    public function getRestfulData()
    {
        return [
            ['GET', Router::RESTFUL_SHOW],
            ['POST', Router::RESTFUL_STORE],
            ['PUT', Router::RESTFUL_UPDATE],
            ['DELETE', Router::RESTFUL_DESTROY],
        ];
    }

    /**
     * @dataProvider getNodeNotFoundData
     *
     * @param string $method
     * @param string $action
     */
    public function testNodeNotFound(string $method, string $action)
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            sprintf('The router App\\App\\Controller\\Home::%s() was not found.', $action)
        );

        $pathInfo = '/';
        $params = [];
        $method = $method;
        $controllerDir = 'App\Controller';

        $request = $this->createRequest($pathInfo, $params, $method);
        $router = $this->createRouter();

        $router->setControllerDir($controllerDir);

        $result = $router->dispatch($request);
    }

    public function getNodeNotFoundData()
    {
        return [
            ['GET', Router::RESTFUL_SHOW],
            ['POST', Router::RESTFUL_STORE],
            ['PUT', Router::RESTFUL_UPDATE],
            ['DELETE', Router::RESTFUL_DESTROY],
        ];
    }

    public function testSetMatchedData()
    {
        $pathInfo = '';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';

        $request = $this->createRequest($pathInfo, $params, $method);
        $router = $this->createRouter();

        $router->setMatchedData([
            IRouter::APP         => 'Tests',
            IRouter::CONTROLLER  => 'Bar',
            IRouter::ACTION      => 'foo',
            IRouter::PREFIX      => 'MatchedData\\Prefix',
            IRouter::PARAMS      => null,
            IRouter::MIDDLEWARES => null,
            IRouter::VARS        => null,
        ]);

        $router->setControllerDir($controllerDir);

        $result = $router->dispatch($request);

        $this->assertInstanceof(IResponse::class, $result);

        $this->assertSame('hello matchedData', $result->getContent());
    }

    public function testThroughMiddleware()
    {
        $pathInfo = '/:tests/hello/throughMiddleware';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';

        $request = $this->createRequest($pathInfo, $params, $method);
        $router = $this->createRouter();

        $router->setMiddlewareGroups([
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
        ]);

        $router->setMiddlewareAlias([
            'demo1' => 'Tests\\Router\\Middlewares\\Demo1',
            'demo2' => 'Tests\\Router\\Middlewares\\Demo2',
            'demo3' => 'Tests\\Router\\Middlewares\\Demo3',
        ]);

        $router->setBasePaths([
            '*' => [
                'middlewares' => [
                    'handle' => [
                        'Tests\\Router\\Middlewares\\Demo2@handle',
                    ],
                    'terminate' => [
                        'Tests\\Router\\Middlewares\\Demo1@terminate',
                        'Tests\\Router\\Middlewares\\Demo2@terminate',
                    ],
                ],
            ],
            '/^\/:tests\/hello\/throughMiddleware$/' => [
                'middlewares' => [
                    'handle' => [
                        'Tests\\Router\\Middlewares\\Demo3:10,hello@handle',
                    ],
                    'terminate' => [
                    ],
                ],
            ],
        ]);

        $router->setControllerDir($controllerDir);

        unset($GLOBALS['demo_middlewares']);

        $result = $router->dispatch($request);

        $router->throughMiddleware($request, [
            $result,
        ]);

        $this->assertInstanceof(IResponse::class, $result);

        $this->assertSame('hello throughMiddleware', $result->getContent());

        $data = <<<'eot'
[
    "Demo2::handle",
    "Demo3::handle(arg1:10,arg2:hello@handle)",
    "Demo1::terminate",
    "Demo2::terminate"
]
eot;

        $this->assertSame(
            $data,
            $this->varJsonEncode(
                $GLOBALS['demo_middlewares'],
                __FUNCTION__
            )
        );

        unset($GLOBALS['demo_middlewares']);
    }

    protected function createRouter(): Router
    {
        return new Router(new Container());
    }

    protected function createRequest(string $pathInfo, array $params, string $method): IRequest
    {
        // 创建 request
        $request = $this->createMock(IRequest::class);

        $this->assertInstanceof(IRequest::class, $request);

        $request->method('getPathInfo')->willReturn($pathInfo);
        $this->assertEquals($pathInfo, $request->getPathInfo());

        $request->method('getMethod')->willReturn($method);
        $this->assertEquals($method, $request->getMethod());

        $bag = $this->createMock(IBag::class);

        $bag->method('replace')->willReturn($params);
        $this->assertEquals($params, $bag->replace([]));

        $request->params = $bag;

        return $request;
    }
}

interface IBag
{
    public function replace(array $data);
}
