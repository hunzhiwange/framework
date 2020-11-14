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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Log;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Event\IDispatch;
use Leevel\Filesystem\Helper;
use Leevel\Log\ILog;
use Leevel\Log\Manager;
use Leevel\Option\Option;
use Monolog\Logger;
use Tests\TestCase;

class ManagerTest extends TestCase
{
    public function testBaseUse(): void
    {
        $manager = $this->createManager();
        $manager->info('foo', ['bar']);

        $filePath = __DIR__.'/cache/development.info/'.date('Y-m-d').'.log';
        $this->assertFileDoesNotExist($filePath);

        $manager->flush();
        $this->assertFileExists($filePath);

        Helper::deleteDirectory(__DIR__.'/cache');
    }

    public function testSyslog(): void
    {
        $manager = $this->createManager();

        $syslog = $manager->connect('syslog');

        $syslog->info('foo', ['bar']);

        $this->assertNull($syslog->flush());
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
                    'syslog' => [
                        'driver'   => 'syslog',
                        'channel'  => null,
                        'facility' => LOG_USER,
                        'level'    => ILog::DEBUG,
                        'format'   => 'Y-m-d H:i:s u',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);
        $eventDispatch = $this->createMock(IDispatch::class);
        $this->assertNull($eventDispatch->handle('event'));
        $container->singleton('event', $eventDispatch);

        return $manager;
    }
}
