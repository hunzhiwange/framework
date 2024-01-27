<?php

declare(strict_types=1);

namespace Tests\Log\Provider;

use Leevel\Config\Config;
use Leevel\Di\Container;
use Leevel\Event\IDispatch;
use Leevel\Filesystem\Helper;
use Leevel\Log\File;
use Leevel\Log\ILog;
use Leevel\Log\Provider\Register;
use Tests\TestCase;

final class RegisterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // logs
        $manager = $container->make('logs');
        $filePath = __DIR__.'/cache/development.info/'.ILog::DEFAULT_MESSAGE_CATEGORY.'-'.date('Y-m-d').'.log';
        static::assertFileDoesNotExist($filePath);
        $manager->info('foo', ['bar']);
        static::assertFileExists($filePath);
        Helper::deleteDirectory(__DIR__.'/cache');
    }

    public function testConnect(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // log
        $file = $container->make('log');
        static::assertInstanceOf(File::class, $file);
        $filePath = __DIR__.'/cache/development.info/'.ILog::DEFAULT_MESSAGE_CATEGORY.'-'.date('Y-m-d').'.log';
        static::assertFileDoesNotExist($filePath);
        $file->info('foo', ['bar']);
        static::assertFileExists($filePath);
        Helper::deleteDirectory(__DIR__.'/cache');
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $config = new Config([
            'log' => [
                'default' => 'file',
                'levels' => [
                    'debug',
                    'info',
                    'notice',
                    'warning',
                    'error',
                    'critical',
                    'alert',
                    'emergency',
                ],
                'channel' => 'development',
                'buffer' => true,
                'buffer_size' => 100,
                'connect' => [
                    'file' => [
                        'driver' => 'file',
                        'channel' => null,
                        'name' => 'Y-m-d',
                        'path' => __DIR__.'/cache',
                        'format' => 'Y-m-d H:i:s u',
                        'file_permission' => null,
                        'use_locking' => false,
                    ],
                ],
            ],
        ]);

        $container->singleton('config', $config);

        $eventDispatch = $this->createMock(IDispatch::class);

        static::assertNull($eventDispatch->handle('event'));

        $container->singleton('event', $eventDispatch);

        return $container;
    }
}
