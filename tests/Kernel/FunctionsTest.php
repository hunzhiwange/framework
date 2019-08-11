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

namespace Tests\Kernel;

use App as Apps;
use Leevel;
use Leevel\Di\Container;
use Leevel\Kernel\App;
use Tests\TestCase;

/**
 * functions test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.22
 *
 * @version 1.0
 */
class FunctionsTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testLeevel(): void
    {
        $container = $this->createContainer();
        $this->assertSame('/runtime', Leevel::runtimePath());
    }

    public function testApp(): void
    {
        $container = $this->createContainer();
        $this->assertSame('/runtime', Apps::runtimePath());
    }

    public function testLeevelWithContainerMethod(): void
    {
        $container = $this->createContainer();
        $this->assertSame('foo', Leevel::make('foo'));
    }

    public function testAppWithContainerMethod(): void
    {
        $container = $this->createContainer();
        $this->assertSame('foo', Apps::make('foo'));
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();
        $container->instance('app', new App($container, ''));

        return $container;
    }
}
