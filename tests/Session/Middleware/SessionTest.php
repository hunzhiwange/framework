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

namespace Tests\Session\Middleware;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\Request;
use Leevel\Http\Response;
use Leevel\Option\Option;
use Leevel\Session\Manager;
use Leevel\Session\Middleware\Session as MiddlewareSession;
use Tests\TestCase;

class SessionTest extends TestCase
{
    public function testBaseUse(): void
    {
        $session = $this->createSession();

        $middleware = new MiddlewareSession($session);

        $request = $this->createRequest('http://127.0.0.1');

        $this->assertNull($middleware->handle(function ($request) {
            $this->assertInstanceof(Request::class, $request);
            $this->assertSame('http://127.0.0.1', $request->getUri());
        }, $request));
    }

    public function testTerminate(): void
    {
        $session = $this->createSession();

        $middleware = new MiddlewareSession($session);

        $request = $this->createRequest('http://127.0.0.1');

        $response = $this->createResponse();

        $this->assertNull($middleware->handle(function ($request) {
            $this->assertInstanceof(Request::class, $request);
            $this->assertSame('http://127.0.0.1', $request->getUri());
        }, $request));

        $this->assertNull($middleware->terminate(function ($request, $response) {
            $this->assertInstanceof(Request::class, $request);
            $this->assertSame('http://127.0.0.1', $request->getUri());
            $this->assertInstanceof(Response::class, $response);
            $this->assertSame('content', $response->getContent());
        }, $request, $response));
    }

    protected function createRequest(string $url): Request
    {
        $request = $this->createMock(Request::class);

        $request->cookies = new CookieTest();

        $request->method('getUri')->willReturn($url);
        $this->assertEquals($url, $request->getUri());

        return $request;
    }

    protected function createResponse(): Response
    {
        $response = $this->createMock(Response::class);

        $response->method('getContent')->willReturn('content');
        $this->assertEquals('content', $response->getContent());

        return $response;
    }

    protected function createSession(): Manager
    {
        $container = new Container();
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'session' => [
                'default'       => 'test',
                'id'            => null,
                'name'          => 'UID',
                'expire'        => 86400,
                'connect'       => [
                    'test' => [
                        'driver' => 'test',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        return $manager;
    }
}

class CookieTest
{
    public function get(string $name, $default = null)
    {
    }
}
