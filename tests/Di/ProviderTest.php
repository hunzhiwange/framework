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

namespace Tests\Di;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Tests\TestCase;

/**
 * provider test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.14
 *
 * @version 1.0
 */
class ProviderTest extends TestCase
{
    public function testBaseUse()
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

    public function testDeferred()
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

    public function testEmptyProviders()
    {
        $test = new PrividerTest3($container = new Container());

        $this->assertInstanceof(IContainer::class, $test->container());
        $this->assertInstanceof(Container::class, $test->container());

        $test->register();
    }

    public function testNotDefinedBootstrap()
    {
        $test = new PrividerTest($container = new Container());

        $this->assertInstanceof(IContainer::class, $test->container());
        $this->assertInstanceof(Container::class, $test->container());

        $test->bootstrap();
    }

    public function testMethodNotFound()
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
    /**
     * 注册服务
     */
    public function register()
    {
        $this->container->singleton('foo', function ($container) {
            return new PrividerService1($container);
        });
    }

    /**
     * 可用服务提供者.
     *
     * @return array
     */
    public static function providers()
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
    /**
     * 是否延迟载入.
     *
     * @var bool
     */
    public static $defer = true;

    /**
     * 注册服务
     */
    public function register()
    {
        $this->container->singleton('world', function ($container) {
            return new PrividerService2();
        });
    }

    /**
     * 可用服务提供者.
     *
     * @return array
     */
    public static function providers()
    {
        return [
            'world' => 'hello',
        ];
    }
}

class PrividerTest3 extends Provider
{
    /**
     * 注册服务
     */
    public function register()
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
