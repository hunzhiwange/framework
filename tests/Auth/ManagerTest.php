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

namespace Tests\Auth;

use Leevel\Auth\Manager;
use Leevel\Cache\Cache;
use Leevel\Cache\File as CacheFile;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\IRequest;
use Leevel\Option\Option;
use Leevel\Session\File as SessionFile;
use Leevel\Session\Session;
use Tests\TestCase;

/**
 * manager test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.11.04
 *
 * @version 1.0
 */
class ManagerTest extends TestCase
{
    public function testBaseUse()
    {
        $manager = $this->createManager();

        $this->assertFalse($manager->isLogin());
        $this->assertSame([], $manager->getLogin());

        $this->assertNull($manager->login(['foo' => 'bar', 'hello' => 'world'], 10));

        $this->assertTrue($manager->isLogin());
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $manager->getLogin());

        $this->assertNull($manager->logout());

        $this->assertFalse($manager->isLogin());
        $this->assertSame([], $manager->getLogin());
    }

    public function testWithToken()
    {
        $manager = $this->createManagerWithToken();

        $manager->setTokenName('token');

        $this->assertFalse($manager->isLogin());
        $this->assertSame([], $manager->getLogin());

        $this->assertNull($manager->login(['foo' => 'bar', 'hello' => 'world'], 10));

        $this->assertTrue($manager->isLogin());
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $manager->getLogin());

        $this->assertNull($manager->logout());

        $this->assertFalse($manager->isLogin());
        $this->assertSame([], $manager->getLogin());
    }

    public function testSetDefaultDriver()
    {
        $manager = $this->createManagerWithTokenAndSession();

        $manager->setDefaultDriver('token');

        $manager->setTokenName('token');

        $this->assertFalse($manager->isLogin());
        $this->assertSame([], $manager->getLogin());

        $this->assertNull($manager->login(['foo' => 'bar', 'hello' => 'world'], 10));

        $this->assertTrue($manager->isLogin());
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $manager->getLogin());

        $this->assertNull($manager->logout());

        $this->assertFalse($manager->isLogin());
        $this->assertSame([], $manager->getLogin());
    }

    protected function createCache()
    {
        return new Cache(new CacheFile([
            'path' => __DIR__.'/cacheFile',
        ]));
    }

    protected function createSession()
    {
        $session = new Session(new SessionFile([
            'path' => __DIR__.'/cache',
        ]));

        $session->start();

        return $session;
    }

    protected function createRequest()
    {
        $request = $this->createMock(IRequest::class);

        $request->method('query')->willReturn('token');
        $this->assertSame('token', $request->query('input_token'));

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

        return $manager;
    }

    protected function createManagerWithToken()
    {
        $container = new Container();

        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'auth' => [
                'default'     => 'api',
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

        $container->singleton('cache', $this->createCache());

        $container->singleton('request', $this->createRequest());

        return $manager;
    }

    protected function createManagerWithTokenAndSession()
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

        $container->singleton('cache', $this->createCache());

        $container->singleton('request', $this->createRequest());

        return $manager;
    }
}
