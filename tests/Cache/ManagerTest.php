<?php

declare(strict_types=1);

namespace Tests\Cache;

use Leevel\Cache\Manager;
use Leevel\Cache\Redis\PhpRedis;
use Leevel\Config\Config;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Helper\DeleteDirectory;
use RedisException;
use Tests\TestCase;

final class ManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        $path = __DIR__.'/cacheManager';
        if (is_dir($path)) {
            DeleteDirectory::handle($path);
        }
    }

    public function testBaseUse(): void
    {
        $manager = $this->createManager();
        $manager->set('manager-foo', 'bar');
        static::assertSame('bar', $manager->get('manager-foo'));

        $manager->delete('manager-foo');
        static::assertFalse($manager->get('manager-foo'));
    }

    public function testReconnect(): void
    {
        $manager = $this->createManager();
        $manager->set('manager-foo', 'bar');
        static::assertSame('bar', $manager->get('manager-foo'));

        $manager->delete('manager-foo');
        static::assertFalse($manager->get('manager-foo'));

        $cache = $manager->reconnect();
        $cache->set('manager-foo', 'bar');
        static::assertSame('bar', $cache->get('manager-foo'));
    }

    public function testRedis(): void
    {
        $this->checkRedis();

        $manager = $this->createManager('redis');
        $manager->set('manager-foo', 'bar');
        static::assertSame('bar', $manager->get('manager-foo'));

        $manager->delete('manager-foo');
        static::assertFalse($manager->get('manager-foo'));
    }

    public function testRedisReconnect(): void
    {
        $this->checkRedis();

        $manager = $this->createManager('redis');
        $manager->set('manager-foo', 'bar');
        static::assertSame('bar', $manager->get('manager-foo'));

        $manager->delete('manager-foo');
        static::assertFalse($manager->get('manager-foo'));

        $manager->close();

        $manager->set('manager-foo', 'bar');
        static::assertSame('bar', $manager->get('manager-foo'));

        $manager->delete('manager-foo');
        static::assertFalse($manager->get('manager-foo'));
    }

    public function testRedisCloseTwice(): void
    {
        $this->checkRedis();

        $manager = $this->createManager('redis');
        $manager->close();
        $manager->close(); // 关闭多次不做任何事
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

    protected function makePhpRedis(array $config = []): PhpRedis
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

        return new PhpRedis($config);
    }

    protected function createManager(string $connect = 'file'): Manager
    {
        $container = new Container();
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $config = new Config([
            'cache' => [
                'default' => $connect,
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

        $container->singleton('config', $config);

        if ('redis' === $connect) {
            $redis = new PhpRedis($config->get('cache\\connect.redis'));
            $container->singleton('redis', $redis);
        }

        return $manager;
    }
}
