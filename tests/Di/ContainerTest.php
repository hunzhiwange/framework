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

namespace Tests\Di;

use Leevel\Di\Container;
use Leevel\Di\ICoroutine;
use stdClass;
use Tests\Di\Fixtures\ITest2;
use Tests\Di\Fixtures\ITest3;
use Tests\Di\Fixtures\ITest8;
use Tests\Di\Fixtures\ProviderTest1;
use Tests\Di\Fixtures\ProviderTest2;
use Tests\Di\Fixtures\Test1;
use Tests\Di\Fixtures\Test10;
use Tests\Di\Fixtures\Test2;
use Tests\Di\Fixtures\Test20;
use Tests\Di\Fixtures\Test21;
use Tests\Di\Fixtures\Test22;
use Tests\Di\Fixtures\Test23;
use Tests\Di\Fixtures\Test24;
use Tests\Di\Fixtures\Test25;
use Tests\Di\Fixtures\Test26;
use Tests\Di\Fixtures\Test27;
use Tests\Di\Fixtures\Test28;
use Tests\Di\Fixtures\Test3;
use Tests\Di\Fixtures\Test4;
use Tests\Di\Fixtures\Test5;
use Tests\Di\Fixtures\Test6;
use Tests\Di\Fixtures\Test7;
use Tests\Di\Fixtures\Test8;
use Tests\Di\Fixtures\Test9;
use Tests\TestCase;

/**
 * container test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.05.27
 *
 * @version 1.0
 *
 * @api(
 *     title="IOC 容器",
 *     path="architecture/ioc",
 *     description="IOC 容器是整个框架最核心的部分，负责服务的管理和解耦组件。
 *
 * 目前系统所有的关键服务都接入了 IOC 容器，包括控制器、Console 命令行。",
 * )
 */
class ContainerTest extends TestCase
{
    /**
     * @api(
     *     title="闭包绑定",
     *     description="闭包属于惰性，真正使用的时候才会执行。
     *
     * 我们可以通过 `bind` 来绑定一个闭包，通过 `make` 来运行服务，第二次运行如果是单例则直接使用生成后的结果，否则会每次执行闭包的代码。
     *
     * 通常来说，系统大部分服务都是单例来提升性能和共享。",
     *     note="",
     * )
     */
    public function testBindClosure(): void
    {
        $container = new Container();

        $container->bind('foo', function () {
            return 'bar';
        });

        $this->assertSame('bar', $container->make('foo'));
    }

    /**
     * @api(
     *     title="闭包绑定单例",
     *     description="",
     *     note="",
     * )
     */
    public function testSingletonClosure(): void
    {
        $container = new Container();

        $singleton = new stdClass();

        $container->singleton('singleton', function () use ($singleton) {
            return $singleton;
        });

        $this->assertSame($singleton, $container->make('singleton'));
        $this->assertSame($singleton, $container->make('singleton'));
    }

    /**
     * @api(
     *     title="类直接生成本身",
     *     description="一个独立的类可以直接生成，而不需要提前注册到容器中。",
     *     note="",
     * )
     */
    public function testClass(): void
    {
        $container = new Container();

        $this->assertInstanceOf(Test1::class, $container->make(Test1::class));
    }

    /**
     * @api(
     *     title="类单例",
     *     description="类也可以注册为单例。",
     *     note="",
     * )
     */
    public function testSingletonClass(): void
    {
        $container = new Container();

        $container->singleton(Test1::class);

        $this->assertSame($container->make(Test1::class), $container->make(Test1::class));
    }

    /**
     * @api(
     *     title="接口绑定",
     *     description="可以为接口绑定实现。",
     *     note="",
     * )
     */
    public function testInterface(): void
    {
        $container = new Container();

        $container->bind(ITest2::class, Test2::class);

        $this->assertInstanceOf(ITest2::class, $container->make(ITest2::class));
        $this->assertInstanceOf(ITest2::class, $container->make(Test2::class));
    }

    /**
     * @api(
     *     title="接口绑定接口作为构造器参数",
     *     description="接口可以作为控制器参数来做依赖注入。
     *
     * **ITest2 定义**
     *
     * ``` php
     * ".\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\ITest2::class)."
     * ```
     *
     * **Test2 定义**
     *
     * ``` php
     * ".\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test2::class)."
     * ```
     *
     * **Test3 定义**
     *
     * ``` php
     * ".\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test3::class)."
     * ```
     *
     * 通过 `Test3` 的构造函数注入 `ITest2` 的实现 `Test2`，通过 IOC 容器可以实现代码解耦。
     * ",
     *     note="",
     * )
     */
    public function testInterface2(): void
    {
        $container = new Container();

        $container->bind(ITest2::class, Test2::class);

        $this->assertInstanceOf(ITest2::class, $test2 = $container->make(Test3::class)->arg1);
        $this->assertInstanceOf(Test2::class, $test2);
    }

