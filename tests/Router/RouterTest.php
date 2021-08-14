<?php

declare(strict_types=1);

namespace Tests\Router;

use Leevel\Di\Container;
use Leevel\Http\Request;
use Leevel\Router\IRouter;
use Leevel\Router\Router;
use Leevel\Router\RouterNotFoundException;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;
use Tests\Router\Middlewares\Demo1;
use Tests\Router\Middlewares\Demo2;
use Tests\Router\Middlewares\Demo3;
use Tests\Router\Middlewares\DemoForGroup;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Router",
 *     path="router/router",
 *     zh-CN:description="
 * 路由是整个框架一个非常重要的调度组件，完成从请求到响应的完整过程，通常我们使用代理 `\Leevel\Router\Proxy\Router` 类进行静态调用。
 *
 * **路有服务提供者**
 *
 * 路由服务是系统核心服务，会在系统初始化时通过路由服务提供者注册。
 *
 * ``` php
 * {[\Leevel\Kernel\Utils\Doc::getClassBody(\App\Infra\Provider\Router::class)]}
 * ```
 * ",
 * )
 */
class RouterTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="基本使用",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\Home**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Home::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="控制器方法单独成为类",
     *     zh-CN:description="
     * 方法类的方法固定为 `handle`，返回响应结果。
     *
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\Hello\ActionClass**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Hello\ActionClass::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="控制器方法支持短横线和下换线转换为驼峰规则",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\Hello\ActionConvertFooBar**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Hello\ActionConvertFooBar::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="控制器支持短横线和下换线转换为驼峰规则",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\ControllerConvertFooBar**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\ControllerConvertFooBar::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="控制器支持子目录",
     *     zh-CN:description="
     * 控制器子目录支持无限层级。
     *
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\Sub\World**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Sub\World::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="控制器子目录支持短横线和下换线转换为驼峰规则",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\Sub\World**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Sub\World::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="可以转换为 JSON 的控制器响应",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\ShouldJson**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\ShouldJson::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
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
     * @api(
     *     zh-CN:title="不可以转换为 JSON 的控制器响应强制转化为字符串",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\Response\IntResponse**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Response\IntResponse::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testResponseIsInt(): void
    {
        $pathInfo = '/:tests/Response/IntResponse';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('123456', $result->getContent());
    }

    public function testResponseIsBool(): void
    {
        $pathInfo = '/:tests/Response/BoolResponse';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('1', $result->getContent());
    }

    public function testResponseIsStringable(): void
    {
        $pathInfo = '/:tests/Response/StringableResponse';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('stringable test.', $result->getContent());
    }

    /**
     * @dataProvider getRestfulData
     *
     * @api(
     *     zh-CN:title="RESTFUL 控制器响应",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **测试类型例子**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Router\RouterTest::class, 'getRestfulData', 'define')]}
     * ```
     *
     * **Tests\Router\Controllers\Restful\Show**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Restful\Show::class)]}
     * ```
     *
     * **Tests\Router\Controllers\Restful\Store**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Restful\Store::class)]}
     * ```
     *
     * **Tests\Router\Controllers\Restful\Update**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Restful\Update::class)]}
     * ```
     *
     * **Tests\Router\Controllers\Restful\Destroy**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Restful\Destroy::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
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

    /**
     * @api(
     *     zh-CN:title="setPreRequestMatched 设置路由请求预解析结果",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\PreRequestMatched\Prefix\Bar\Foo**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\PreRequestMatched\Prefix\Bar\Foo::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="穿越中间件",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\Hello\ThroughMiddleware**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Hello\ThroughMiddleware::class)]}
     * ```
     *
     * **Tests\Router\Middlewares\Demo1**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Middlewares\Demo1::class)]}
     * ```
     *
     * **Tests\Router\Middlewares\Demo2**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Middlewares\Demo2::class)]}
     * ```
     *
     * **Tests\Router\Middlewares\Demo3**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Middlewares\Demo3::class)]}
     * ```
     *
     * **Tests\Router\Middlewares\DemoForGroup**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Middlewares\DemoForGroup::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testThroughMiddleware(): void
    {
        $pathInfo = '/:tests/hello/throughMiddleware';
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
            'demo1'        => Demo1::class,
            'demo2'        => Demo2::class,
            'demo3'        => Demo3::class,
            'demoForGroup' => DemoForGroup::class,
        ]);

        $router->setBasePaths([
            '*' => [
                'middlewares' => [
                    'handle' => [
                        Demo2::class.'@handle',
                    ],
                    'terminate' => [
                        Demo1::class.'@terminate',
                        Demo2::class.'@terminate',
                    ],
                ],
            ],
            '/^\\/:tests\/hello\/throughMiddleware\\/$/' => [
                'middlewares' => [
                    'handle' => [
                        Demo3::class.':10,hello@handle',
                    ],
                ],
            ],
            '/^\\/:tests(\\S*)\\/$/' => [
                'middlewares' => [
                    'handle' => [
                        DemoForGroup::class.'@handle',
                    ],
                    'terminate' => [
                        DemoForGroup::class.'@terminate',
                    ],
                ],
            ],
        ]);

        $router->setControllerDir($controllerDir);

        if (isset($GLOBALS['demo_middlewares'])) {
            unset($GLOBALS['demo_middlewares']);
        }

        $result = $router->dispatch($request);
        $router->throughTerminateMiddleware($request, $result);

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
        $pathInfo = '';
        $attributes = [];
        $method = 'OPTIONS';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('CORS', $result->getContent());
    }

    /**
     * @api(
     *     zh-CN:title="控制器支持指定分隔路由前缀",
     *     zh-CN:description="
     * 子目录支持无限层级。
     *
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\Api\V1\Hello\Index**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Api\V1\Hello\Index::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testPrefixInController(): void
    {
        $pathInfo = '/:tests/api/v1:hello';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello api vi', $result->getContent());
    }

    /**
     * @api(
     *     zh-CN:title="控制器支持波浪号分隔为子目录",
     *     zh-CN:description="
     * 子目录支持无限层级。
     *
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\Colon\Hello**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Colon\Hello::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="控制器支持波浪号分隔为子目录多层级例子",
     *     zh-CN:description="
     * 子目录支持无限层级。
     *
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\ColonActionSingle\Hello\World\Foo\Index**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\ColonActionSingle\Hello\World\Foo\Index::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testColonInControllerWithMoreThanOne(): void
    {
        $pathInfo = '/:tests/colon~hello~world~foo';
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
        $pathInfo = '/:tests/colonActionSingle~hello~world~foo';
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
        $pathInfo = '/:tests/~colon/foundAction';
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
        $pathInfo = '/:tests/~colonActionSingle/foundAction';
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
            'The router Tests\\Router\\Controllers\\~colon::notFound() was not found.'
        );

        $pathInfo = '/:tests/~colon/notFound';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $router->dispatch($request);
    }

    /**
     * @api(
     *     zh-CN:title="方法支持波浪号分隔转为驼峰规则",
     *     zh-CN:description="
     * 波浪号分隔方法，方法未独立成类，则将波浪号转为驼峰规则。
     *
     * 下面例子中的方法为 `fooBar`。
     *
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\Colon\Action**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Colon\Action::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testColonInActionAndActionIsNotSingleClass(): void
    {
        $pathInfo = '/:tests/colon~action/foo~bar';
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

    /**
     * @api(
     *     zh-CN:title="方法独立为类支持波浪号分隔转为子目录",
     *     zh-CN:description="
     * 波浪号分隔方法，方法独立成类，则将波浪号转为子目录。
     *
     * 子目录支持无限层级。
     *
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\ColonActionSingle\Action\Foo\Bar**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\ColonActionSingle\Action\Foo\Bar::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testColonInActionAndActionIsSingleClass(): void
    {
        $pathInfo = '/:tests/colonActionSingle~action/foo~bar';
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
        $pathInfo = '/:tests/colon~action/more~foo~bar';
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
        $pathInfo = '/:tests/colonActionSingle~action/more~foo~bar';
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
        $pathInfo = '/:tests/colon~action/~beforeButFirst';
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
        $pathInfo = '/:tests/colonActionSingle~action/~beforeButFirst';
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
            'The router Tests\\Router\\Controllers\\Colon~action::~beforeButFirstAndNotFound() was not found.'
        );

        $pathInfo = '/:tests/colon~action/~beforeButFirstAndNotFound';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $router->dispatch($request);
    }

    /**
     * @api(
     *     zh-CN:title="RESTFUL 控制器支持波浪号分隔为子目录",
     *     zh-CN:description="
     * 子目录支持无限层级。
     *
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\ColonRestful\Hello\Show**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\ColonRestful\Hello\Show::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testColonRestfulInControllerWithActionIsNotSingleClass(): void
    {
        $pathInfo = '/:tests/colonRestful~hello/5';
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
        $pathInfo = '/:tests/colonRestfulActionSingle~hello/5';
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

    /**
     * @api(
     *     zh-CN:title="RESTFUL 方法支持波浪号分隔转为驼峰规则",
     *     zh-CN:description="
     * 波浪号分隔方法，方法未独立成类，则将波浪号转为驼峰规则。
     *
     * 下面例子中的方法为 `fooBar`。
     *
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\ColonRestful\Hello**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\ColonRestful\Hello::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testColonRestfulInActionWithActionIsNotSingleClass(): void
    {
        $pathInfo = '/:tests/colonRestful~hello/5/foo~bar';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello colon restful with controller and action fooBar', $result->getContent());
    }

    /**
     * @api(
     *     zh-CN:title="RESTFUL 方法支持波浪号分隔为子目录",
     *     zh-CN:description="
     * 子目录支持无限层级。
     *
     * **fixture 定义**
     *
     * **Tests\Router\Controllers\ColonRestfulActionSingle\Hello\Foo\Bar**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\ColonRestfulActionSingle\Hello\Foo\Bar::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testColonRestfulInActionWithActionIsSingleClass(): void
    {
        $pathInfo = '/:tests/colonRestfulActionSingle~hello/5/foo~bar';
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

    /**
     * @api(
     *     zh-CN:title="应用支持波浪号分隔为子目录",
     *     zh-CN:description="
     * 子目录支持无限层级。
     *
     * **fixture 定义**
     *
     * **Tests\Router\SubAppController\Router\Controllers\Hello**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\SubAppController\Router\Controllers\Hello::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testColonInApp(): void
    {
        $pathInfo = '/:tests~router~subAppController/hello';
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

    public function testRouterNotFoundExceptionReportable(): void
    {
        $e = new RouterNotFoundException();
        $this->assertFalse($e->reportable());
    }

    protected function createRouter(): Router
    {
        return new Router(new Container());
    }

    protected function createRequest(string $pathInfo, array $attributes, string $method): Request
    {
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
