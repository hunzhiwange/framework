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
use Tests\TestCase;

/**
 * container test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.05.27
 *
 * @version 1.0
 */
class ContainerTest extends TestCase
{
    public function testBindClosure()
    {
        $container = new Container();

        $container->bind('foo', function () {
            return 'bar';
        });

        $this->assertSame('bar', $container->make('foo'));
    }

    public function testSingletonClosure()
    {
        $container = new Container();

        $singleton = new stdClass();

        $container->singleton('singleton', function () use ($singleton) {
            return $singleton;
        });

        $this->assertSame($singleton, $container->make('singleton'));
        $this->assertSame($singleton, $container->make('singleton'));
    }

    public function testClass()
    {
        $container = new Container();

        $this->assertInstanceOf(Test1::class, $container->make(Test1::class));
    }

    public function testSingletonClass()
    {
        $container = new Container();

        $container->singleton(Test1::class);

        $this->assertSame($container->make(Test1::class), $container->make(Test1::class));
    }

    public function testInterface()
    {
        $container = new Container();

        $container->bind(ITest2::class, Test2::class);

        $this->assertInstanceOf(ITest2::class, $container->make(ITest2::class));
        $this->assertInstanceOf(ITest2::class, $container->make(Test2::class));
    }

    public function testInterface2()
    {
        $container = new Container();

        $container->bind(ITest2::class, Test2::class);

        $this->assertInstanceOf(ITest2::class, $container->make(Test3::class)->arg1);
    }

    public function testInterface3()
    {
        $container = new Container();

        $container->bind(ITest2::class, Test2::class);

        $test4 = $container->make(Test4::class);

        $this->assertInstanceOf(ITest3::class, $test4->arg1);
        $this->assertInstanceOf(ITest2::class, $test4->arg1->arg1);
    }

    public function testContainerAsFirstArgs()
    {
        $container = new Container();

        $container->bind('test', function ($container) {
            return $container;
        });

        $this->assertSame($container, $container->make('test'));
    }

    public function testArrayAccess()
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

    public function testAliases()
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

    public function testMakeWithArgs()
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

    public function testOverridden()
    {
        $container = new Container();

        $container['foo'] = 'bar';
        $this->assertSame('bar', $container['foo']);

        $container['foo'] = 'bar2';
        $this->assertSame('bar2', $container['foo']);
    }

    public function testInstance()
    {
        $container = new Container();

        $instance = new stdClass();

        $container->instance('foo', $instance);

        $this->assertSame($instance, $container->make('foo'));
    }

    public function testDefaultArgs()
    {
        $container = new Container();

        $container->bind(ITest2::class, Test2::class);
        $container->bind('foo', Test5::class);

        $test5 = $container->make('foo');

        $this->assertInstanceOf(ITest3::class, $test5);
        $this->assertSame('hello default', $test5->arg2);
    }

    public function testUnsetInstances()
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

    public function testArgsRequiredContainerInvalidArgumentException()
    {
        $this->expectException(\Leevel\Di\ContainerInvalidArgumentException::class);

        $container = new Container();

        $container->make(Test6::class, []);
    }

    public function testInterfaceContainerInvalidArgumentException()
    {
        $this->expectException(\Leevel\Di\ContainerInvalidArgumentException::class);

        $container = new Container();

        $container->make(ITest2::class, []);
    }

    public function testCall()
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

    public function testCallNotFoundClass()
    {
        $this->expectException(\ReflectionException::class);

        $container = new Container();

        $result = $container->call('Test8');
    }

    public function testCallWithArrayOrString()
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

    public function testCallWithCallableArray()
    {
        $container = new Container();

        $test8 = new Test8();

        $result = $container->call([$test8, 'func1'], ['foo', 'bar']);

        $this->assertSame(['foo', 'bar'], $result);
    }

    public function testCallStatic()
    {
        $container = new Container();

        $result = $container->call(Test8::class.'::staticFunc3', ['hello', 'world']);
        $this->assertSame(['hello', 'world'], $result);
    }

    public function testRemove()
    {
        $container = new Container();

        $test8 = new Test8();
        $container->instance(Test8::class, $test8);
        $this->assertTrue($container->exists(Test8::class));

        $container->remove(Test8::class);
        $this->assertFalse($container->exists(Test8::class));
    }

