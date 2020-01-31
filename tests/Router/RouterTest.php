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
use Leevel\Http\Request;
use Leevel\Router\IRouter;
use Leevel\Router\Router;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RouterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $pathInfo = '/:tests';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello my home', $result->getContent());
    }

    public function testActionAsClass(): void
    {
        $pathInfo = '/:tests/hello/actionClass';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello action class', $result->getContent());
    }

    public function testActionConvert(): void
    {
        $pathInfo = '/:tests/hello/action_convert-foo_bar';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello action convert foo bar', $result->getContent());
    }

    public function testControllerConvert(): void
    {
        $pathInfo = '/:tests/controller_convert-foo_bar/bar';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello controller convert', $result->getContent());
    }

    public function testSubControllerDir(): void
    {
        $pathInfo = '/:tests/sub/world/foo';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello sub world foo', $result->getContent());
    }

    public function testSubControllerDir2(): void
    {
        $pathInfo = '/:tests/sub/world/foo/bar';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello sub world foo bar', $result->getContent());
    }

    public function testConvertAll(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router Tests\\Router\\Controllers\\HeLloWor\\Bar\\Foo\\XYYAc\\ControllerXxYy::actionXxxYzs() was not found.'
        );

        $pathInfo = '/:tests/he_llo-wor/Bar/foo/xYY-ac/controller_xx-yy/action-xxx_Yzs';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $router->dispatch($request);
    }

    public function testShouldJson(): void
    {
        $pathInfo = '/:tests/should_json';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('{"foo":"bar"}', $result->getContent());
    }

    /**
     * @dataProvider getRestfulData
     *
     * @param mixed $action
     */
    public function testRestful(string $method, string $action): void
    {
        $pathInfo = '/:tests/restful/5';
        $attributes = [];
        $method = $method;
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
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
     */
    public function testNodeNotFound(string $method, string $action): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            sprintf('The router App\\App\\Controller\\Home::%s() was not found.', $action)
        );

        $pathInfo = '/';
        $attributes = [];
        $method = $method;
        $controllerDir = 'App\\Controller';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $router->dispatch($request);
    }

    public function getNodeNotFoundData()
    {
        return [
            ['GET', Router::RESTFUL_INDEX],
            ['POST', Router::RESTFUL_STORE],
            ['PUT', Router::RESTFUL_UPDATE],
            ['DELETE', Router::RESTFUL_DESTROY],
        ];
    }

    /**
     * @dataProvider getNodeNotFoundDataWithParams
     */
    public function testNodeNotFoundWithParams(string $method, string $action): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            sprintf('The router App\\App\\Controller\\Home::%s() was not found.', $action)
        );

        $pathInfo = '/home/5';
        $attributes = [];
        $method = $method;
        $controllerDir = 'App\\Controller';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $router->dispatch($request);
    }

    public function getNodeNotFoundDataWithParams()
    {
        return [
            ['GET', Router::RESTFUL_SHOW],
            ['POST', Router::RESTFUL_STORE],
            ['PUT', Router::RESTFUL_UPDATE],
            ['DELETE', Router::RESTFUL_DESTROY],
        ];
    }

    public function testSetPreRequestMatched(): void
    {
        $pathInfo = '';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setPreRequestMatched($request, [
            IRouter::APP             => 'Tests',
            IRouter::CONTROLLER      => 'Bar',
            IRouter::ACTION          => 'foo',
            IRouter::PREFIX          => 'PreRequestMatched\\Prefix',
            IRouter::ATTRIBUTES      => null,
            IRouter::MIDDLEWARES     => null,
            IRouter::VARS            => null,
        ]);
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello preRequestMatched', $result->getContent());
    }

    public function testThroughMiddleware(): void
    {
        $pathInfo = '/ap1/v1/:tests/hello/throughMiddleware';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
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
            'demo1'        => 'Tests\\Router\\Middlewares\\Demo1',
            'demo2'        => 'Tests\\Router\\Middlewares\\Demo2',
            'demo3'        => 'Tests\\Router\\Middlewares\\Demo3',
            'demoForGroup' => 'Tests\\Router\\Middlewares\\DemoForGroup',
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
            '/^\/ap1\/v1\/:tests\/hello\/throughMiddleware\\/$/' => [
                'middlewares' => [
                    'handle' => [
                        'Tests\\Router\\Middlewares\\Demo3:10,hello@handle',
                    ],
                    'terminate' => [
                    ],
                ],
            ],
        ]);

        $router->setGroupPaths([
            '/ap1/v1' => [
                'middlewares' => [
                    'handle' => [
                        'Tests\\Router\\Middlewares\\DemoForGroup@handle',
                    ],
                    'terminate' => [
                        'Tests\\Router\\Middlewares\\DemoForGroup@terminate',
                    ],
                ],
            ],
        ]);

        $router->setControllerDir($controllerDir);

        if (isset($GLOBALS['demo_middlewares'])) {
            unset($GLOBALS['demo_middlewares']);
        }

        $result = $router->dispatch($request);
        $router->throughMiddleware($request, [
            $result,
        ]);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello throughMiddleware', $result->getContent());

        $data = <<<'eot'
            [
                "Demo2::handle",
                "Demo3::handle(arg1:10,arg2:hello@handle)",
                "DemoForGroup::handle",
                "Demo1::terminate",
                "Demo2::terminate",
                "DemoForGroup::terminate"
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares']
            )
        );

        unset($GLOBALS['demo_middlewares']);
    }

    public function testParseDefaultBindControllerFoundButMethodNotFound(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router Tests\\Router\\Controllers\\Hello\\ControllerFoundMethodNot::foo() was not found.'
        );

        $pathInfo = '/:tests/hello/ControllerFoundMethodNot/foo';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $router->dispatch($request);
    }

    public function testParseDefaultBindMethodClassFoundButEnterMethodNotFound(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router Tests\\Router\\Controllers\\Hello::MethodClassFoundButEnterMethodNot() was not found.'
        );

        $pathInfo = '/:tests/hello/MethodClassFoundButEnterMethodNot';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $router->dispatch($request);
    }

    public function testOptionsForCorsWillBackCorsResponse(): void
    {
        $pathInfo = '/:tests/options/index';
        $attributes = [];
        $method = 'OPTIONS';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('cors', $result->getContent());
    }

    public function testOptionsForCorsButNotFound(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router App\\Router\\Controllers\\Options::index() was not found.'
        );

        $pathInfo = '/options/index';
        $attributes = [];
        $method = 'OPTIONS';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $router->dispatch($request);
    }

    public function testColonInController(): void
    {
        $pathInfo = '/:tests/colon:hello';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello colon with controller', $result->getContent());
    }

    public function testColonInControllerWithActionIsSingleClass(): void
    {
        $pathInfo = '/:tests/colonActionSingle:hello';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello colon with controller and action is single', $result->getContent());
    }

    public function testColonInControllerWithMoreThanOne(): void
    {
        $pathInfo = '/:tests/colon:hello:world:foo';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello colon with more than one in controller', $result->getContent());
    }

    public function testColonInControllerWithMoreThanOneWithActionIsSingleClass(): void
    {
        $pathInfo = '/:tests/colonActionSingle:hello:world:foo';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello colon with more than one in controller and action is single', $result->getContent());
    }

    public function testColonInControllerMustBeforeAlpha(): void
    {
        $pathInfo = '/:tests/:colon/foundAction';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello colon with controller with foundAction', $result->getContent());
    }

    public function testColonInControllerMustBeforeAlphaWithActionIsSingleClass(): void
    {
        $pathInfo = '/:tests/:colonActionSingle/foundAction';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello colon with controller with foundAction and action is single', $result->getContent());
    }

    public function testColonInControllerMustBeforeAlphaButNotFound(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router Tests\\Router\\Controllers\\:colon::notFound() was not found.'
        );

        $pathInfo = '/:tests/:colon/notFound';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $router->dispatch($request);
    }

    public function testColonInActionAndActionIsNotSingleClass(): void
    {
        $pathInfo = '/:tests/colon:action/foo:bar';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello colon with action and action is not single class', $result->getContent());
    }

    public function testColonInActionAndActionIsSingleClass(): void
    {
        $pathInfo = '/:tests/colonActionSingle:action/foo:bar';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello colon with action and action is not single class and action is single', $result->getContent());
    }

    public function testColonInActionAndActionIsNotSingleClassWithMoreThanOne(): void
    {
        $pathInfo = '/:tests/colon:action/more:foo:bar';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello colon with action and action is not single class with more than one', $result->getContent());
    }

    public function testColonInActionAndActionIsSingleClassWithMoreThanOne(): void
    {
        $pathInfo = '/:tests/colonActionSingle:action/more:foo:bar';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);
        $this->assertInstanceof(Response::class, $result);

        $this->assertSame('hello colon with action and action is not single class with more than one and action is single', $result->getContent());
    }

    public function testColonInActionIsNotSingleClassMustBeforeAlpha(): void
    {
        $pathInfo = '/:tests/colon:action/:beforeButFirst';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);
        $this->assertInstanceof(Response::class, $result);

        $this->assertSame('hello colon with action and action is not single class before but first', $result->getContent());
    }

    public function testColonInActionIsSingleClassMustBeforeAlpha(): void
    {
        $pathInfo = '/:tests/colonActionSingle:action/:beforeButFirst';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello colon with action and action is not single class before but first and action is single', $result->getContent());
    }

    public function testColonInActionIsNotSingleClassMustBeforeAlphaButActionNotFound(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router Tests\\Router\\Controllers\\Colon:action:::beforeButFirstAndNotFound() was not found.'
        );

        $pathInfo = '/:tests/colon:action/:beforeButFirstAndNotFound';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $router->dispatch($request);
    }

    public function testColonRestfulInControllerWithActionIsNotSingleClass(): void
    {
        $pathInfo = '/:tests/colonRestful:hello/5';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello colon restful with controller', $result->getContent());
    }

    public function testColonRestfulInControllerWithActionIsSingleClass(): void
    {
        $pathInfo = '/:tests/colonRestfulActionSingle:hello/5';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello colon restful with controller and action is single', $result->getContent());
    }

    public function testColonRestfulInActionWithActionIsNotSingleClass(): void
    {
        $pathInfo = '/:tests/colonRestful:hello/5/foo:bar';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello colon restful with action', $result->getContent());
    }

    public function testColonRestfulInActionWithActionIsSingleClass(): void
    {
        $pathInfo = '/:tests/colonRestfulActionSingle:hello/5/foo:bar';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello colon restful with action and action is single', $result->getContent());
    }

    public function testColonInApp(): void
    {
        $pathInfo = '/:tests:router:subAppController/test';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello sub app', $result->getContent());
    }

    protected function createRouter(): Router
    {
        return new Router(new Container());
    }

    protected function createRequest(string $pathInfo, array $attributes, string $method): Request
    {
        // 创建 request
        $request = $this->createMock(Request::class);
        $this->assertInstanceof(Request::class, $request);

        $request->method('getPathInfo')->willReturn($pathInfo);
        $this->assertEquals($pathInfo, $request->getPathInfo());

        $request->method('getMethod')->willReturn($method);
        $this->assertEquals($method, $request->getMethod());

        $request->attributes = new ParameterBag($attributes);

        return $request;
    }
}
