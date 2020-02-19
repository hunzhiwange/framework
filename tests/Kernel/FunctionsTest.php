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

namespace Tests\Kernel;

use App as Apps;
use Leevel;
use Leevel\Di\Container;
use Leevel\Kernel\App;
use Tests\TestCase;

/**
 * @api(
 *     title="内核助手函数",
 *     path="architecture/functions",
 *     description="
 * QueryPHP 在内核助手函数中为代理应用 `\Leevel\Kernel\Proxy\App` 提供了两个别名类 `\App` 和 `\Leevel`，提供简洁的静态访问入口。
 *
 * 例外还提供了一个语言包函数 `__`，为应用提供国际化支持。
 * ",
 *     note="",
 * )
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

    /**
     * @api(
     *     title="Leevel 应用静态代理别名类调用应用",
     *     description="",
     *     note="",
     * )
     */
    public function testLeevel(): void
    {
        $this->createContainer();
        $this->assertSame('/runtime', Leevel::runtimePath());
    }

    /**
     * @api(
     *     title="App 应用静态代理别名类调用应用",
     *     description="",
     *     note="",
     * )
     */
    public function testApp(): void
    {
        $this->createContainer();
        $this->assertSame('/runtime', Apps::runtimePath());
    }

    /**
     * @api(
     *     title="Leevel 应用静态代理别名类调用 IOC 容器",
     *     description="",
     *     note="",
     * )
     */
    public function testLeevelWithContainerMethod(): void
    {
        $this->createContainer();
        $this->assertSame('foo', Leevel::make('foo'));
    }

    /**
     * @api(
     *     title="App 应用静态代理别名类调用 IOC 容器",
     *     description="",
     *     note="",
     * )
     */
    public function testAppWithContainerMethod(): void
    {
        $this->createContainer();
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
