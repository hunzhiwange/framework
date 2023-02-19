<?php

declare(strict_types=1);

namespace Tests\Session;

use Leevel\Cache\Manager as CacheManager;
use Leevel\Cache\Redis\PhpRedis;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Option\Option;
use Leevel\Session\Manager;
use RedisException;
use Tests\TestCase;

final class ManagerTest extends TestCase
{
    public function testBaseUse(): void
    {
        $manager = $this->createManager();

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

    public function testConnectFile(): void
    {
        $manager = $this->createManager('file');

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

    public function testConnectRedis(): void
    {
        $this->checkRedis();

        $manager = $this->createManager('redis');

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

    protected function checkRedis(): void
    {
        if (!\extension_loaded('redis')) {
            static::markTestSkipped('Redis extension must be loaded before use.');
        }

        try {
            $this->makePhpRedis();
        } catch (RedisException) {
            static::markTestSkipped('Redis read error on connection and ignore.');
        }
    }

    protected function makePhpRedis(array $option = []): PhpRedis
    {
        $default = [
            'host' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['HOST'],
            'port' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PORT'],
            'password' => $GLOBALS['LEEVEL_ENV']['CACHE']['REDIS']['PASSWORD'],
            'select' => 0,
            'timeout' => 0,
            'persistent' => false,
        ];

        $option = array_merge($default, $option);

        return new PhpRedis($option);
    }

    protected function createManager(string $connect = 'test'): Manager
    {
        if (!\extension_loaded('redis')) {
            static::markTestSkipped('Redis extension must be loaded before use.');
        }

        $container = new Container();
        $manager = new Manager($container);
        $cacheManager = new CacheManager($container);
        $container->instance('caches', $cacheManager);
        $container->instance('redis', $this->makePhpRedis());

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
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
                'default' => $connect,
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

        $container->singleton('option', $option);

        return $manager;
    }
}
