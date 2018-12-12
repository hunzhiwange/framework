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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Log;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Filesystem\Fso;
use Leevel\Log\ILog;
use Leevel\Log\Manager;
use Leevel\Option\Option;
use Monolog\Logger;
use Tests\TestCase;

/**
 * manager test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.24
 *
 * @version 1.0
 */
class ManagerTest extends TestCase
{
    public function testBaseUse()
    {
        $manager = $this->createManager();
        $manager->info('foo', ['bar']);

        $filePath = __DIR__.'/cache/development.info/'.date('Y-m-d').'.log';
        $this->assertFileNotExists($filePath);

        $manager->flush();
        $this->assertFileExists($filePath);

        Fso::deleteDirectory(__DIR__.'/cache', true);
    }

    public function testSyslog()
    {
        $manager = $this->createManager();

        $syslog = $manager->connect('syslog');

        $syslog->info('foo', ['bar']);

        $this->assertNull($syslog->flush());
    }

    public function testMonolog()
    {
        $manager = $this->createManager();

        $manager->setDefaultDriver('syslog');

        $this->assertInstanceof(Container::class, $container = $manager->container());
        $this->assertInstanceof(IContainer::class, $container);

        $this->assertTrue($manager->isMonolog());
        $this->assertInstanceof(Logger::class, $manager->getMonolog());
    }

    protected function createManager()
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
                        'driver'  => 'file',
                        'channel' => null,
                        'name'    => 'Y-m-d',
                        'size'    => 2097152,
                        'path'    => __DIR__.'/cache',
                    ],
                    'syslog' => [
                        'driver'   => 'syslog',
                        'channel'  => null,
                        'facility' => LOG_USER,
                        'level'    => ILog::DEBUG,
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        return $manager;
    }
}
