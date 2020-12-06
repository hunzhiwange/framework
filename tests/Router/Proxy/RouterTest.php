<?php

declare(strict_types=1);

namespace Tests\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Http\Request;
use Leevel\Router\Proxy\Router as ProxyRouter;
use Leevel\Router\Router;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class RouterTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testBaseUse(): void
    {
        $pathInfo = '/:tests';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);

        $container = $this->createContainer();
        $container->singleton('router', function () use ($router): Router {
            return $router;
        });

        $result = $router->dispatch($request);
        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello my home', $result->getContent());
    }

    public function testProxy(): void
    {
        $pathInfo = '/:tests';
        $attributes = [];
        $method = 'GET';
        $controllerDir = 'Router\\Controllers';
        $request = $this->createRequest($pathInfo, $attributes, $method);
        $router = $this->createRouter();
        $router->setControllerDir($controllerDir);

        $container = $this->createContainer();
        $container->singleton('router', function () use ($router): Router {
            return $router;
        });

        $result = ProxyRouter::dispatch($request);
        $this->assertInstanceof(Response::class, $result);
        $this->assertSame('hello my home', $result->getContent());
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

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
