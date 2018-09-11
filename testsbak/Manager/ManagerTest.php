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
    public function testBaseUse()
    {
        $manager = $this->createManager();

        $foo = $manager->connect('foo');
        $bar = $manager->connect('bar');

        $this->assertInstanceof(IBag::class, $foo);
        $this->assertInstanceof(Bag::class, $foo);

        $this->assertInstanceof(IBag::class, $bar);
        $this->assertInstanceof(Bag::class, $bar);

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

    public function testConnectCache()
    {
        $manager = $this->createManager();

        $foo = $manager->connect('foo');
        $bar = $manager->connect('bar');

        $this->assertInstanceof(IBag::class, $foo);
        $this->assertInstanceof(Bag::class, $foo);

        $this->assertInstanceof(IBag::class, $bar);
        $this->assertInstanceof(Bag::class, $bar);

        $foo2 = $manager->connect('foo');
        $bar2 = $manager->connect('bar');

        $this->assertInstanceof(IBag::class, $foo2);
        $this->assertInstanceof(Bag::class, $foo2);

        $this->assertInstanceof(IBag::class, $bar2);
        $this->assertInstanceof(Bag::class, $bar2);

        $this->assertSame($foo, $foo2);
        $this->assertSame($bar, $bar2);
    }

    public function testReconnect()
    {
        $manager = $this->createManager();

        $foo = $manager->connect('foo');
        $bar = $manager->connect('bar');

        $this->assertInstanceof(IBag::class, $foo);
        $this->assertInstanceof(Bag::class, $foo);

        $this->assertInstanceof(IBag::class, $bar);
        $this->assertInstanceof(Bag::class, $bar);

        $foo2 = $manager->reconnect('foo');
        $bar2 = $manager->reconnect('bar');

        $this->assertInstanceof(IBag::class, $foo2);
        $this->assertInstanceof(Bag::class, $foo2);

        $this->assertInstanceof(IBag::class, $bar2);
        $this->assertInstanceof(Bag::class, $bar2);

        $this->assertFalse($foo === $foo2);
        $this->assertFalse($bar === $bar2);
    }

    public function testDisconnect()
    {
        $manager = $this->createManager();

        $foo = $manager->connect('foo');
        $bar = $manager->connect('bar');

        $this->assertInstanceof(IBag::class, $foo);
        $this->assertInstanceof(Bag::class, $foo);

        $this->assertInstanceof(IBag::class, $bar);
        $this->assertInstanceof(Bag::class, $bar);

        $manager->disconnect('foo');
        $manager->disconnect('bar');

        $foo2 = $manager->connect('foo');
        $bar2 = $manager->connect('bar');

        $this->assertInstanceof(IBag::class, $foo2);
        $this->assertInstanceof(Bag::class, $foo2);

        $this->assertInstanceof(IBag::class, $bar2);
        $this->assertInstanceof(Bag::class, $bar2);

        $this->assertFalse($foo === $foo2);
        $this->assertFalse($bar === $bar2);
    }

    public function testStaticWithDefaultDriver()
    {
        $manager = $this->createManager();

        $this->assertSame('hello foo', $manager->foo());
        $this->assertSame('hello foo bar', $manager->bar('bar'));
        $this->assertSame('hello foo 1', $manager->bar('1'));
        $this->assertSame('hello foo 2', $manager->bar('2'));
    }

    public function testGetConnects()
    {
        $manager = $this->createManager();

        $this->assertSame(0, count($manager->getConnects()));

        $manager->connect('foo');
        $manager->connect('bar');

        $this->assertSame(2, count($manager->getConnects()));

        $manager->disconnect('foo');

        $this->assertSame(1, count($manager->getConnects()));

        $manager->disconnect('bar');

        $this->assertSame(0, count($manager->getConnects()));
    }

    public function testSetDefaultDriver()
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

    public function testParseOptionParameterConnectIsNotArray()
    {
        $manager = $this->createManager();

        // if not then default
        $notArray = $manager->connect('notarray');

        $this->assertSame('hello foo', $manager->foo());
        $this->assertSame('hello foo bar', $manager->bar('bar'));
        $this->assertSame('hello foo 1', $manager->bar('1'));
        $this->assertSame('hello foo 2', $manager->bar('2'));
    }

    public function testDriverNotFoundException()
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
    protected function normalizeOptionNamespace()
    {
        return 'test1';
    }

    protected function createConnect($connect)
    {
        return new Bag($connect);
    }

    protected function makeConnectFoo($options = [])
    {
        return new Foo(
            $this->normalizeConnectOption('foo')
        );
    }

    protected function makeConnectBar($options = [])
    {
        return new Bar(
            $this->normalizeConnectOption('bar', $options)
        );
    }

    protected function getConnectOption($connect)
    {
        return $this->filterNullOfOption(
            parent::getConnectOption($connect)
        );
    }
}

interface IBag
{
}

class Bag implements IBag
{
    protected $connect;

    public function __construct(IConnect $connect)
    {
        $this->connect = $connect;
    }

    public function __call(string $method, array $args)
    {
        return $this->connect->{$method}(...$args);
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
