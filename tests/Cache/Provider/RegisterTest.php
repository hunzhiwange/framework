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

namespace Tests\Cache\Provider;

use Leevel\Cache\File;
use Leevel\Cache\Load;
use Leevel\Cache\Provider\Register;
use Leevel\Di\Container;
use Leevel\Filesystem\Fso;
use Leevel\Option\Option;
use Tests\TestCase;
use Leevel\Cache\Redis\PhpRedis;
use Leevel\Protocol\Coroutine;
use Leevel\Cache\Redis\RedisPool;

/**
 * register test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.26
 *
 * @version 1.0
 */
class RegisterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // caches
        $manager = $container->make('caches');
        $filePath = __DIR__.'/cache/hello.php';
        $this->assertFileNotExists($filePath);
        $manager->set('hello', 'world');
        $this->assertFileExists($filePath);
        $this->assertSame('world', $manager->get('hello'));
        $manager->delete('hello');
        $this->assertFileNotExists($filePath);
        $this->assertFalse($manager->get('hello'));
        Fso::deleteDirectory(__DIR__.'/cache', true);
    }

    public function testCache(): void
    { 
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // cache
        $filePath = __DIR__.'/cache/hello.php';
        $file = $container->make('cache');
        $this->assertInstanceOf(File::class, $file);
        $this->assertFileNotExists($filePath);
        $file->set('hello', 'world');
        $this->assertFileExists($filePath);
        $file->delete('hello');
        $this->assertFileNotExists($filePath);
        $this->assertFalse($file->get('hello'));
        Fso::deleteDirectory(__DIR__.'/cache', true);
    }

    public function testLoad(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // load
        $load = $container->make('cache.load');
        $this->assertInstanceOf(Load::class, $load);
    }

    public function testRedis(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // redis
        $redis = $container->make('redis');
        $this->assertInstanceOf(PhpRedis::class, $redis);
        $redis->set('hello', 'world');
        $this->assertSame('world', $redis->get('hello'));
    }

    public function testRedisPool(): void
    {
        $test = new Register($container = $this->createContainerWithRedisPool());
        $test->register();
        $container->alias($test->providers());

        $redisPool = $container->make('redis.pool');
        $this->assertInstanceOf(RedisPool::class, $redisPool);
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $option = new Option([
            'cache' => [
                'default'     => 'file',
                'expire'      => 86400,
                'time_preset' => [],
                'connect'     => [
                    'file' => [
                        'driver'    => 'file',
                        'path'      => __DIR__.'/cache',
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

        return $container;
    }

    protected function createContainerWithRedisPool(): Container
    {
        $container = new Container();

        $option = new Option([
            'cache' => [
                'default'     => 'redisPool',
                'expire'      => 86400,
                'time_preset' => [],
                'connect'     => [
                    'file' => [
                        'driver'    => 'file',
                        'path'      => __DIR__.'/cache',
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

        $coroutine = new Coroutine();
        $container->instance('coroutine', $coroutine);
        $container->setCoroutine($coroutine);

        return $container;
    }
}
