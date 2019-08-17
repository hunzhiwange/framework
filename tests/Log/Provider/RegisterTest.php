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

namespace Tests\Log\Provider;

use Leevel\Di\Container;
use Leevel\Event\IDispatch;
use Leevel\Filesystem\Fso;
use Leevel\Log\File;
use Leevel\Log\Provider\Register;
use Leevel\Option\Option;
use Tests\TestCase;

/**
 * register test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.24
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

        // logs
        $manager = $container->make('logs');
        $manager->info('foo', ['bar']);
        $filePath = __DIR__.'/cache/development.info/'.date('Y-m-d').'.log';
        $this->assertFileNotExists($filePath);
        $manager->flush();
        $this->assertFileExists($filePath);
        Fso::deleteDirectory(__DIR__.'/cache', true);

        // log
        $file = $container->make('log');
        $this->assertInstanceOf(File::class, $file);
        $file->info('foo', ['bar']);
        $filePath = __DIR__.'/cache/development.info/'.date('Y-m-d').'.log';
        $this->assertFileNotExists($filePath);
        $file->flush();
        $this->assertFileExists($filePath);
        Fso::deleteDirectory(__DIR__.'/cache', true);
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
                        'driver'  => 'file',
                        'channel' => null,
                        'name'    => 'Y-m-d',
                        'size'    => 2097152,
                        'path'    => __DIR__.'/cache',
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