    public function testNotInstantiable()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Class Tests\\Di\\Test9 is not instantiable.'
        );

        $container = new Container();

        $this->assertSame('world9', $container->make(Test9::class)->hello());
    }

    public function testUnsupportedCallbackTypes()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Unsupported callback types.'
        );

        $container = new Container();

        $container->call(false);
    }

    public function testMakeServiceBool()
    {
        $container = new Container();

        $container->bind('foo', false);

        $this->assertFalse($container->make('foo'));
    }

    public function testBindArrayAsAlias()
    {
        $container = new Container();

        $container->bind(['foo' => 'bar'], false);

        $this->assertFalse($container->make('foo'));
        $this->assertFalse($container->make('bar'));
    }

    public function testParseReflectionException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Class Tests\\Di\\TestNotFound does not exist'
        );

        $container = new Container();

        $container->call([new Test10(), 'hello']);
    }

    public function testInstanceWithArray()
    {
        $container = new Container();

        $instance = new stdClass();

        $container->instance(['foo' => 'bar'], $instance);

        $this->assertSame($instance, $container->make('foo'));
        $this->assertSame($instance, $container->make('bar'));
    }

    public function testInstanceItSelf()
    {
        $container = new Container();

        $container->instance('foo');
        $this->assertSame('foo', $container->make('foo'));

        $container->instance('Leevel\\Foo\\Middleware\\Bar');
        $this->assertSame('Leevel\\Foo\\Middleware\\Bar', $container->make('Leevel\\Foo\\Middleware\\Bar'));
    }

    public function testCallWithClassArgsAndItInstance()
    {
        $container = new Container();

        $obj = new Test20();

        $args = [new Test21('hello'), new Test22('world')];

        $result = $container->call([$obj, 'handle'], $args);

        $this->assertSame(['test21' => 'hello', 'test22' => 'world'], $result);
    }

    public function testCallWithClassArgsAndItInstanceAndMore()
    {
        $container = new Container();

        $obj = new Test23();

        $args = [new Test24('hello'), new Test25('world'), 'more'];

        $result = $container->call([$obj, 'handle'], $args);

        $this->assertSame(['test24' => 'hello', 'test25' => 'world', 'three' => 'more'], $result);
    }

    public function testCoroutine()
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

    public function testRemoveCoroutine()
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

    public function testRemoveCoroutineAll()
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

    public function testCoroutineWasSingleton()
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

    public function testClassArgsClassAsStringContainer()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Class or interface Tests\\Di\\Test27 is register in container is not object.'
        );

        $container = new Container();

        $container->instance(Test27::class); // instance 直接的将直接返回字符串
        $container->make(Test28::class);
    }

    public function testClassArgsASingleClass()
    {
        $container = new Container();
        $test = $container->make(Test28::class);
        $this->assertSame('world', $test->hello());
    }

    public function testMagicGet()
    {
        $container = new Container();

        $container->bind('foo', 'bar');

        $this->assertSame('bar', $container->foo);
    }

    public function testMagicSet()
    {
        $container = new Container();

        $container->foo = 'bar';

        $this->assertSame('bar', $container->foo);
    }

    public function testMagicCallException()
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage(
            'Method `callNotFound` is not exits.'
        );

        $container = new Container();

        $container->callNotFound();
    }
}

class Test1
{
}

interface ITest2
{
}

class Test2 implements ITest2
{
}

interface ITest3
{
}

class Test3 implements ITest3
{
    public $arg1;

    public function __construct(ITest2 $arg1)
    {
        $this->arg1 = $arg1;
    }
}

class Test4 implements ITest3
{
    public $arg1;

    public function __construct(Test3 $arg1)
    {
        $this->arg1 = $arg1;
    }
}

class Test5 implements ITest3
{
    public $arg1;

    public $arg2;

    public function __construct(Test3 $arg1, $arg2 = 'hello default')
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }
}

class Test6
{
    public $arg1;

    public $arg2;

    public $arg3;

    public function __construct($arg1, $arg2, $arg3)
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
        $this->arg3 = $arg3;
    }
}

class Test7
{
}

interface ITest8
{
}

class Test8 implements ITest8
{
    public function func1()
    {
        return func_get_args();
    }

    public function func2($arg1 = 'hello')
    {
        return func_get_args();
    }

    public static function staticFunc3()
    {
        return func_get_args();
    }

    public function handle()
    {
        return ['call handle'];
    }
}

class Test9
{
    protected function __construct()
    {
    }

    public function hello()
    {
        return 'world9';
    }
}

class Test10
{
    public function hello(TestNotFound $test)
    {
        return 'test10';
    }
}

class Test20
{
    public function handle(Test21 $arg1, Test22 $arg2)
    {
        return ['test21' => $arg1->prop, 'test22' => $arg2->prop];
    }
}

class Test21
{
    public $prop;

    public function __construct(?string $prop = null)
    {
        $this->prop = $prop;
    }
}

class Test22
{
    public $prop;

    public function __construct(?string $prop = null)
    {
        $this->prop = $prop;
    }
}

class Test23
{
    public function handle(Test24 $arg1, Test25 $arg2, string $arg3)
    {
        return ['test24' => $arg1->prop, 'test25' => $arg2->prop, 'three' => $arg3];
    }
}

class Test24
{
    public $prop;

    public function __construct(?string $prop = null)
    {
        $this->prop = $prop;
    }
}

class Test25
{
    public $prop;

    public function __construct(?string $prop = null)
    {
        $this->prop = $prop;
    }
}

class Test26
{
}

class Test27
{
    public function hello(): string
    {
        return 'world';
    }
}

class Test28
{
    private $test;

    public function __construct(Test27 $test)
    {
        $this->test = $test;
    }

    public function hello()
    {
        return $this->test->hello();
    }
}

class Test29
{
    private $test;

    public function __construct(TestNotFoundNotFound $test)
    {
        $this->test = $test;
    }

    public function hello()
    {
        return $this->test->hello();
    }
}
