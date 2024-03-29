<?php

declare(strict_types=1);

namespace Tests\Router;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\Request;
use Leevel\Kernel\App;
use Leevel\Kernel\Utils\Api;
use Leevel\Router\Router;
use Leevel\Router\RouterProvider;
use Leevel\Router\ScanRouter;
use Leevel\Router\Url;
use Symfony\Component\HttpFoundation\Response;
use Tests\Router\Middlewares\Demo1;
use Tests\Router\Middlewares\Demo2;
use Tests\Router\Middlewares\Demo3;
use Tests\Router\Middlewares\DemoForAll;
use Tests\Router\Middlewares\DemoForBasePath;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '注解路由',
    'path' => 'router/annotation',
    'zh-CN:description' => <<<'EOT'
QueryPHP 除了传统的自动匹配 MVC 路由之外，也支持自定义的注解路由。
EOT,
])]
final class RouterAnnotationTest extends TestCase
{
    protected function setUp(): void
    {
        $this->containerClear();
    }

    protected function tearDown(): void
    {
        $this->setUp();
    }

    public function testBaseUse(): void
    {
        $pathInfo = '/app:tests/Annotation/base-use';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter($container);

        $router->setControllerDir($controllerDir);

        $result = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $result);

        static::assertSame('hello plus base use', $result->getContent());
    }

    #[Api([
        'zh-CN:title' => '注解路由扫描',
        'zh-CN:description' => <<<'EOT'
QueryPHP 系统会根据路由服务提供者信息，扫描系统的注解生成框架的注解路由，并且支持缓存到文件。

**fixture 定义**

**路由服务提供者 Tests\Router\RouterProviderAnnotation**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\RouterProviderAnnotation::class)]}
```

**路由注解缓存结果 tests/Router/Apps/AppForAnnotation/data.json**

``` json
{[file_get_contents('vendor/hunzhiwange/framework/tests/Router/Apps/AppForAnnotation/data.json')]}
```
EOT,
    ])]
    public function testBaseRouterData(): void
    {
        $container = $this->createContainer();

        $container->singleton('router', $router = $this->createRouter($container));

        $provider = new RouterProviderAnnotation($container);

        static::assertNull($provider->register());
        static::assertNull($provider->bootstrap());

        $data = file_get_contents(__DIR__.'/Apps/AppForAnnotation/data.json');

        static::assertEquals(
            $data,
            $this->varJson(
                [
                    'base_paths' => $router->getBasePaths(),
                    'groups' => $router->getGroups(),
                    'routers' => $router->getRouters(),
                ]
            )
        );
    }

    #[Api([
        'zh-CN:title' => '基本使用',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**路由定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Router\Apps\AppForAnnotation\Controllers\Pet::class, 'petLeevel', 'define')]}
```

**控制器**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Annotation\PetLeevel::class)]}
```
EOT,
    ])]
    public function testMatchedPetLeevel(): void
    {
        $pathInfo = '/api/v1/petLeevel/hello';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $attributes, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        if (isset($GLOBALS['demo_middlewares'])) {
            unset($GLOBALS['demo_middlewares']);
        }

        $response = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $response);

        static::assertSame('hello plus for petLeevel, attributes petId is hello', $response->getContent());

        $data = <<<'eot'
            [
                "DemoForAll::handle",
                "Demo2::handle"
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares']
            )
        );

        $router->throughTerminateMiddleware($request, $response);

        $data = <<<'eot'
            [
                "DemoForAll::handle",
                "Demo2::handle",
                "DemoForAll::terminate",
                "Demo1::terminate",
                "Demo2::terminate"
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares'],
                1
            )
        );

        unset($GLOBALS['demo_middlewares']);
    }

    public function testMatchedPetLeevelNotInGroup(): void
    {
        $pathInfo = '/api/notInGroup/petLeevel/hello';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $attributes, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        if (isset($GLOBALS['demo_middlewares'])) {
            unset($GLOBALS['demo_middlewares']);
        }

        $response = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $response);
        static::assertSame('petLeevelNotInGroup', $response->getContent());
    }

    #[Api([
        'zh-CN:title' => '基础路径匹配',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**路由定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Router\Apps\AppForAnnotation\Controllers\BasePath::class, 'foo', 'define')]}
```

