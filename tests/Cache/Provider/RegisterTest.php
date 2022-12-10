<?php

declare(strict_types=1);

namespace Tests\Cache\Provider;

use Leevel\Cache\File;
use Leevel\Cache\Provider\Register;
use Leevel\Cache\Redis\PhpRedis;
use Leevel\Di\Container;
use Leevel\Filesystem\Helper;
use Leevel\Option\Option;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    protected function setUp(): void
    {
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension must be loaded before use.');
        }
    }

    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // caches
        $manager = $container->make('caches');
        $filePath = __DIR__.'/cache/hello.php';
        $this->assertFileDoesNotExist($filePath);
        $manager->set('hello', 'world');
        $this->assertFileExists($filePath);
        $this->assertSame('world', $manager->get('hello'));
        $manager->delete('hello');
        $this->assertFileDoesNotExist($filePath);
        $this->assertFalse($manager->get('hello'));
        Helper::deleteDirectory(__DIR__.'/cache');
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
        $this->assertFileDoesNotExist($filePath);
        $file->set('hello', 'world');
        $this->assertFileExists($filePath);
        $file->delete('hello');
        $this->assertFileDoesNotExist($filePath);
        $this->assertFalse($file->get('hello'));
        Helper::deleteDirectory(__DIR__.'/cache');
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

    protected function createContainer(): Container
    {
        $container = new Container();

        $option = new Option([
            'cache' => [
                'default'     => 'file',
                'expire'      => 86400,
                'connect'     => [
                    'file' => [
                        'driver'    => 'file',
                        'path'      => __DIR__.'/cache',
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

        return $container;
    }
}
