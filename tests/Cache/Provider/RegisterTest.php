<?php

declare(strict_types=1);

namespace Tests\Cache\Provider;

use Leevel\Cache\File;
use Leevel\Cache\Provider\Register;
use Leevel\Cache\Redis;
use Leevel\Config\Config;
use Leevel\Di\Container;
use Leevel\Filesystem\Helper\DeleteDirectory;
use Tests\TestCase;

final class RegisterTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        if (!\extension_loaded('redis')) {
            self::markTestSkipped('Redis extension must be loaded before use.');
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
        self::assertFileDoesNotExist($filePath);
        $manager->set('hello', 'world');
        self::assertFileExists($filePath);
        self::assertSame('world', $manager->get('hello'));
        $manager->delete('hello');
        self::assertFileDoesNotExist($filePath);
        self::assertFalse($manager->get('hello'));
        DeleteDirectory::handle(__DIR__.'/cache');
    }

    public function testCache(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // cache
        $filePath = __DIR__.'/cache/hello.php';
        $file = $container->make('cache');
        self::assertInstanceOf(File::class, $file);
        self::assertFileDoesNotExist($filePath);
        $file->set('hello', 'world');
        self::assertFileExists($filePath);
        $file->delete('hello');
        self::assertFileDoesNotExist($filePath);
        self::assertFalse($file->get('hello'));
        DeleteDirectory::handle(__DIR__.'/cache');
    }

    public function testRedis(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        self::assertInstanceOf(Redis::class, $container->make('caches')->connect('redis'));
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $config = new Config([
            'cache' => [
                'default' => 'file',
                'expire' => 86400,
                'connect' => [
                    'file' => [
                        'driver' => 'file',
                        'path' => __DIR__.'/cache',
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

        return $container;
    }
}