    public function testInterface3(): void
    {
        $container = new Container();

        $container->bind(ITest2::class, Test2::class);

        $test4 = $container->make(Test4::class);

        $this->assertInstanceOf(ITest3::class, $test4->arg1);
        $this->assertInstanceOf(ITest2::class, $test4->arg1->arg1);
    }

    public function testContainerAsFirstArgs(): void
    {
        $container = new Container();

        $container->bind('test', function ($container) {
            return $container;
        });

        $this->assertSame($container, $container->make('test'));
    }

    public function testArrayAccess(): void
    {
        $container = new Container();

        $container['foo'] = function () {
            return 'bar';
        };

        $this->assertTrue(isset($container['foo']));
        $this->assertSame('bar', $container['foo']);
        unset($container['foo']);
        $this->assertFalse(isset($container['foo']));
    }

    public function testAliases(): void
    {
        $container = new Container();

        $container['foo'] = 'bar';

        $container->alias('foo', 'foo2');
        $container->alias('foo', ['foo3', 'foo4']);
        $container->alias(['foo' => ['foo5', 'foo6']]);
        $container->alias(['foo' => 'foo7']);

        $this->assertSame('bar', $container->make('foo'));
        $this->assertSame('bar', $container->make('foo2'));
        $this->assertSame('bar', $container->make('foo3'));
        $this->assertSame('bar', $container->make('foo4'));
        $this->assertSame('bar', $container->make('foo5'));
        $this->assertSame('bar', $container->make('foo6'));
        $this->assertSame('bar', $container->make('foo7'));
    }

    public function testMakeWithArgs(): void
    {
        $container = new Container();

        $container['foo'] = function ($container, $arg1, $arg2) {
            return [
                $arg1,
                $arg2,
            ];
        };

        $this->assertSame([1, 2], $container->make('foo', [1, 2, 3]));
    }

    public function testOverridden(): void
    {
        $container = new Container();

        $container['foo'] = 'bar';
        $this->assertSame('bar', $container['foo']);

        $container['foo'] = 'bar2';
        $this->assertSame('bar2', $container['foo']);
    }

    public function testInstance(): void
    {
        $container = new Container();

        $instance = new stdClass();

        $container->instance('foo', $instance);

        $this->assertSame($instance, $container->make('foo'));
    }

    public function testDefaultArgs(): void
    {
        $container = new Container();

        $container->bind(ITest2::class, Test2::class);
        $container->bind('foo', Test5::class);

        $test5 = $container->make('foo');

        $this->assertInstanceOf(ITest3::class, $test5);
        $this->assertSame('hello default', $test5->arg2);
    }

    public function testUnsetInstances(): void
    {
        $container = new Container();

        $container->instance('foo', 'bar');
        $container->alias('foo', 'foo2');
        $container->alias('foo', 'foo3');

        $this->assertTrue(isset($container['foo']));
        $this->assertTrue(isset($container['foo2']));
        $this->assertTrue(isset($container['foo3']));

        unset($container['foo']);

        $this->assertFalse(isset($container['foo']));
        $this->assertFalse(isset($container['foo2']));
        $this->assertFalse(isset($container['foo3']));
    }

    public function testArgsRequiredContainerInvalidArgumentException(): void
    {
        $this->expectException(\Leevel\Di\ContainerInvalidArgumentException::class);

        $container = new Container();

        $container->make(Test6::class, []);
    }

    public function testInterfaceContainerInvalidArgumentException(): void
    {
        $this->expectException(\Leevel\Di\ContainerInvalidArgumentException::class);

        $container = new Container();

        $container->make(ITest2::class, []);
    }

    public function testCall(): void
    {
        $container = new Container();

        $result = $container->call(function (Test7 $arg1, array $arg2 = []) {
            return func_get_args();
        });

        $this->assertInstanceOf(Test7::class, $result[0]);
        $this->assertSame([], $result[1]);

        $result = $container->call(function (Test7 $arg1, array $arg2 = [], $arg3 = null) {
            return func_get_args();
        }, ['arg3' => 'hello']);

        $this->assertInstanceOf(Test7::class, $result[0]);
        $this->assertSame([], $result[1]);
        $this->assertSame('hello', $result[2]);

        $test7 = new Test7();

        $result = $container->call(function (Test7 $arg1, $arg2 = 'hello') {
            return func_get_args();
        }, [Test7::class => $test7, 'arg2' => 'hello world']);

        $this->assertSame($test7, $result[0]);
        $this->assertSame('hello world', $result[1]);

        $test8 = new Test8();

        $result = $container->call(function ($arg1, $arg2, $arg3, ITest8 $arg4 = null, Test8 $arg5 = null) {
            return func_get_args();
        }, ['arg1' => 'foo', 'arg3' => 'world2', Test8::class => $test8]);

        $this->assertSame('foo', $result[0]);
        $this->assertNull($result[1]);
        $this->assertSame('world2', $result[2]);
        $this->assertNull($result[3]);
        $this->assertSame($result[4], $test8);
    }

