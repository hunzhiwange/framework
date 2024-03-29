<?php

declare(strict_types=1);

namespace Tests\Router;

use Leevel\Di\Container;
use Leevel\Http\Request;
use Leevel\Kernel\Utils\Api;
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

#[Api([
    'zh-CN:title' => 'Router',
    'path' => 'router/router',
    'zh-CN:description' => <<<'EOT'
路由是整个框架一个非常重要的调度组件，完成从请求到响应的完整过程，通常我们使用代理 `\Leevel\Router\Proxy\Router` 类进行静态调用。

**路有服务提供者**

路由服务是系统核心服务，会在系统初始化时通过路由服务提供者注册。

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\App\Infra\Provider\Router::class)]}
```
EOT,
])]
final class RouterTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '基本使用',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Router\Controllers\Home**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Home::class)]}
```
EOT,
    ])]
    public function testBaseUse(): void
    {
        $pathInfo = '/app:tests';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        static::assertSame('hello my home', $result->getContent());
    }

    #[Api([
        'zh-CN:title' => '控制器方法单独成为类',
        'zh-CN:description' => <<<'EOT'
方法类的方法固定为 `handle`，返回响应结果。

**fixture 定义**

**Tests\Router\Controllers\Hello\ActionClass**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Hello\ActionClass::class)]}
```
EOT,
    ])]
    public function testActionAsClass(): void
    {
        $pathInfo = '/app:tests/hello/actionClass';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        static::assertSame('hello action class', $result->getContent());
    }

    #[Api([
        'zh-CN:title' => '控制器方法支持短横线和下换线转换为驼峰规则',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Router\Controllers\Hello\ActionConvertFooBar**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Hello\ActionConvertFooBar::class)]}
```
EOT,
    ])]
    public function testActionConvert(): void
    {
        $pathInfo = '/app:tests/hello/action_convert-foo_bar';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        static::assertSame('hello action convert foo bar', $result->getContent());
    }

    #[Api([
        'zh-CN:title' => '控制器支持短横线和下换线转换为驼峰规则',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Router\Controllers\ControllerConvertFooBar**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\ControllerConvertFooBar::class)]}
```
EOT,
    ])]
    public function testControllerConvert(): void
    {
        $pathInfo = '/app:tests/controller_convert-foo_bar/bar';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        static::assertSame('hello controller convert', $result->getContent());
    }

    #[Api([
        'zh-CN:title' => '控制器支持子目录',
        'zh-CN:description' => <<<'EOT'
控制器子目录支持无限层级。

**fixture 定义**

**Tests\Router\Controllers\Sub\World**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Sub\World::class)]}
```
EOT,
    ])]
    public function testSubControllerDir(): void
    {
        $pathInfo = '/app:tests/sub/world/foo';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        static::assertSame('hello sub world foo', $result->getContent());
    }

    public function testSubControllerDir2(): void
    {
        $pathInfo = '/app:tests/sub/world/foo/bar';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        static::assertSame('hello sub world foo bar', $result->getContent());
    }

    #[Api([
        'zh-CN:title' => '控制器子目录支持短横线和下换线转换为驼峰规则',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Router\Controllers\Sub\World**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Sub\World::class)]}
```
EOT,
    ])]
    public function testConvertAll(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router Tests\\Router\\Controllers\\HeLloWor\\Bar\\Foo\\XYYAc\\ControllerXxYy::actionXxxYzs() was not found.'
        );

        $pathInfo = '/app:tests/he_llo-wor/Bar/foo/xYY-ac/controller_xx-yy/action-xxx_Yzs';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $router->dispatch($request);
    }

    #[Api([
        'zh-CN:title' => '可以转换为 JSON 的控制器响应',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Router\Controllers\ShouldJson**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\ShouldJson::class)]}
```
EOT,
    ])]
    public function testShouldJson(): void
    {
        $pathInfo = '/app:tests/should_json';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        static::assertSame('{"foo":"bar"}', $result->getContent());
    }

    #[Api([
        'zh-CN:title' => '不可以转换为 JSON 的控制器响应强制转化为字符串',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Router\Controllers\Response\IntResponse**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Response\IntResponse::class)]}
```
EOT,
    ])]
    public function testResponseIsInt(): void
    {
        $pathInfo = '/app:tests/Response/IntResponse';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        static::assertSame('123456', $result->getContent());
    }

    public function testResponseIsBool(): void
    {
        $pathInfo = '/app:tests/Response/BoolResponse';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        static::assertSame('1', $result->getContent());
    }

    public function testResponseIsStringable(): void
    {
        $pathInfo = '/app:tests/Response/StringableResponse';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        static::assertSame('stringable test.', $result->getContent());
    }

    /**
     * @dataProvider getRestfulData
     */
    #[Api([
        'zh-CN:title' => 'RESTFUL 控制器响应',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**测试类型例子**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Router\RouterTest::class, 'getRestfulData', 'define')]}
```

**Tests\Router\Controllers\Restful\Show**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Restful\Show::class)]}
```

**Tests\Router\Controllers\Restful\Store**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Restful\Store::class)]}
```

