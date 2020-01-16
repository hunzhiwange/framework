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

namespace Tests\Di;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Tests\TestCase;

/**
 * @api(
 *     title="服务提供者",
 *     path="architecture/provider",
 *     description="
 * IOC 容器是整个框架最核心的部分，负责服务的管理和解耦。
 *
 * 服务提供者将服务注入到 IOC 容器中，通常来说服务会依赖一些配置和调用其它服务等完成组装，还有有一定复杂度。
 *
 * 我们可以为服务定义一组配套的服务提供者，可以免去配置服务的成本，开发起来很愉悦。
 * ",
 * )
 */
class ProviderTest extends TestCase
{
    /**
     * @api(
     *     title="基本使用方法",
     *     description="
     * 服务提供者通过 `register` 完成服务注册。
     *
     * **fixture 定义**
     *
     * **Tests\Di\PrividerTest**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\PrividerTest::class)]}
     * ```
     *
     * **Tests\Di\PrividerService1**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\PrividerService1::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $test = new PrividerTest($container = new Container());

        $this->assertInstanceof(IContainer::class, $test->container());
        $this->assertInstanceof(Container::class, $test->container());

        $test->register();

        $this->assertSame('world', $container->make('foo')->hello());
        $this->assertSame('world', $container->make('bar')->hello());
        $this->assertSame('world', $container->make('hello')->hello());

        $this->assertFalse($test->isDeferred());
    }

    /**
     * @api(
     *     title="延迟服务提供者",
     *     description="
     * **fixture 定义**
     *
     * **Tests\Di\PrividerTest2**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\PrividerTest2::class)]}
     * ```
     *
     * **Tests\Di\PrividerService2**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\PrividerService2::class)]}
     * ```
     * ",
     *     note="",
     * )
     */
    public function testDeferred(): void
    {
        $test = new PrividerTest2($container = new Container());

        $this->assertInstanceof(IContainer::class, $test->container());
        $this->assertInstanceof(Container::class, $test->container());

        $test->register();

        $this->assertSame('bar', $container->make('world')->foo());
        $this->assertSame('hello', $container->make('hello'));

        $container->alias($test->providers());

        $this->assertSame('bar', $container->make('hello')->foo());
        $this->assertTrue($test->isDeferred());
    }

    public function testEmptyProviders(): void
    {
        $test = new PrividerTest3($container = new Container());

        $this->assertInstanceof(IContainer::class, $test->container());
        $this->assertInstanceof(Container::class, $test->container());

        $test->register();
    }

    /**
     * @api(
     *     title="bootstrap 服务注册后的引导程序",
     *     description="",
     *     note="",
     * )
     */
    public function testBootstrap(): void
    {
        $test = new PrividerTest($container = new Container());

        $this->assertInstanceof(IContainer::class, $test->container());
        $this->assertInstanceof(Container::class, $test->container());

        if (isset($_SERVER['test.privider'])) {
            unset($_SERVER['test.privider']);
        }

        $test->bootstrap();
        $this->assertSame('bootstrap', $_SERVER['test.privider']);

        if (isset($_SERVER['test.privider'])) {
            unset($_SERVER['test.privider']);
        }
    }

    public function testMethodNotFound(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Method notFound is not exits.'
        );

        $test = new PrividerTest($container = new Container());
        $test->notFound();
    }
}

class PrividerTest extends Provider
{
    public function register(): void
    {
        $this->container->singleton('foo', function ($container) {
            return new PrividerService1($container);
        });
    }

    public function bootstrap(): void
    {
        $_SERVER['test.privider'] = 'bootstrap';
    }

    public static function providers(): array
    {
        return [
            'foo' => [
                'bar',
                'hello',
            ],
        ];
    }
}

class PrividerTest2 extends Provider
{
    public function register(): void
    {
        $this->container->singleton('world', function ($container) {
            return new PrividerService2();
        });
    }

    public static function providers(): array
    {
        return [
            'world' => 'hello',
        ];
    }

    public static function isDeferred(): bool
    {
        return true;
    }
}

class PrividerTest3 extends Provider
{
    public function register(): void
    {
    }
}

class PrividerService1
{
    public function __construct(IContainer $container)
    {
    }

    public function hello()
    {
        return 'world';
    }
}

class PrividerService2
{
    public function foo()
    {
        return 'bar';
    }
}
