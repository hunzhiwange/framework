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

namespace Tests\Manager;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Manager\Manager;
use Leevel\Option\Option;
use Tests\TestCase;

/**
 * @api(
 *     title="Manager",
 *     path="architecture/manager",
 *     description="
 * QueryPHP 为驱动类组件统一抽象了一个基础管理类 `\Leevel\Manager\Manager`，驱动类组件可以轻松接入。
 *
 * 系统一些关键服务，比如说日志、邮件、数据库等驱动类组件均接入了统一的抽象层。
 * ",
 * )
 */
class ManagerTest extends TestCase
{
    /**
     * @api(
     *     title="基础使用方法",
     *     description="
     * **fixture 定义**
     *
     * **Tests\Manager\Test1**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Manager\Test1::class)]}
     * ```
     *
     * **Tests\Manager\IConnect**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Manager\IConnect::class)]}
     * ```
     *
     * **Tests\Manager\Foo**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Manager\Foo::class)]}
     * ```
     *
     * **Tests\Manager\Bar**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Manager\Bar::class)]}
     * ```
     *
     * 可以通过 `connect` 方法连接并返回连接对象，然后可以执行相应的操作。
     * ",
     *     note="",
     * )
     */
    public function testBaseUse(): void
    {
        $manager = $this->createManager();

        $foo = $manager->connect('foo');
        $bar = $manager->connect('bar');

        $this->assertSame([
            'driver'  => 'foo',
            'option1' => 'world',
            'null1'   => null,
        ], $foo->option());
        $this->assertSame('hello foo', $foo->foo());
        $this->assertSame('hello foo bar', $foo->bar('bar'));
        $this->assertSame('hello foo 1', $foo->bar('1'));
        $this->assertSame('hello foo 2', $foo->bar('2'));

        $this->assertSame([
            'driver'  => 'bar',
            'option1' => 'foo',
            'option2' => 'bar',
        ], $bar->option());
        $this->assertSame('hello bar', $bar->foo());
        $this->assertSame('hello bar bar', $bar->bar('bar'));
        $this->assertSame('hello bar 1', $bar->bar('1'));
        $this->assertSame('hello bar 2', $bar->bar('2'));
    }

    /**
     * @api(
     *     title="extend 扩展自定义连接",
     *     description="
     * **fixture 定义**
     *
     * **Tests\Manager\FooExtend**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Manager\FooExtend::class)]}
     * ```
     * ",
     *     note="如果驱动存在则会替换，否则新增驱动。",
     * )
     */
    public function testExtend(): void
    {
        $manager = $this->createManager();

        $foo = $manager->connect('foo');
        $this->assertSame('hello foo', $foo->foo());
        $this->assertSame('hello foo bar', $foo->bar('bar'));

        $manager->extend('foo', function (array $options, Manager $manager): FooExtend {
            $options = $manager->normalizeConnectOption('foo', $options);

            return new FooExtend($options);
        });

        $manager->disconnect('foo');
        $foo = $manager->connect('foo');
        $this->assertSame('hello extend foo', $foo->foo());
        $this->assertSame('hello extend foo bar', $foo->bar('bar'));
    }

    /**
     * @api(
     *     title="connect 连接并返回连接对象支持缓存",
     *     description="",
     *     note="",
     * )
     */
    public function testConnectCache(): void
    {
        $manager = $this->createManager();

        $foo = $manager->connect('foo');
        $bar = $manager->connect('bar');
        $foo2 = $manager->connect('foo');
        $bar2 = $manager->connect('bar');

        $this->assertSame($foo, $foo2);
        $this->assertSame($bar, $bar2);
    }

    /**
     * @api(
     *     title="reconnect 重新连接",
     *     description="",
     *     note="",
     * )
     */
    public function testReconnect(): void
    {
        $manager = $this->createManager();

        $foo = $manager->connect('foo');
        $bar = $manager->connect('bar');
        $foo2 = $manager->reconnect('foo');
        $bar2 = $manager->reconnect('bar');

        $this->assertFalse($foo === $foo2);
        $this->assertFalse($bar === $bar2);
    }

    /**
     * @api(
     *     title="disconnect 删除连接",
     *     description="",
     *     note="",
     * )
     */
    public function testDisconnect(): void
    {
        $manager = $this->createManager();

        $foo = $manager->connect('foo');
        $bar = $manager->connect('bar');

        $manager->disconnect('foo');
        $manager->disconnect('bar');

        $foo2 = $manager->connect('foo');
        $bar2 = $manager->connect('bar');

        $this->assertFalse($foo === $foo2);
        $this->assertFalse($bar === $bar2);
    }