    public function testCallNotFoundClass(): void
    {
        $this->expectException(\ReflectionException::class);

        $container = new Container();

        $result = $container->call('Test8');
    }

    public function testCallWithArrayOrString(): void
    {
        $container = new Container();

        $result = $container->call([Test8::class, 'func1'], ['foo', 'bar']);
        $this->assertSame(['foo', 'bar'], $result);

        $result = $container->call(Test8::class.'@func1', ['foo', 'bar']);
        $this->assertSame(['foo', 'bar'], $result);

        $result = $container->call([Test8::class], ['foo', 'bar']);
        $this->assertSame(['call handle'], $result);

        $result = $container->call(Test8::class.'@', ['foo', 'bar']);
        $this->assertSame(['call handle'], $result);

        $result = $container->call(Test8::class.'@func2');
        $this->assertSame('hello', $result[0]);

        $result = $container->call(Test8::class.'@func2', ['world', 'foo', 'bar']);
        $this->assertSame('hello', $result[0]);
        $this->assertSame('world', $result[1]);
        $this->assertSame('foo', $result[2]);
        $this->assertSame('bar', $result[3]);

        $result = $container->call(Test8::class.'@func2', ['world', 'arg1' => 'foo', 'bar']);
        $this->assertSame('foo', $result[0]);
        $this->assertSame('world', $result[1]);
        $this->assertSame('bar', $result[2]);
    }

    public function testCallWithCallableArray(): void
    {
        $container = new Container();

        $test8 = new Test8();

        $result = $container->call([$test8, 'func1'], ['foo', 'bar']);

        $this->assertSame(['foo', 'bar'], $result);
    }

    public function testCallStatic(): void
    {
        $container = new Container();

        $result = $container->call(Test8::class.'::staticFunc3', ['hello', 'world']);
        $this->assertSame(['hello', 'world'], $result);
    }

    public function testRemove(): void
    {
        $container = new Container();

        $test8 = new Test8();
        $container->instance(Test8::class, $test8);
        $this->assertTrue($container->exists(Test8::class));

        $container->remove(Test8::class);
        $this->assertFalse($container->exists(Test8::class));
    }

    public function testNotInstantiable(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Class Tests\\Di\\Fixtures\\Test9 is not instantiable.'
        );

        $container = new Container();

