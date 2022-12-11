<?php

declare(strict_types=1);

namespace Tests\Log\Provider;

use Leevel\Di\Container;
use Leevel\Event\IDispatch;
use Leevel\Filesystem\Helper;
use Leevel\Log\File;
use Leevel\Log\ILog;
use Leevel\Log\Provider\Register;
use Leevel\Option\Option;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // logs
        $manager = $container->make('logs');
        $manager->info('foo', ['bar']);
        $filePath = __DIR__.'/cache/development.info/'.ILog::DEFAULT_MESSAGE_CATEGORY.'-'.date('Y-m-d').'.log';
        $this->assertFileDoesNotExist($filePath);
        $manager->flush();
        $this->assertFileExists($filePath);
        Helper::deleteDirectory(__DIR__.'/cache');
    }

    public function testConnect(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // log
        $file = $container->make('log');
        $this->assertInstanceOf(File::class, $file);
        $file->info('foo', ['bar']);
        $filePath = __DIR__.'/cache/development.info/'.ILog::DEFAULT_MESSAGE_CATEGORY.'-'.date('Y-m-d').'.log';
        $this->assertFileDoesNotExist($filePath);
        $file->flush();
        $this->assertFileExists($filePath);
        Helper::deleteDirectory(__DIR__.'/cache');
    }

    protected function createContainer(): Container
    {
        $container = new Container();

        $option = new Option([
            'log' => [
                'default'  => 'file',
                'levels'   => [
                    'debug',
                    'info',
                    'notice',
                    'warning',
                    'error',
                    'critical',
                    'alert',
                    'emergency',
                ],
                'channel'     => 'development',
                'buffer'      => true,
                'buffer_size' => 100,
                'connect'     => [
                    'file' => [
                        'driver'          => 'file',
                        'channel'         => null,
                        'name'            => 'Y-m-d',
                        'path'            => __DIR__.'/cache',
                        'format'          => 'Y-m-d H:i:s u',
                        'file_permission' => null,
                        'use_locking'     => false,
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        $eventDispatch = $this->createMock(IDispatch::class);

        $this->assertNull($eventDispatch->handle('event'));

        $container->singleton('event', $eventDispatch);

        return $container;
    }
}