**控制器**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Router\Controllers\Annotation\BasePath::class)]}
```
EOT,
    ])]
    public function testMatchedBasePathNormalize(): void
    {
        $pathInfo = '/basePath/normalize';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $attributes, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $provider->register();
        $provider->bootstrap();

        if (isset($GLOBALS['demo_middlewares'])) {
            unset($GLOBALS['demo_middlewares']);
        }

        $response = $router->dispatch($request);

        $this->assertInstanceof(Response::class, $response);

        static::assertSame('hello plus for basePath normalize', $response->getContent());

        $data = <<<'eot'
            [
                "DemoForAll::handle",
                "DemoForBasePath::handle"
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares']
            )
        );

        $router->throughTerminateMiddleware($request, $response);

        $data = <<<'eot'
            [
                "DemoForAll::handle",
                "DemoForBasePath::handle",
                "DemoForAll::terminate"
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares'],
                1
            )
        );

        unset($GLOBALS['demo_middlewares']);
    }

    public function testMatchedButMethodNotFound(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router App\\Router\\Controllers\\Api\\V1\\PetLeevel::hello() was not found.'
        );

        $pathInfo = '/api/v1/petLeevel/hello';
        $attributes = [];
        $method = 'PUT';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $attributes, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $router->dispatch($request);
    }

    public function testFirstLetterNotMatched(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router App\\Router\\Controllers\\NotFirstLetter::index() was not found.'
        );

        $pathInfo = '/notFirstLetter';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $attributes, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $router->dispatch($request);
    }

    public function testFirstLetterMatchedAndGroupMatched(): void
    {
        $pathInfo = '/newPrefix/v1/petLeevel/hello';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $attributes, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);
        static::assertSame('hello plus for newPrefix, attributes petId is hello', $result->getContent());
    }

    public function testFirstLetterMatchedButGroupNotMatched(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router App\\Router\\Controllers\\NNotFound::index() was not found.'
        );

        $pathInfo = '/nNotFound';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $attributes, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $router->dispatch($request);
    }

    public function testGroupNotMatched(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router App\\Router\\Controllers\\PFirstLetterNotGroupNotMatched::index() was not found.'
        );

        $pathInfo = '/pFirstLetterNotGroupNotMatched';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $attributes, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $router->dispatch($request);
    }

    public function testRegexNotMatched(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router App\\Router\\Controllers\\PetRegexNotMatched::index() was not found.'
        );

        $pathInfo = '/petRegexNotMatched';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $attributes, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $router->dispatch($request);
    }

    public function testRegexNotMatched2(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router App\\Router\\Controllers\\Api\\V1::petRegexNotMatched() was not found.'
        );

        $pathInfo = '/api/v1/petRegexNotMatched';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $attributes, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $router->dispatch($request);
    }

    #[Api([
        'zh-CN:title' => 'Attributes 扩展变量匹配',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**路由定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Router\Apps\AppForAnnotation\Controllers\ExtendVar::class, 'withExtendVar', 'define')]}
```

