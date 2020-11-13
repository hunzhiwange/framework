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

namespace Tests\Session\Proxy;

use Leevel\Cache\Manager as CacheManager;
use Leevel\Cache\Redis\PhpRedis;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Option\Option;
use Leevel\Session\Manager;
use Leevel\Session\Proxy\Session;
use Tests\TestCase;

class SessionTest extends TestCase
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
        $container->singleton('sessions', function () use ($manager): Manager {
            return $manager;
        });

        $this->assertFalse($manager->isStart());
        $this->assertSame('', $manager->getId());
        $this->assertSame('UID', $manager->getName());

        $manager->start();
        $this->assertTrue($manager->isStart());

        $manager->set('hello', 'world');
        $this->assertSame(['hello' => 'world'], $manager->all());
        $this->assertTrue($manager->has('hello'));
        $this->assertSame('world', $manager->get('hello'));

        $manager->delete('hello');
        $this->assertSame([], $manager->all());
        $this->assertFalse($manager->has('hello'));
        $this->assertNull($manager->get('hello'));

        $manager->start();
        $this->assertTrue($manager->isStart());
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $manager = $this->createManager($container);
        $container->singleton('sessions', function () use ($manager): Manager {
            return $manager;
        });

        $this->assertFalse(Session::isStart());
        $this->assertSame('', Session::getId());
        $this->assertSame('UID', Session::getName());

        Session::start();
        $this->assertTrue(Session::isStart());

        Session::set('hello', 'world');
        $this->assertSame(['hello' => 'world'], Session::all());
        $this->assertTrue(Session::has('hello'));
        $this->assertSame('world', Session::get('hello'));

        Session::delete('hello');
        $this->assertSame([], Session::all());
        $this->assertFalse(Session::has('hello'));
        $this->assertNull(Session::get('hello'));

        Session::start();
        $this->assertTrue(Session::isStart());
    }

    protected function createManager(Container $container): Manager
    {
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension must be loaded before use.');
        }

        $manager = new Manager($container);
        $cacheManager = new CacheManager($container);
        $container->instance('caches', $cacheManager);
        $container->instance('redis', $this->makePhpRedis());

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'cache' => [
                'default'     => 'file',
                'expire'      => 86400,
                'time_preset' => [],
                'connect'     => [
                    'file' => [
                        'driver'    => 'file',
                        'path'      => __DIR__.'/session',
                        'expire'    => null,
                    ],
                    'redis' => [
                        'driver'     => 'redis',
                        'host'       => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['HOST'],
                        'port'       => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PORT'],
                        'password'   => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PASSWORD'],
                        'select'     => 0,
                        'timeout'    => 0,
                        'persistent' => false,
                        'expire'     => null,
                    ],
                ],
            ],
            'session' => [
                'default'       => 'test',
                'id'            => null,
                'name'          => 'UID',
                'connect'       => [
                    'file' => [
                        'driver'      => 'file',
                        'file_driver' => 'file',
                    ],
                    'redis' => [
                        'driver'       => 'redis',
                        'redis_driver' => 'redis',
                    ],
                    'test' => [
                        'driver' => 'test',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        return $manager;
    }

    protected function makePhpRedis(array $option = []): PhpRedis
    {
        $default = [
            'host'        => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['HOST'],
            'port'        => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PORT'],
            'password'    => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PASSWORD'],
            'select'      => 0,
            'timeout'     => 0,
            'persistent'  => false,
        ];

        $option = array_merge($default, $option);

        return new PhpRedis($option);
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
