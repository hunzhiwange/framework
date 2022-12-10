<?php

declare(strict_types=1);

namespace Tests\Cache;

use Leevel\Cache\Manager;
use Leevel\Cache\Redis\PhpRedis;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Option\Option;
use RedisException;
use Tests\TestCase;

class ManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        $path = __DIR__.'/cacheManager';
        if (is_dir($path)) {
            rmdir($path);
        }
    }

    public function testBaseUse(): void
    {
        $manager = $this->createManager();
        $manager->set('manager-foo', 'bar');
        $this->assertSame('bar', $manager->get('manager-foo'));

        $manager->delete('manager-foo');
        $this->assertFalse($manager->get('manager-foo'));
    }

    public function testRedis(): void
    {
        $this->checkRedis();

        $manager = $this->createManager('redis');
        $manager->set('manager-foo', 'bar');
        $this->assertSame('bar', $manager->get('manager-foo'));

        $manager->delete('manager-foo');
        $this->assertFalse($manager->get('manager-foo'));
    }

    public function testRedisReconnect(): void
    {
        $this->checkRedis();

        $manager = $this->createManager('redis');
        $manager->set('manager-foo', 'bar');
        $this->assertSame('bar', $manager->get('manager-foo'));

        $manager->delete('manager-foo');
        $this->assertFalse($manager->get('manager-foo'));

        $manager->close();

        $manager->set('manager-foo', 'bar');
        $this->assertSame('bar', $manager->get('manager-foo'));

        $manager->delete('manager-foo');
        $this->assertFalse($manager->get('manager-foo'));
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
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension must be loaded before use.');
        }

        try {
            $this->makePhpRedis();
        } catch (RedisException) {
            $this->markTestSkipped('Redis read error on connection and ignore.');
        }
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

    protected function createManager(string $connect = 'file'): Manager
    {
        $container = new Container();
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'cache' => [
                'default'     => $connect,
                'expire'      => 86400,
                'connect'     => [
                    'file' => [
                        'driver'    => 'file',
                        'path'      => __DIR__.'/cacheManager',
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
        ]);

        $container->singleton('option', $option);

        if ('redis' === $connect) {
            $redis = new PhpRedis($option->get('cache\\connect.redis'));
            $container->singleton('redis', $redis);
        }

        return $manager;
    }
}
