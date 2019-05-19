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
use Leevel\Di\IContainer;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Http\Request;
use Leevel\Kernel\App;
use Leevel\Router\Router;
use Leevel\Router\RouterProvider;
use Tests\TestCase;

/**
 * routerAnnotation test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.09.18
 *
 * @version 1.0
 */
class RouterAnnotationTest extends TestCase
{
    protected function setUp(): void
    {
        $this->containerClear();
    }

    protected function tearDown(): void
    {
        $this->setUp();
    }

    public function testBaseUse()
    {
        $pathInfo = '/:tests/Annotation/base-use';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $params, $method);
        $router = $this->createRouter($container);

        $router->setControllerDir($controllerDir);

        $result = $router->dispatch($request);

        $this->assertInstanceof(IResponse::class, $result);

        $this->assertSame('hello plus base use', $result->getContent());
    }

    public function testBaseRouterData()
    {
        $container = $this->createContainer();

        $container->singleton('router', $router = $this->createRouter($container));

        $provider = new RouterProviderAnnotation($container);

        $this->assertNull($provider->register());
        $this->assertNull($provider->bootstrap());

        $data = file_get_contents(__DIR__.'/Apps/AppForAnnotation/data.json');

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
    }

    public function testMatchedPetLeevel()
    {
        $pathInfo = '/api/v1/petLeevel/hello';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $params, $method);
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

        $this->assertInstanceof(IResponse::class, $response);

        $this->assertSame('hello plus for petLeevel, params petId is hello', $response->getContent());

        $data = <<<'eot'
            [
                "DemoForAll::handle",
                "Demo2::handle"
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares']
            )
        );

        $router->throughMiddleware($request, [
            $response,
        ]);

        $data = <<<'eot'
            [
                "DemoForAll::handle",
                "Demo2::handle",
                "DemoForAll::terminate",
                "Demo1::terminate",
                "Demo2::terminate"
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares'],
                1
            )
        );

        unset($GLOBALS['demo_middlewares']);
    }

    public function testMatchedBasePathNormalize()
    {
        $pathInfo = '/basePath/normalize';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $params, $method);
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

        $this->assertInstanceof(IResponse::class, $response);

        $this->assertSame('hello plus for basePath normalize', $response->getContent());

        $data = <<<'eot'
            [
                "DemoForAll::handle",
                "DemoForBasePath::handle"
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares']
            )
        );

        $router->throughMiddleware($request, [
            $response,
        ]);

        $data = <<<'eot'
            [
                "DemoForAll::handle",
                "DemoForBasePath::handle",
                "DemoForAll::terminate"
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares'],
                1
            )
        );

        unset($GLOBALS['demo_middlewares']);
    }

    public function testMatchedButMethodNotFound()
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router App\\Router\\Controllers\\PetLeevel::hello() was not found.'
        );

        $pathInfo = '/api/v1/petLeevel/hello';
        $params = [];
        $method = 'PUT';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $params, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);
    }

    public function testFirstLetterNotMatched()
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router App\\Router\\Controllers\\NotFirstLetter::index() was not found.'
        );

        $pathInfo = '/notFirstLetter';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $params, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);
    }

    public function testGroupNotMatched()
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router App\\Router\\Controllers\\PFirstLetterNotGroupNotMatched::index() was not found.'
        );

        $pathInfo = '/pFirstLetterNotGroupNotMatched';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $params, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);
    }

    public function testRegexNotMatched()
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router App\\Router\\Controllers\\PetRegexNotMatched::index() was not found.'
        );

        $pathInfo = '/petRegexNotMatched';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $params, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);
    }

    public function testMatchedButSchemeNotMatched()
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router App\\Router\\Controllers\\Scheme::test() was not found.'
        );

        $pathInfo = '/scheme/test';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $params, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);
    }

    public function testMatchedAndSchemeMatched()
    {
        $pathInfo = '/scheme/test2';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $params, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);

        $this->assertSame('barMatchedScheme', $result->getContent());
    }

    public function testMatchedButDomainNotMatched()
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router App\\Router\\Controllers\\Domain::test() was not found.'
        );

        $pathInfo = '/domain/test';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = $this->createRequest($pathInfo, $params, $method);
        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);
    }

    public function testMatchedAndDomainMatched()
    {
        $pathInfo = '/domain/test2';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $params, [], [], ['HTTP_HOST' => 'queryphp.com']);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);

        $this->assertSame('barMatchedDomain', $result->getContent());
    }

    public function testMatchedAndDomainWithVarMatched()
    {
        $pathInfo = '/domain/test3';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $params, [], [], ['HTTP_HOST' => 'foo-vip.bar.queryphp.com']);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->instance(IRequest::class, $request);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);

        $this->assertSame('barMatchedDomainWithVar and params are {"subdomain":"foo","domain":"bar"}', $result->getContent());
    }

    public function testMatchedAndDomainWithVarNotMatched()
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router App\\Router\\Controllers\\Domain::test3() was not found.'
        );

        $pathInfo = '/domain/test3';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $params, [], [], ['HTTP_HOST' => '123.queryphp.com']);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->instance(IRequest::class, $request);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance('request', $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);

        $this->assertSame('barMatchedDomainWithVar and params are {"subdomain":"foo","domain":"bar"}', $result->getContent());
    }

    public function testMatchedWithExtendVar()
    {
        $pathInfo = '/extendVar/test';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $params);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(IRequest::class, $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);

        $this->assertSame('withExtendVar and params are {"args1":"hello","args2":"world"}', $result->getContent());
    }

    public function testBindNotSet()
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router App\\Router\\Controllers\\BindNotSet::test() was not found.'
        );

        $pathInfo = '/bindNotSet/test';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $params);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(IRequest::class, $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);
    }

    public function testBindNotSet2()
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router App\\Router\\Controllers\\BindNotSet::test2() was not found.'
        );

        $pathInfo = '/bindNotSet/test2';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $params);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(IRequest::class, $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);
    }

    public function testMiddleware()
    {
        $pathInfo = '/middleware/test';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $params);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(IRequest::class, $request);
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

        $this->assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares']
            )
        );

        $router->throughMiddleware($request, [
            $result,
        ]);

        $data = <<<'eot'
            [
                "DemoForAll::handle",
                "Demo2::handle",
                "DemoForAll::terminate",
                "Demo1::terminate",
                "Demo2::terminate"
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares'],
                1
            )
        );

        $this->assertSame('Middleware matched', $result->getContent());

        unset($GLOBALS['demo_middlewares']);
    }

    public function testMiddleware2()
    {
        $pathInfo = '/middleware/test2';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $params);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(IRequest::class, $request);
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

        $this->assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares']
            )
        );

        $router->throughMiddleware($request, [
            $result,
        ]);

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

        $this->assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares'],
                1
            )
        );

        $this->assertSame('Middleware matched 2', $result->getContent());

        unset($GLOBALS['demo_middlewares']);
    }

    public function testMiddleware3()
    {
        $pathInfo = '/middleware/test3';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $params);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(IRequest::class, $request);
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

        $this->assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares']
            )
        );

        $router->throughMiddleware($request, [
            $result,
        ]);

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

        $this->assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares'],
                1
            )
        );

        $this->assertSame('Middleware matched 3', $result->getContent());

        unset($GLOBALS['demo_middlewares']);
    }

    public function testMiddleware4()
    {
        $pathInfo = '/middleware/test4';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $params);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(IRequest::class, $request);
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

        $this->assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares']
            )
        );

        $router->throughMiddleware($request, [
            $result,
        ]);

        $data = <<<'eot'
            [
                "DemoForAll::handle",
                "DemoForAll::terminate",
                "Demo1::terminate"
            ]
            eot;

        $this->assertSame(
            $data,
            $this->varJson(
                $GLOBALS['demo_middlewares'],
                1
            )
        );

        $this->assertSame('Middleware matched 4', $result->getContent());

        unset($GLOBALS['demo_middlewares']);
    }

    public function testBindNotFound()
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router \\Tests\\Router\\Controllers\\Annotation\\BindNotFound@notFound was not found.'
        );

        $pathInfo = '/bindNotFound/test';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $params);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(IRequest::class, $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);
    }

    public function testBindNotFound2()
    {
        $this->expectException(\Leevel\Router\RouterNotFoundException::class);
        $this->expectExceptionMessage(
            'The router \\Tests\\Router\\Controllers\\Annotation\\BindNotFound was not found.'
        );

        $pathInfo = '/bindNotFound/test2';
        $params = [];
        $method = 'GET';
        $controllerDir = 'Controllers';

        $container = $this->createContainer();

        $request = new Request([], [], $params);
        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $container->singleton('router', $router = $this->createRouter($container));
        $container->instance(IRequest::class, $request);
        $container->instance(IContainer::class, $container);

        $provider = new RouterProviderAnnotation($container);

        $router->setControllerDir($controllerDir);

        $provider->register();
        $provider->bootstrap();

        $result = $router->dispatch($request);
    }

    protected function createRouter(Container $container): Router
    {
        $router = new Router($container);

        $container->singleton('app', $container);
        $container->singleton('url', new UrlAnnotation());
        $container->singleton('router', $router);

        return $router;
    }

    protected function createRequest(string $pathInfo, array $params, string $method): IRequest
    {
        $request = new Request([], [], $params);

        $request->setPathInfo($pathInfo);
        $request->setMethod($method);

        $this->assertSame($pathInfo, $request->getPathInfo());
        $this->assertSame($method, $request->getMethod());

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
        'demo1'              => 'Tests\\Router\\Middlewares\\Demo1',
        'demo2'              => 'Tests\\Router\\Middlewares\\Demo2',
        'demo3'              => 'Tests\\Router\\Middlewares\\Demo3',
        'demo_for_base_path' => 'Tests\\Router\\Middlewares\\DemoForBasePath',
        'demo_for_all'       => 'Tests\\Router\\Middlewares\\DemoForAll',
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

class UrlAnnotation
{
    public function getDomain(): string
    {
        return 'queryphp.com';
    }
}
