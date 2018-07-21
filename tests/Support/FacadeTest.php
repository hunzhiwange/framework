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

namespace Tests\Support;

use Leevel\Di\Container;
use Leevel\Support\Facade;
use Tests\TestCase;

/**
 * facade test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.12
 *
 * @version 1.0
 */
class FacadeTest extends TestCase
{
    protected $container;

    protected function setUp()
    {
        $this->container = new Container();

        Facade::setContainer($this->container);
    }

    protected function tearDown()
    {
        Facade::setContainer(null);
    }

    public function testBaseUse()
    {
        $test = new Test1();

        $this->assertSame($this->container, $test->container());

        $this->container->singleton('test1', function () {
            return new Service1();
        });

        $this->assertSame('world', Test1::hello());

        // 缓存
        $this->assertSame('world', Test1::hello());
    }

    public function testServiceMethodNotFound()
    {
        $test = new Test1();

        $this->assertSame($this->container, $test->container());

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Method methodNotFound is not exits.'
        );

        Test1::methodNotFound();
    }

    public function testServiceNotFound()
    {
        $test = new Test2();

        $this->assertSame($this->container, $test->container());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Services test2 was not found in the IOC container.'
        );

        Test2::notFound();
    }

    public function testEmptyName()
    {
        $test = new Test3();

        $this->assertSame($this->container, $test->container());

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Services  was not found in the IOC container.'
        );

        Test3::notFound();
    }

    public function testRemove()
    {
        $test = new Test3();

        $this->assertSame($this->container, $test->container());
        $this->assertArrayHasKey('test1', $this->getTestProperty($test, 'instances'));
        $this->assertArrayHasKey('test2', $this->getTestProperty($test, 'instances'));
        $this->assertArrayNotHasKey('test3', $this->getTestProperty($test, 'instances'));

        Facade::remove('test1');

        $this->assertArrayNotHasKey('test1', $this->getTestProperty($test, 'instances'));
        $this->assertArrayHasKey('test2', $this->getTestProperty($test, 'instances'));
        $this->assertArrayNotHasKey('test3', $this->getTestProperty($test, 'instances'));

        Facade::remove();

        $this->assertArrayNotHasKey('test1', $this->getTestProperty($test, 'instances'));
        $this->assertArrayNotHasKey('test2', $this->getTestProperty($test, 'instances'));
        $this->assertArrayNotHasKey('test3', $this->getTestProperty($test, 'instances'));
    }
}

class Service1
{
    public function hello()
    {
        return 'world';
    }
}

class Test1 extends Facade
{
    protected static function name(): string
    {
        return 'test1';
    }
}

class Test2 extends Facade
{
    protected static function name(): string
    {
        return 'test2';
    }
}

class Test3 extends Facade
{
}
