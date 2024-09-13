<?php

declare(strict_types=1);

namespace Tests\Log\Proxy;

use Leevel\Config\Config;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Filesystem\Helper;
use Leevel\Log\ILog;
use Leevel\Log\Manager;
use Leevel\Log\Proxy\Log;
use Tests\TestCase;

final class LogTest extends TestCase
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
        $container->singleton('logs', static function () use ($manager): Manager {
            return $manager;
        });

        $filePath = __DIR__.'/cache/development.info/'.ILog::DEFAULT_MESSAGE_CATEGORY.'-'.date('Y-m-d').'.log';
        self::assertFileDoesNotExist($filePath);
        $manager->info('foo', ['bar']);
        self::assertFileExists($filePath);
        Helper::deleteDirectory(__DIR__.'/cache');
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $manager = $this->createManager($container);
        $container->singleton('logs', static function () use ($manager): Manager {
            return $manager;
        });

        $filePath = __DIR__.'/cache/development.info/'.ILog::DEFAULT_MESSAGE_CATEGORY.'-'.date('Y-m-d').'.log';
        self::assertFileDoesNotExist($filePath);
        Log::proxy()->info('foo', ['bar']);
        self::assertFileExists($filePath);
        Helper::deleteDirectory(__DIR__.'/cache');
    }

    protected function createManager(Container $container): Manager
    {
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $config = new Config([
            'log' => [
                'default' => 'file',
                'level' => [
                    ILog::DEFAULT_MESSAGE_CATEGORY => 'debug',
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
                    'syslog' => [
                        'driver' => 'syslog',
                        'channel' => null,
                        'facility' => LOG_USER,
                        'format' => 'Y-m-d H:i:s u',
                    ],
                ],
            ],
        ]);

        $container->singleton('config', $config);
        $eventDispatch = $this->createMock(IDispatch::class);
        self::assertNull($eventDispatch->handle('event'));
        $container->singleton('event', $eventDispatch);

        return $manager;
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
