<?php

declare(strict_types=1);

namespace Tests\Manager;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Manager\Manager;
use Leevel\Option\Option;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="Manager",
 *     path="architecture/manager",
 *     zh-CN:description="
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
     *     zh-CN:title="基础使用方法",
     *     zh-CN:description="
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
     *     zh-CN:note="",
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
     *     zh-CN:title="extend 扩展自定义连接",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Manager\FooExtend**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Manager\FooExtend::class)]}
     * ```
     * ",
     *     zh-CN:note="如果驱动存在则会替换，否则新增驱动。",
     * )
     */
    public function testExtend(): void
    {
        $manager = $this->createManager();

        $foo = $manager->connect('foo');
        $this->assertSame('hello foo', $foo->foo());
        $this->assertSame('hello foo bar', $foo->bar('bar'));

        $manager->extend('foo', function (Manager $manager): FooExtend {
            return new FooExtend($manager->normalizeConnectOption('foo'));
        });

        $manager->disconnect('foo');
        $foo = $manager->connect('foo');
        $this->assertSame('hello extend foo', $foo->foo());
        $this->assertSame('hello extend foo bar', $foo->bar('bar'));
    }

    /**
     * @api(
     *     zh-CN:title="connect 连接并返回连接对象支持缓存",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="reconnect 重新连接",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="disconnect 删除连接",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="manager 默认连接调用",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="getConnects 取回所有连接",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
     *     zh-CN:title="setDefaultConnect 设置默认驱动",
     *     zh-CN:description="",
     *     zh-CN:note="",
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
        $manager->setDefaultConnect('bar');

        $this->assertSame('hello bar', $manager->foo());
        $this->assertSame('hello bar bar', $manager->bar('bar'));
        $this->assertSame('hello bar 1', $manager->bar('1'));
        $this->assertSame('hello bar 2', $manager->bar('2'));
    }

    public function testParseOptionParamConnectIsNotArray(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Connection notarray option is not an array'
        );

        $manager = $this->createManager();
        $manager->connect('notarray');
    }

    public function testConnectNotFoundException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Connection notFound option is not an array'
        );

        $manager = $this->createManager();
        $manager->setDefaultConnect('notFound');
        $manager->foo();
    }

    public function testConnectDriverNotFoundException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Connection notFoundConnectDriver driver is not set.'
        );

        $manager = $this->createManager();
        $manager->setDefaultConnect('notFoundConnectDriver');
        $manager->foo();
    }

    public function testConnectDriverIsInvalidException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Connection driverInvalid driver `invalid` is invalid.'
        );

        $manager = $this->createManager();
        $manager->setDefaultConnect('driverInvalid');
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
                    'notarray'              => null,
                    'notFoundConnectDriver' => [],
                    'driverInvalid'         => [
                        'driver' => 'invalid',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);

        return $manager;
    }
}

class Test1 extends Manager
{
    protected function getOptionNamespace(): string
    {
        return 'test1';
    }

    protected function makeConnectFoo(): Foo
    {
        return new Foo($this->normalizeConnectOption('foo'));
    }

    protected function makeConnectBar($options = []): Bar
    {
        return new Bar($this->normalizeConnectOption('bar'));
    }

    protected function getConnectOption(string $connect): array
    {
        return $this->filterNullOfOption(parent::getConnectOption($connect));
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
