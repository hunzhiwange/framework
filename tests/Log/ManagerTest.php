<?php

declare(strict_types=1);

namespace Tests\Log;

use Leevel\Config\Config;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Filesystem\Helper;
use Leevel\Log\ILog;
use Leevel\Log\Manager;
use Monolog\Logger;
use Tests\TestCase;

final class ManagerTest extends TestCase
{
    public function testBaseUse(): void
    {
        $manager = $this->createManager();
        $filePath = __DIR__.'/cache/development.info/'.ILog::DEFAULT_MESSAGE_CATEGORY.'-'.date('Y-m-d').'.log';
        static::assertFileDoesNotExist($filePath);
        $manager->info('foo', ['bar']);
        static::assertFileExists($filePath);
        Helper::deleteDirectory(__DIR__.'/cache');
    }

    public function testSyslog(): void
    {
        $manager = $this->createManager();
        $syslog = $manager->connect('syslog');
        $syslog->info('foo', ['bar']);
    }

    public function test1(): void
    {
        $manager = $this->createManager();
        $syslog = $manager->reconnect('syslog');
        $syslog->info('foo', ['bar']);
    }

    public function testMonolog(): void
    {
        $manager = $this->createManager();
        $manager->setDefaultConnect('syslog');
        $this->assertInstanceof(Container::class, $container = $manager->container());
        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Logger::class, $manager->getMonolog());
    }

    protected function createManager(): Manager
    {
        $container = new Container();
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
        static::assertNull($eventDispatch->handle('event'));
        $container->singleton('event', $eventDispatch);

        return $manager;
    }
}