**控制器**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Router\Apps\AppForAnnotation\Controllers\ExtendVar::class, 'withExtendVar')]}
```
EOT,
    ])]
    public function testMatchedWithExtendVar(): void
    {
        $pathInfo = '/extendVar/test';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $attributes);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(Request::class, $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);

        static::assertSame('withExtendVar and attributes are {"args1":"hello","args2":"world"}', $result->getContent());
    }

    public function testBindNotSet(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router \\Tests\\Router\\Apps\\AppForAnnotation\\Controllers\\BindNotSet@routePlaceholderFoo was not found.'
        );

        $pathInfo = '/bindNotSet/test';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $attributes);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(Request::class, $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $router->dispatch($request);
    }

    public function testBindNotSet2(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router \\Tests\\Router\\Apps\\AppForAnnotation\\Controllers\\BindNotSet@routePlaceholderBar was not found.'
        );

        $pathInfo = '/bindNotSet/test2';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $attributes);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(Request::class, $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $router->dispatch($request);
    }

    #[Api([
        'zh-CN:title' => 'Middlewares 中间件匹配',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**路由定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Router\Apps\AppForAnnotation\Controllers\Middleware::class, 'foo', 'define')]}
```

**控制器**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Router\Apps\AppForAnnotation\Controllers\Middleware::class, 'foo')]}
```
EOT,
    ])]
    public function testMiddleware(): void
    {
        $pathInfo = '/middleware/test';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $attributes);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(Request::class, $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        if (isset($GLOBALS['demo_middlewares'])) {
            unset($GLOBALS['demo_middlewares']);
        }

        $result = $router->dispatch($request);

        $data = <<<'eot'
            [
                "DemoForAll::handle",
                "Demo2::handle"
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares']
            )
        );

        $router->throughTerminateMiddleware($request, $result);

        $data = <<<'eot'
            [
                "DemoForAll::handle",
                "Demo2::handle",
                "DemoForAll::terminate",
                "Demo1::terminate",
                "Demo2::terminate"
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares'],
                1
            )
        );

        static::assertSame('Middleware matched', $result->getContent());

        unset($GLOBALS['demo_middlewares']);
    }

    #[Api([
        'zh-CN:title' => 'Middlewares 中间件匹配支持数组',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**路由定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Router\Apps\AppForAnnotation\Controllers\Middleware::class, 'bar', 'define')]}
```

**控制器**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Router\Apps\AppForAnnotation\Controllers\Middleware::class, 'bar')]}
```
EOT,
    ])]
    public function testMiddleware2(): void
    {
        $pathInfo = '/middleware/test2';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $attributes);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(Request::class, $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        if (isset($GLOBALS['demo_middlewares'])) {
            unset($GLOBALS['demo_middlewares']);
        }

        $result = $router->dispatch($request);

        $data = <<<'eot'
            [
                "DemoForAll::handle",
                "Demo2::handle",
                "Demo3::handle(arg1:10,arg2:world)"
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares']
            )
        );

        $router->throughTerminateMiddleware($request, $result);

        $data = <<<'eot'
            [
                "DemoForAll::handle",
                "Demo2::handle",
                "Demo3::handle(arg1:10,arg2:world)",
                "DemoForAll::terminate",
                "Demo1::terminate",
                "Demo2::terminate"
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares'],
                1
            )
        );

        static::assertSame('Middleware matched 2', $result->getContent());

        unset($GLOBALS['demo_middlewares']);
    }

    public function testMiddleware3(): void
    {
        $pathInfo = '/middleware/test3';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $attributes);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(Request::class, $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        if (isset($GLOBALS['demo_middlewares'])) {
            unset($GLOBALS['demo_middlewares']);
        }

        $result = $router->dispatch($request);

        $data = <<<'eot'
            [
                "DemoForAll::handle",
                "Demo2::handle",
                "Demo3::handle(arg1:10,arg2:world)",
                "DemoForBasePath::handle"
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares']
            )
        );

        $router->throughTerminateMiddleware($request, $result);

        $data = <<<'eot'
            [
                "DemoForAll::handle",
                "Demo2::handle",
                "Demo3::handle(arg1:10,arg2:world)",
                "DemoForBasePath::handle",
                "DemoForAll::terminate",
                "Demo1::terminate",
                "Demo2::terminate"
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares'],
                1
            )
        );

        static::assertSame('Middleware matched 3', $result->getContent());

        unset($GLOBALS['demo_middlewares']);
    }

    #[Api([
        'zh-CN:title' => 'Middlewares 中间件匹配支持类名',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**路由定义**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Router\Apps\AppForAnnotation\Controllers\Middleware::class, 'world', 'define')]}
```

