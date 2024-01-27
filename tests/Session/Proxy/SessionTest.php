<?php

declare(strict_types=1);

namespace Tests\Session\Proxy;

use Leevel\Cache\Manager as CacheManager;
use Leevel\Cache\Redis;
use Leevel\Config\Config;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Session\Manager;
use Leevel\Session\Proxy\Session;
use Tests\TestCase;

final class SessionTest extends TestCase
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

        static::assertFalse($manager->isStart());
        static::assertSame('', $manager->getId());
        static::assertSame('UID', $manager->getName());

        $manager->start();
        static::assertTrue($manager->isStart());

        $manager->set('hello', 'world');
        static::assertSame(['hello' => 'world'], $manager->all());
        static::assertTrue($manager->has('hello'));
        static::assertSame('world', $manager->get('hello'));

        $manager->delete('hello');
        static::assertSame([], $manager->all());
        static::assertFalse($manager->has('hello'));
        static::assertNull($manager->get('hello'));

        $manager->start();
        static::assertTrue($manager->isStart());
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $manager = $this->createManager($container);
        $container->singleton('sessions', function () use ($manager): Manager {
            return $manager;
        });

        static::assertFalse(Session::isStart());
        static::assertSame('', Session::getId());
        static::assertSame('UID', Session::getName());

        Session::start();
        static::assertTrue(Session::isStart());

        Session::set('hello', 'world');
        static::assertSame(['hello' => 'world'], Session::all());
        static::assertTrue(Session::has('hello'));
        static::assertSame('world', Session::get('hello'));

        Session::delete('hello');
        static::assertSame([], Session::all());
        static::assertFalse(Session::has('hello'));
        static::assertNull(Session::get('hello'));

        Session::start();
        static::assertTrue(Session::isStart());
    }

    protected function createManager(Container $container): Manager
    {
        if (!\extension_loaded('redis')) {
            static::markTestSkipped('Redis extension must be loaded before use.');
        }

        $manager = new Manager($container);
        $cacheManager = new CacheManager($container);
        $container->instance('caches', $cacheManager);
        $container->instance('redis', $this->makePhpRedis());

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $config = new Config([
            'cache' => [
                'default' => 'file',
                'expire' => 86400,
                'time_preset' => [],
                'connect' => [
                    'file' => [
                        'driver' => 'file',
                        'path' => __DIR__.'/session',
                        'expire' => null,
                    ],
                    'redis' => [
                        'driver' => 'redis',
                        'host' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['HOST'],
                        'port' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PORT'],
                        'password' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PASSWORD'],
                        'select' => 0,
                        'timeout' => 0,
                        'persistent' => false,
                        'expire' => null,
                    ],
                ],
            ],
            'session' => [
                'default' => 'test',
                'id' => null,
                'name' => 'UID',
                'connect' => [
                    'file' => [
                        'driver' => 'file',
                        'file_driver' => 'file',
                    ],
                    'redis' => [
                        'driver' => 'redis',
                        'redis_driver' => 'redis',
                    ],
                    'test' => [
                        'driver' => 'test',
                    ],
                ],
            ],
        ]);

        $container->singleton('config', $config);

        return $manager;
    }

    protected function makePhpRedis(array $config = []): Redis
    {
        $default = [
            'host' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['HOST'],
            'port' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PORT'],
            'password' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PASSWORD'],
            'select' => 0,
            'timeout' => 0,
            'persistent' => false,
        ];

        $config = array_merge($default, $config);

        return new Redis($config);
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
