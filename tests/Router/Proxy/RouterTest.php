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