**控制器**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Tests\Router\Apps\AppForAnnotation\Controllers\Middleware::class, 'world')]}
```
EOT,
    ])]
    public function testMiddleware4(): void
    {
        $pathInfo = '/middleware/test4';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $attributes);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(Request::class, $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        if (isset($GLOBALS['demo_middlewares'])) {
            unset($GLOBALS['demo_middlewares']);
        }

        $result = $router->dispatch($request);

        $data = <<<'eot'
            [
                "DemoForAll::handle"
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares']
            )
        );

        $router->throughTerminateMiddleware($request, $result);

        $data = <<<'eot'
            [
                "DemoForAll::handle",
                "DemoForAll::terminate",
                "Demo1::terminate"
            ]
            eot;

        static::assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares'],
                1
            )
        );

        static::assertSame('Middleware matched 4', $result->getContent());

        unset($GLOBALS['demo_middlewares']);
    }

    public function testBindNotFound(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router \\Tests\\Router\\Controllers\\Annotation\\BindNotFound@notFound was not found.'
        );

        $pathInfo = '/bindNotFound/test';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $attributes);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(Request::class, $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $router->dispatch($request);
    }

    public function testBindNotFound2(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router \\Tests\\Router\\Controllers\\Annotation\\BindNotFound was not found.'
        );

        $pathInfo = '/bindNotFound/test2';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $attributes);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(Request::class, $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $router->dispatch($request);
    }

    public function testBindMethodNotFound(): void
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router \\Tests\\Router\\Controllers\\Annotation\\BindMethodNotFound was not found.'
        );

        $pathInfo = '/bindNotFound/test3';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $attributes);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(Request::class, $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $router->dispatch($request);
    }

    protected function createRouter(Container $container): Router
    {
        $router = new Router($container);

        $container->singleton('app', $container);
        $container->singleton('router', $router);

        return $router;
    }

    protected function createRequest(string $pathInfo, array $attributes, string $method): Request
    {
        $request = new Request([], [], $attributes);

        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        static::assertSame($pathInfo, $request->getPathInfo());
        static::assertSame($method, $request->getMethod());

        return $request;
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $app = new App($container, '');
        $app->setAppPath(__DIR__.'/Apps/AppForAnnotation');
        $app->setPath(__DIR__.'/Apps/AppForAnnotation');
        $app->setRouterCachedPath(__DIR__.'/router_cached.php');

        $container->instance('app', $app);

        return $container;
    }

    protected function containerClear(): void
    {
        Container::singletons()->clear();
    }
}

class RouterProviderAnnotation extends RouterProvider
{
    protected ?string $controllerDir = 'Router\\Controllers';

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
        'demo1' => Demo1::class,
        'demo2' => Demo2::class,
        'demo3' => Demo3::class,
        'demo_for_base_path' => DemoForBasePath::class,
        'demo_for_all' => DemoForAll::class,
    ];

    protected array $basePaths = [
        '*' => [
            'middlewares' => 'demo_for_all',
        ],
        '/basePath/normalize' => [
            'middlewares' => 'demo_for_base_path',
        ],
    ];

    protected array $groups = [
        'pet' => [],
        'store' => [],
        'user' => [],
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
        'newPrefix/v1' => [],
    ];

    public function bootstrap(): void
    {
        parent::bootstrap();
    }

    public function getRouters(): array
    {
        return parent::getRouters();
    }

    protected function makeScanRouter(): ScanRouter
    {
        $scanRouter = parent::makeScanRouter();
        $scanRouter->setControllerDir('');

        return $scanRouter;
    }
}
