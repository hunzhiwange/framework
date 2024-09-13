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
    #[\Override]
    protected function setUp(): void
    {
        $this->tearDown();
    }

    #[\Override]
    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testBaseUse(): void
    {
        $container = $this->createContainer();
        $manager = $this->createManager($container);
        $container->singleton('sessions', static function () use ($manager): Manager {
            return $manager;
        });

        self::assertFalse($manager->isStart());
        self::assertSame('', $manager->getId());
        self::assertSame('UID', $manager->getName());

        $manager->start();
        self::assertTrue($manager->isStart());

        $manager->set('hello', 'world');
        self::assertSame(['hello' => 'world'], $manager->all());
        self::assertTrue($manager->has('hello'));
        self::assertSame('world', $manager->get('hello'));

        $manager->delete('hello');
        self::assertSame([], $manager->all());
        self::assertFalse($manager->has('hello'));
        self::assertNull($manager->get('hello'));

        $manager->start();
        self::assertTrue($manager->isStart());
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $manager = $this->createManager($container);
        $container->singleton('sessions', static function () use ($manager): Manager {
            return $manager;
        });

        self::assertFalse(Session::proxy()->isStart());
        self::assertSame('', Session::proxy()->getId());
        self::assertSame('UID', Session::proxy()->getName());

        Session::proxy()->start();
        self::assertTrue(Session::proxy()->isStart());

        Session::proxy()->set('hello', 'world');
        self::assertSame(['hello' => 'world'], Session::proxy()->all());
        self::assertTrue(Session::proxy()->has('hello'));
        self::assertSame('world', Session::proxy()->get('hello'));

        Session::proxy()->delete('hello');
        self::assertSame([], Session::proxy()->all());
        self::assertFalse(Session::proxy()->has('hello'));
        self::assertNull(Session::proxy()->get('hello'));

        Session::proxy()->start();
        self::assertTrue(Session::proxy()->isStart());
    }

    protected function createManager(Container $container): Manager
    {
        if (!\extension_loaded('redis')) {
            self::markTestSkipped('Redis extension must be loaded before use.');
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
