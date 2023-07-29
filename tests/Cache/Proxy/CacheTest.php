<?php

declare(strict_types=1);

namespace Tests\Cache\Proxy;

use Leevel\Cache\Manager;
use Leevel\Cache\Proxy\Cache;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper;
use Leevel\Option\Option;
use Tests\TestCase;

/**
 * @internal
 */
final class CacheTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Helper::deleteDirectory(__DIR__.'/cacheManager');
        Container::singletons()->clear();
    }

    public function testBaseUse(): void
    {
        $container = $this->createContainer();
        $manager = $this->createManager($container);
        $container->singleton('caches', function () use ($manager): Manager {
            return $manager;
        });

        $manager->set('manager-foo', 'bar');
        static::assertSame('bar', $manager->get('manager-foo'));
        $manager->delete('manager-foo');
        static::assertFalse($manager->get('manager-foo'));
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $manager = $this->createManager($container);
        $container->singleton('caches', function () use ($manager): Manager {
            return $manager;
        });

        Cache::set('manager-foo', 'bar');
        static::assertSame('bar', Cache::get('manager-foo'));
        Cache::delete('manager-foo');
        static::assertFalse(Cache::get('manager-foo'));
    }

    protected function createManager(Container $container): Manager
    {
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'cache' => [
                'default' => 'file',
                'expire' => 86400,
                'connect' => [
                    'file' => [
                        'driver' => 'file',
                        'path' => __DIR__.'/cacheManager',
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
        ]);

        $container->singleton('option', $option);

        return $manager;
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