**Tests\Router\Controllers\Restful\Update**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Restful\Update::class)]}
```

**Tests\Router\Controllers\Restful\Destroy**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Restful\Destroy::class)]}
```
EOT,
    ])]
    public function testRestful(string $method, string $action): void
    {
        $pathInfo = '/app:tests/restful/5';
        $attributes = [];
        $method = $method;
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        static::assertSame('hello for restful '.$action, $result->getContent());
    }

    public static function getRestfulData()
    {
        return [
            ['GET', Router::RESTFUL_SHOW],
            ['POST', Router::RESTFUL_STORE],
            ['PUT', Router::RESTFUL_UPDATE],
            ['DELETE', Router::RESTFUL_DESTROY],
        ];
    }

    #[Api([
        'zh-CN:title' => 'RESTFUL 控制器响应指定方法',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Router\Controllers\Restful\Action**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Restful\Action::class)]}
```
EOT,
    ])]
    public function testRestful2(): void
    {
        $pathInfo = '/app:tests/restful/5/action';
        $attributes = [];
        $method = 'get';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        static::assertSame('hello action for restful show', $result->getContent());
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

    public static function getNodeNotFoundData()
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

    public static function getNodeNotFoundDataWithParams()
    {
        return [
            ['GET', Router::RESTFUL_SHOW],
            ['POST', Router::RESTFUL_STORE],
            ['PUT', Router::RESTFUL_UPDATE],
            ['DELETE', Router::RESTFUL_DESTROY],
        ];
    }

    #[Api([
        'zh-CN:title' => 'setPreRequestMatched 设置路由请求预解析结果',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Router\Controllers\PreRequestMatched\Prefix\Bar\Foo**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\PreRequestMatched\Prefix\Bar\Foo::class)]}
```
EOT,
    ])]
    public function testSetPreRequestMatched(): void
    {
        $pathInfo = '';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setPreRequestMatched($request, [
            IRouter::APP => 'Tests',
            IRouter::CONTROLLER => 'Bar',
            IRouter::ACTION => 'foo',
            IRouter::PREFIX => 'PreRequestMatched\\Prefix',
            IRouter::ATTRIBUTES => null,
            IRouter::MIDDLEWARES => null,
            IRouter::VARS => null,
        ]);
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        static::assertSame('hello preRequestMatched', $result->getContent());
    }

    #[Api([
        'zh-CN:title' => '穿越中间件',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Router\Controllers\Hello\ThroughMiddleware**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Hello\ThroughMiddleware::class)]}
```

**Tests\Router\Middlewares\Demo1**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Middlewares\Demo1::class)]}
```

**Tests\Router\Middlewares\Demo2**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Middlewares\Demo2::class)]}
```

**Tests\Router\Middlewares\Demo3**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Middlewares\Demo3::class)]}
```

**Tests\Router\Middlewares\DemoForGroup**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Middlewares\DemoForGroup::class)]}
```
EOT,
    ])]
    public function testThroughMiddleware(): void
    {
        $pathInfo = '/app:tests/hello/throughMiddleware';
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
            'demo1' => Demo1::class,
            'demo2' => Demo2::class,
            'demo3' => Demo3::class,
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
            '/^\\/app:tests\/hello\/throughMiddleware\\/$/' => [
                'middlewares' => [
                    'handle' => [
                        Demo3::class.':10,hello@handle',
                    ],
                ],
            ],
            '/^\\/app:tests(\\S*)\\/$/' => [
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
        static::assertSame('hello throughMiddleware', $result->getContent());

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

        static::assertSame(
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

        $pathInfo = '/app:tests/hello/ControllerFoundMethodNot/foo';
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

        $pathInfo = '/app:tests/hello/MethodClassFoundButEnterMethodNot';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $router->dispatch($request);
    }

    public function testConfigsForCorsWillBackCorsResponse(): void
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
        static::assertSame('CORS', $result->getContent());
    }

    #[Api([
        'zh-CN:title' => '控制器支持指定分隔路由前缀',
        'zh-CN:description' => <<<'EOT'
子目录支持无限层级。

**fixture 定义**

**Tests\Router\Controllers\Api\V1\Hello\Index**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Api\V1\Hello\Index::class)]}
```
EOT,
    ])]
    public function testPrefixInController(): void
    {
        $pathInfo = '/app:tests/api/v1:hello';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);
        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);
        static::assertSame('hello api vi', $result->getContent());
    }

    public function testRouterNotFoundExceptionReportable(): void
    {
        $e = new RouterNotFoundException();
        static::assertFalse($e->reportable());
    }

    public function test1(): void
    {
        $pathInfo = '/hello';
        $attributes = [];
        $method = 'GET';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $response = new Response('content');
        $router = $this->createRouter();
        $router->throughTerminateMiddleware($request, $response);
    }

    public function test2(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router App\\My\\Router\\Controllers\\Api\\V1\\Hello::index() was not found.'
        );

        $pathInfo = '/app:my/api/v1:hello';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setWithDefaultAppNamespace(true);
        $router->setControllerDir($controllerDir);
        $router->dispatch($request);
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
        static::assertSame($pathInfo, $request->getPathInfo());

        $request->method('getMethod')->willReturn($method);
        static::assertSame($method, $request->getMethod());

        $request->attributes = new ParameterBag($attributes);

        return $request;
    }
}
