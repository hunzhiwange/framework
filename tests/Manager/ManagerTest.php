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

namespace Tests\Manager;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Manager\Manager;
use Leevel\Option\Option;
use Tests\TestCase;

/**
 * manager test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.22
 *
 * @version 1.0
 */
class ManagerTest extends TestCase
{
    public function testBaseUse(): void
    {
        $manager = $this->createManager();

        $foo = $manager->connect('foo');
        $bar = $manager->connect('bar');

        $this->assertSame(['driver' => 'foo', 'option1' => 'world'], $foo->option());
        $this->assertSame('hello foo', $foo->foo());
        $this->assertSame('hello foo bar', $foo->bar('bar'));
        $this->assertSame('hello foo 1', $foo->bar('1'));
        $this->assertSame('hello foo 2', $foo->bar('2'));

        $this->assertSame(['driver' => 'bar', 'option1' => 'foo', 'option2' => 'bar'], $bar->option());
        $this->assertSame('hello bar', $bar->foo());
        $this->assertSame('hello bar bar', $bar->bar('bar'));
        $this->assertSame('hello bar 1', $bar->bar('1'));
        $this->assertSame('hello bar 2', $bar->bar('2'));
    }

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

    public function testStaticWithDefaultDriver(): void
    {
        $manager = $this->createManager();

        $this->assertSame('hello foo', $manager->foo());
        $this->assertSame('hello foo bar', $manager->bar('bar'));
        $this->assertSame('hello foo 1', $manager->bar('1'));
        $this->assertSame('hello foo 2', $manager->bar('2'));
    }

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

    public function testParseOptionParameterConnectIsNotArray(): void
    {
        $manager = $this->createManager();

        // if not then default
        $notArray = $manager->connect('notarray');

        $this->assertSame('hello foo', $manager->foo());
        $this->assertSame('hello foo bar', $manager->bar('bar'));
        $this->assertSame('hello foo 1', $manager->bar('1'));
        $this->assertSame('hello foo 2', $manager->bar('2'));
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

    protected function createManager()
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
                    'notarray' => null,
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
        return new Foo(
            $this->normalizeConnectOption('foo')
        );
    }

    protected function makeConnectBar($options = []): Bar
    {
        return new Bar(
            $this->normalizeConnectOption('bar', $options)
        );
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
