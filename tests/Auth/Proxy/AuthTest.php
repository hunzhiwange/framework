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

namespace Tests\Auth\Proxy;

use Leevel\Auth\Manager;
use Leevel\Auth\Proxy\Auth;
use Leevel\Cache\File as CacheFile;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Option\Option;
use Leevel\Session\File as SessionFile;
use Tests\TestCase;

class AuthTest extends TestCase
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
        $container = $this->createContainer();
        $manager = $this->createManager($container);
        $container->singleton('auths', function () use ($manager): Manager {
            return $manager;
        });
        $this->assertFalse($manager->isLogin());
        $this->assertSame([], $manager->getLogin());
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $manager = $this->createManager($container);
        $container->singleton('auths', function () use ($manager): Manager {
            return $manager;
        });
        $this->assertFalse(Auth::isLogin());
        $this->assertSame([], Auth::getLogin());
    }

    protected function createManager(Container $container): Manager
    {
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

    protected function createSession(): SessionFile
    {
        $session = new SessionFile(new CacheFile([
            'path' => __DIR__.'/cache',
        ]));

        $session->start();

        return $session;
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
