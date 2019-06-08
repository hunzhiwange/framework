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

namespace Tests\Auth\Middleware;

use Leevel\Auth\Manager;
use Leevel\Auth\Middleware\Auth as MiddlewareAuth;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\IRequest;
use Leevel\Option\Option;
use Leevel\Session\File;
use Tests\TestCase;

/**
 * auth test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.11.05
 *
 * @version 1.0
 */
class AuthTest extends TestCase
{
    public function testBaseUse(): void
    {
        $auth = $this->createManager();

        $middleware = new MiddlewareAuth($auth);

        $request = $this->createRequest('http://127.0.0.1');

        $this->assertNull($middleware->handle(function ($request) {
            $this->assertInstanceof(IRequest::class, $request);
            $this->assertSame('http://127.0.0.1', $request->getUri());
        }, $request));
    }

    public function testAuthFailed(): void
    {
        $this->expectException(\Leevel\Auth\AuthException::class);
        $this->expectExceptionMessage(
            'User authorization failed.'
        );

        $auth = $this->createManagerNotLogin();

        $middleware = new MiddlewareAuth($auth);

        $request = $this->createRequest('http://127.0.0.1');

        $this->assertNull($middleware->handle(function ($request) {
            $this->assertInstanceof(IRequest::class, $request);
            $this->assertSame('http://127.0.0.1', $request->getUri());
        }, $request));
    }

    protected function createRequest(string $url): IRequest
    {
        $request = $this->createMock(IRequest::class);

        $request->method('getUri')->willReturn($url);
        $this->assertEquals($url, $request->getUri());

        return $request;
    }

    protected function createManager()
    {
        $container = new Container();

        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'auth' => [
                'default'     => 'web',
                'web_default' => 'session',
                'api_default' => 'token',
                'connect'     => [
                    'session' => [
                        'driver' => 'session',
                        'token'  => 'token',
                    ],
                    'token' => [
                        'driver'      => 'token',
                        'token'       => null,
                        'input_token' => 'token',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        $container->singleton('session', $this->createSession());

        $manager->login(['foo' => 'bar']);

        return $manager;
    }

    protected function createManagerNotLogin()
    {
        $container = new Container();

        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'auth' => [
                'default'     => 'web',
                'web_default' => 'session',
                'api_default' => 'token',
                'connect'     => [
                    'session' => [
                        'driver' => 'session',
                        'token'  => 'token',
                    ],
                    'token' => [
                        'driver'      => 'token',
                        'token'       => null,
                        'input_token' => 'token',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        $container->singleton('session', $this->createSession());

        return $manager;
    }

    protected function createSession()
    {
        $session = new File([
            'path' => __DIR__.'/cache',
        ]);

        $session->start();

        return $session;
    }
}
