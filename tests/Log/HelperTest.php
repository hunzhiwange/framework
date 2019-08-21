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

namespace Tests\Log;

use Leevel\Di\Container;
use Leevel\Log\Helper;
use Leevel\Log\ILog;
use Tests\TestCase;

/**
 * helper test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.08.10
 *
 * @version 1.0
 */
class HelperTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testOption(): void
    {
        $log = $this->createMock(ILog::class);
        $this->assertNull($log->log(ILog::INFO, 'bar', []));

        $container = $this->createContainer();
        $container->singleton('logs', function () use ($log) {
            return $log;
        });

        $this->assertInstanceof(ILog::class, f('Leevel\\Log\\Helper\\log'));
        $this->assertNull(f('Leevel\\Log\\Helper\\log_record', 'bar', [], ILog::INFO));
    }

    public function testOptionHelper(): void
    {
        $log = $this->createMock(ILog::class);
        $this->assertNull($log->log(ILog::INFO, 'bar', []));

        $container = $this->createContainer();
        $container->singleton('logs', function () use ($log) {
            return $log;
        });

        $this->assertInstanceof(ILog::class, Helper::log());
        $this->assertNull(Helper::logRecord('bar', [], ILog::INFO));
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