    /**
     * @api(
     *     title="manager 默认连接调用",
     *     description="",
     *     note="",
     * )
     */
    public function testCallWithDefaultDriver(): void
    {
        $manager = $this->createManager();

        $this->assertSame('hello foo', $manager->foo());
        $this->assertSame('hello foo bar', $manager->bar('bar'));
        $this->assertSame('hello foo 1', $manager->bar('1'));
        $this->assertSame('hello foo 2', $manager->bar('2'));
    }

    /**
     * @api(
     *     title="getConnects 取回所有连接",
     *     description="",
     *     note="",
     * )
     */
    public function testGetConnects(): void
    {
        $manager = $this->createManager();
        $this->assertCount(0, $manager->getConnects());

        $manager->connect('foo');
        $manager->connect('bar');
        $this->assertCount(2, $manager->getConnects());

        $manager->disconnect('foo');
        $this->assertCount(1, $manager->getConnects());

        $manager->disconnect('bar');
        $this->assertCount(0, $manager->getConnects());
    }

    /**
     * @api(
     *     title="setDefaultDriver 设置默认驱动",
     *     description="",
     *     note="",
     * )
     */
    public function testSetDefaultDriver(): void
    {
        $manager = $this->createManager();

        $this->assertSame('hello foo', $manager->foo());
        $this->assertSame('hello foo bar', $manager->bar('bar'));
        $this->assertSame('hello foo 1', $manager->bar('1'));
        $this->assertSame('hello foo 2', $manager->bar('2'));

        $manager->disconnect();
        $manager->setDefaultDriver('bar');

        $this->assertSame('hello bar', $manager->foo());
        $this->assertSame('hello bar bar', $manager->bar('bar'));
        $this->assertSame('hello bar 1', $manager->bar('1'));
        $this->assertSame('hello bar 2', $manager->bar('2'));
    }

    public function testParseOptionParamConnectIsNotArray(): void
    {
        $manager = $this->createManager();

        // if not then default
        $notArray = $manager->connect('notarray');

        $this->assertSame('hello foo', $notArray->foo());
        $this->assertSame('hello foo bar', $notArray->bar('bar'));
        $this->assertSame('hello foo 1', $notArray->bar('1'));
        $this->assertSame('hello foo 2', $notArray->bar('2'));
    }

    public function testDriverNotFoundException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage(
            'Connect driver notFound not exits.'
        );

        $manager = $this->createManager();
        $manager->setDefaultDriver('notFound');
        $manager->foo();
    }

    public function testDriverConnectNotFoundException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Connect `notFoundConnect` of `Tests\\Manager\\Test1` is invalid.'
        );

        $manager = $this->createManager();
        $manager->setDefaultDriver('notFoundConnect');
        $manager->foo();
    }

    protected function createManager(): Test1
    {
        $container = new Container();
        $manager = new Test1($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'test1' => [
                'default' => 'foo',
                'connect' => [
                    'foo' => [
                        'driver'  => 'foo',
                        'option1' => 'hello',
                        'option1' => 'world',
                        'null1'   => null,
                    ],
                    'bar' => [
                        'driver'  => 'bar',
                        'option1' => 'foo',
                        'option2' => 'bar',
                    ],
                    'notarray'        => null,
                    'notFoundConnect' => [],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        return $manager;
    }
}

class Test1 extends Manager
{
    protected function normalizeOptionNamespace(): string
    {
        return 'test1';
    }

    protected function makeConnectFoo($options = []): Foo
    {
        $options = $this->normalizeConnectOption('foo', $options);

        return new Foo($options);
    }

    protected function makeConnectBar($options = []): Bar
    {
        $options = $this->normalizeConnectOption('bar', $options);

        return new Bar($options);
    }

    protected function getConnectOption(string $connect): array
    {
        return $this->filterNullOfOption(
            parent::getConnectOption($connect)
        );
    }
}

interface IConnect
{
    public function option(): array;

    public function foo(): string;

    public function bar(string $arg1): string;
}

class Foo implements IConnect
{
    protected $option = [];

    public function __construct(array $option)
    {
        $this->option = $option;
    }

    public function option(): array
    {
        return $this->option;
    }

    public function foo(): string
    {
        return 'hello foo';
    }

    public function bar(string $arg1): string
    {
        return 'hello foo '.$arg1;
    }
}

class Bar implements IConnect
{
    protected $option = [];

    public function __construct(array $option)
    {
        $this->option = $option;
    }

    public function option(): array
    {
        return $this->option;
    }

    public function foo(): string
    {
        return 'hello bar';
    }

    public function bar(string $arg1): string
    {
        return 'hello bar '.$arg1;
    }
}

class FooExtend implements IConnect
{
    protected $option = [];

    public function __construct(array $option)
    {
        $this->option = $option;
    }

    public function option(): array
    {
        return $this->option;
    }

    public function foo(): string
    {
        return 'hello extend foo';
    }

    public function bar(string $arg1): string
    {
        return 'hello extend foo '.$arg1;
    }
}
