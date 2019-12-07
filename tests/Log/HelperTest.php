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
use Leevel\Log\Helper;
use Leevel\Log\ILog;
use Leevel\Log\Manager;
use Tests\Log\Fixtures\Manager1;
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

    public function testLog(): void
    {
        $connect = $this->createMock(ILog::class);
        $this->assertNull($connect->log(ILog::INFO, 'bar', []));

        $container = $this->createContainer();
        $log = new Manager1($container);
        Manager1::setConnect($connect);
        $container->singleton('logs', function () use ($log): Manager {
            return $log;
        });

        $this->assertInstanceof(Manager::class, f('Leevel\\Log\\Helper\\log'));
        $this->assertNull(f('Leevel\\Log\\Helper\\record', 'bar', [], ILog::INFO));
    }

    public function testLogHelper(): void
    {
        $connect = $this->createMock(ILog::class);
        $this->assertNull($connect->log(ILog::INFO, 'bar', []));

        $container = $this->createContainer();
        $log = new Manager1($container);
        Manager1::setConnect($connect);
        $container->singleton('logs', function () use ($log): Manager {
            return $log;
        });

        $this->assertInstanceof(Manager::class, Helper::log());
        $this->assertNull(Helper::record('bar', [], ILog::INFO));
    }

    public function testHelperNotFound(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage(
            'Call to undefined function Leevel\\Log\\Helper\\not_found()'
        );

        $this->assertFalse(Helper::notFound());
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
