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

namespace Tests\Cache;

use Leevel\Cache\Manager;
use Leevel\Cache\Redis\PhpRedis;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Option\Option;
use RedisException;
use Tests\TestCase;
use Leevel\Protocol\Coroutine;
use Leevel\Protocol\Pool\IConnection;
use Leevel\Cache\Redis\RedisPool as RedisPools;

/**
 * manager test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.30
 *
 * @version 1.0
 */
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

    public function testRedisPool(): void
    {
        $this->checkRedis();

        $manager = $this->createManagerForRedisPool();
        $manager->set('manager-foo', 'bar');
        $this->assertSame('bar', $manager->get('manager-foo'));

        $manager->delete('manager-foo');
        $this->assertFalse($manager->get('manager-foo'));
    }

    public function testRedisCanOnlyBeUsedInSwoole(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Redis pool can only be used in swoole scenarios.'
        );

        $manager = $this->createManagerForRedisPool(false);
        $manager->set('manager-foo', 'bar');
    }

    protected function checkRedis(): void
    {
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension must be loaded before use.');
        }

        try {
            $this->makePhpRedis();
        } catch (RedisException $th) {
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
                'time_preset' => [],
                'connect'     => [
                    'file' => [
                        'driver'    => 'file',
                        'path'      => __DIR__.'/cacheManager',
                        'serialize' => true,
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
                        'serialize'  => true,
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

    protected function createManagerForRedisPool(bool $inSwoole = true): Manager
    {
        $container = new Container();
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'cache' => [
                'default'     => 'redisPool',
                'expire'      => 86400,
                'time_preset' => [],
                'connect'     => [
                    'file' => [
                        'driver'    => 'file',
                        'path'      => __DIR__.'/cacheManager',
                        'serialize' => true,
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
                        'serialize'  => true,
                        'expire'     => null,
                    ],
                    'redisPool' => [
                        'driver'               => 'redisPool',
                        'redis_connect'        => 'redis',
                        'max_idle_connections' => 30,
                        'min_idle_connections' => 10,
                        'max_push_timeout'     => -1000,
                        'max_pop_timeout'      => 0,
                        'keep_alive_duration'  => 60000,
                        'retry_times'          => 3,
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);
        $redis = new PhpRedis($option->get('cache\\connect.redis'));
        $container->singleton('redis', $redis);

        if (true === $inSwoole) {
            $coroutine = new Coroutine();
            $container->instance('coroutine', $coroutine);
            $container->setCoroutine($coroutine);
            $redisPool = $this->createRedisPool($container, $manager);
            $container->instance('redis.pool', $redisPool);
        }

        return $manager;
    }

    protected function createRedisPool(IContainer $container, Manager $manager): RedisPoolMock
    {
        $options = $container
            ->make('option')
            ->get('cache\\connect.redisPool');

        return new RedisPoolMock($manager, $options['redis_connect'], $options);
    }
}

class RedisPoolMock extends RedisPools
{
    public function returnConnection(IConnection $connection): bool
    {
        return true;
    }
}