        $this->assertSame('world9', $container->make(Test9::class)->hello());
    }

    public function testUnsupportedCallbackTypes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Unsupported callback types.'
        );

        $container = new Container();

        $container->call(false);
    }

    public function testMakeServiceBool(): void
    {
        $container = new Container();

        $container->bind('foo', false);

        $this->assertFalse($container->make('foo'));
    }

    public function testBindArrayAsAlias(): void
    {
        $container = new Container();

        $container->bind(['foo' => 'bar'], false);

        $this->assertFalse($container->make('foo'));
        $this->assertFalse($container->make('bar'));
    }

    public function testParseReflectionException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Class Tests\\Di\\Fixtures\\TestNotFound does not exist'
        );

        $container = new Container();

        $container->call([new Test10(), 'hello']);
    }

    public function testInstanceWithArray(): void
    {
        $container = new Container();

        $instance = new stdClass();

        $container->instance(['foo' => 'bar'], $instance);

        $this->assertSame($instance, $container->make('foo'));
        $this->assertSame($instance, $container->make('bar'));
    }

    public function testInstanceItSelf(): void
    {
        $container = new Container();

        $container->instance('foo');
        $this->assertSame('foo', $container->make('foo'));

        $container->instance('Leevel\\Foo\\Middleware\\Bar');
        $this->assertSame('Leevel\\Foo\\Middleware\\Bar', $container->make('Leevel\\Foo\\Middleware\\Bar'));
    }

    public function testCallWithClassArgsAndItInstance(): void
    {
        $container = new Container();

        $obj = new Test20();

        $args = [new Test21('hello'), new Test22('world')];

        $result = $container->call([$obj, 'handle'], $args);

        $this->assertSame(['test21' => 'hello', 'test22' => 'world'], $result);
    }

    public function testCallWithClassArgsAndItInstanceAndMore(): void
    {
        $container = new Container();

        $obj = new Test23();

        $args = [new Test24('hello'), new Test25('world'), 'more'];

        $result = $container->call([$obj, 'handle'], $args);

        $this->assertSame(['test24' => 'hello', 'test25' => 'world', 'three' => 'more'], $result);
    }

    public function testCoroutine(): void
    {
        $coroutine = $this->createMock(ICoroutine::class);

        $coroutine->method('context')->willReturn(true);
        $this->assertTrue($coroutine->context(Test26::class));

        $coroutine->method('uid')->willReturn(2);
        $this->assertSame(2, $coroutine->uid());

        $container = new Container();
        $container->setCoroutine($coroutine);
        $this->assertInstanceOf(ICoroutine::class, $container->getCoroutine());

        $container->instance('test', new Test26());

        $this->assertInstanceOf(Test26::class, $container->make('test'));
        $this->assertTrue($container->existsCoroutine('test'));
    }

    public function testRemoveCoroutine(): void
    {
        $coroutine = $this->createMock(ICoroutine::class);

        $coroutine->method('context')->willReturn(true);
        $this->assertTrue($coroutine->context(Test26::class));

        $coroutine->method('uid')->willReturn(2);
        $this->assertSame(2, $coroutine->uid());

        $container = new Container();
        $container->setCoroutine($coroutine);

        $container->instance('test', new Test26());

        $this->assertInstanceOf(Test26::class, $container->make('test'));
        $this->assertTrue($container->existsCoroutine('test'));

        $container->removeCoroutine('test');
        $this->assertFalse($container->existsCoroutine('test'));
    }

    public function testRemoveCoroutineAll(): void
    {
        $coroutine = $this->createMock(ICoroutine::class);

        $coroutine->method('context')->willReturn(true);
        $this->assertTrue($coroutine->context(Test26::class));

        $coroutine->method('uid')->willReturn(2);
        $this->assertSame(2, $coroutine->uid());

        $container = new Container();
        $container->setCoroutine($coroutine);

        $container->instance('test', new Test26());

        $this->assertInstanceOf(Test26::class, $container->make('test'));
        $this->assertTrue($container->existsCoroutine('test'));

        $container->removeCoroutine();
        $this->assertFalse($container->existsCoroutine('test'));
    }

    public function testCoroutineWasSingleton(): void
    {
        $coroutine = $this->createMock(ICoroutine::class);

        $coroutine->method('context')->willReturn(true);
        $this->assertTrue($coroutine->context(Test26::class));

        $coroutine->method('uid')->willReturn(2);
        $this->assertSame(2, $coroutine->uid());

        $container = new Container();
        $container->setCoroutine($coroutine);
        $this->assertInstanceOf(ICoroutine::class, $container->getCoroutine());

        $container->singleton('test', new Test26());

        $this->assertInstanceOf(Test26::class, $container->make('test'));
        $this->assertTrue($container->existsCoroutine('test'));
    }

    public function testClassArgsClassAsStringContainer(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Class or interface Tests\\Di\\Fixtures\\Test27 is register in container is not object.'
        );

        $container = new Container();

        $container->instance(Test27::class); // instance 直接的将直接返回字符串
        $container->make(Test28::class);
    }

    public function testClassArgsASingleClass(): void
    {
        $container = new Container();
        $test = $container->make(Test28::class);
        $this->assertSame('world', $test->hello());
    }

    public function testMagicGet(): void
    {
        $container = new Container();

        $container->bind('foo', 'bar');

        $this->assertSame('bar', $container->foo);
    }

    public function testMagicSet(): void
    {
        $container = new Container();

        $container->foo = 'bar';

        $this->assertSame('bar', $container->foo);
    }

    public function testMagicCallException(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Method `callNotFound` is not exits.'
        );

        $container = new Container();

        $container->callNotFound();
    }

    public function testClear(): void
    {
        $container = new Container();

        $container->instance('foo', 'bar');

        $this->assertSame('bar', $container->make('foo'));
        $this->assertSame('notfound', $container->make('notfound'));

        $container->clear();
        $this->assertSame('foo', $container->make('foo'));
    }

    public function testClone(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Ioc container disallowed clone.'
        );

        $container = new Container();
        $container2 = clone $container;
    }

    public function testMakeProvider(): void
    {
        $container = new Container();

        $container->makeProvider(ProviderTest1::class);

        $this->assertSame(1, $_SERVER['testMakeProvider']);

        unset($_SERVER['testMakeProvider']);
    }

    public function testCallProviderBootstrap(): void
    {
        $container = new Container();

        $container->callProviderBootstrap(new ProviderTest1($container));

        $this->assertSame(1, $_SERVER['testMakeProvider']);

        unset($_SERVER['testMakeProvider']);

        $container->callProviderBootstrap(new ProviderTest2($container));

        $this->assertSame(1, $_SERVER['testCallProviderBootstrap']);

        unset($_SERVER['testCallProviderBootstrap']);
    }
}
