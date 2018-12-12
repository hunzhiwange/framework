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

namespace Tests\Session\Middleware;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\IRequest;
use Leevel\Http\IResponse;
use Leevel\Option\Option;
use Leevel\Session\Manager;
use Leevel\Session\Middleware\Session as MiddlewareSession;
use Tests\TestCase;

/**
 * session test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.18
 *
 * @version 1.0
 */
class SessionTest extends TestCase
{
    public function testBaseUse()
    {
        $session = $this->createSession();

        $middleware = new MiddlewareSession($session);

        $request = $this->createRequest('http://127.0.0.1');

        $this->assertNull($middleware->handle(function ($request) {
            $this->assertInstanceof(IRequest::class, $request);
            $this->assertSame('http://127.0.0.1', $request->getUri());
        }, $request));
    }

    public function testTerminate()
    {
        $session = $this->createSession();

        $middleware = new MiddlewareSession($session);

        $request = $this->createRequest('http://127.0.0.1');

        $response = $this->createResponse();

        $this->assertNull($middleware->handle(function ($request) {
            $this->assertInstanceof(IRequest::class, $request);
            $this->assertSame('http://127.0.0.1', $request->getUri());
        }, $request));

        $this->assertNull($middleware->terminate(function ($request, $response) {
            $this->assertInstanceof(IRequest::class, $request);
            $this->assertSame('http://127.0.0.1', $request->getUri());
            $this->assertInstanceof(IResponse::class, $response);
            $this->assertSame('content', $response->getContent());
        }, $request, $response));
    }

    protected function createRequest(string $url): IRequest
    {
        $request = $this->createMock(IRequest::class);

        $request->cookies = new CookieTest();

        $request->method('getUri')->willReturn($url);
        $this->assertEquals($url, $request->getUri());

        return $request;
    }

    protected function createResponse(): IResponse
    {
        $response = $this->createMock(IResponse::class);

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
                'default'       => 'nulls',
                'id'            => null,
                'name'          => 'UID',
                'expire'        => 86400,
                'connect'       => [
                    'nulls' => [
                        'driver' => 'nulls',
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
