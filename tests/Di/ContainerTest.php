<?php

declare(strict_types=1);

namespace Tests\Di;

use Leevel\Di\Container;
use Leevel\Kernel\Utils\Api;
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

#[Api([
    'zh-CN:title' => 'IOC 容器',
    'path' => 'architecture/ioc',
    'zh-CN:description' => <<<'EOT'
IOC 容器是整个框架最核心的部分，负责服务的管理和解耦。

目前系统所有的关键服务都接入了 IOC 容器，包括控制器、Console 命令行。
EOT,
])]
final class ContainerTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '闭包绑定',
        'zh-CN:description' => <<<'EOT'
闭包属于惰性，真正使用的时候才会执行。

我们可以通过 `bind` 来绑定一个闭包，通过 `make` 来运行服务，第二次运行如果是单例则直接使用生成后的结果，否则会每次执行闭包的代码。

通常来说，系统大部分服务都是单例来提升性能和共享。
EOT,
    ])]
    public function testBindClosure(): void
    {
        $container = new Container();

        $container->bind('foo', function () {
            return 'bar';
        });

        static::assertSame('bar', $container->make('foo'));
    }

    #[Api([
        'zh-CN:title' => '闭包绑定单例',
    ])]
    public function testSingletonClosure(): void
    {
        $container = new Container();
        $singleton = new \stdClass();
        $container->singleton('singleton', function () use ($singleton) {
            return $singleton;
        });

        static::assertSame($singleton, $container->make('singleton'));
        static::assertSame($singleton, $container->make('singleton'));
    }

    #[Api([
        'zh-CN:title' => '类直接生成本身',
        'zh-CN:description' => <<<'EOT'
一个独立的类可以直接生成，而不需要提前注册到容器中。

**fixture 定义**

**Tests\Di\Fixtures\Test1**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test1::class)]}
```
EOT,
    ])]
    public function testClass(): void
    {
        $container = new Container();

        static::assertInstanceOf(Test1::class, $container->make(Test1::class));
    }

    #[Api([
        'zh-CN:title' => '类单例',
        'zh-CN:description' => <<<'EOT'
类也可以注册为单例。
EOT,
    ])]
    public function testSingletonClass(): void
    {
        $container = new Container();
        $container->singleton(Test1::class);

        static::assertSame($container->make(Test1::class), $container->make(Test1::class));
    }

    #[Api([
        'zh-CN:title' => '接口绑定',
        'zh-CN:description' => <<<'EOT'
可以为接口绑定实现。

**fixture 定义**

**Tests\Di\Fixtures\ITest2**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\ITest2::class)]}
```

**Tests\Di\Fixtures\Test2**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test2::class)]}
```
EOT,
    ])]
    public function testInterface(): void
    {
        $container = new Container();
        $container->bind(ITest2::class, Test2::class);

        static::assertInstanceOf(ITest2::class, $container->make(ITest2::class));
        static::assertInstanceOf(ITest2::class, $container->make(Test2::class));
    }

    #[Api([
        'zh-CN:title' => '接口绑定接口作为构造器参数',
        'zh-CN:description' => <<<'EOT'
接口可以作为控制器参数来做依赖注入。

**fixture 定义**

**Tests\Di\Fixtures\Test3**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test3::class)]}
```

通过 `Test3` 的构造函数注入 `ITest2` 的实现 `Test2`，通过 IOC 容器可以实现代码解耦。
EOT,
    ])]
    public function testInterface2(): void
    {
        $container = new Container();
        $container->bind(ITest2::class, Test2::class);

        static::assertInstanceOf(ITest2::class, $test2 = $container->make(Test3::class)->arg1);
        static::assertInstanceOf(Test2::class, $test2);
    }

    public function testInterface3(): void
    {
        $container = new Container();
        $container->bind(ITest2::class, Test2::class);
        $test4 = $container->make(Test4::class);

        static::assertInstanceOf(ITest3::class, $test4->arg1);
        static::assertInstanceOf(ITest2::class, $test4->arg1->arg1);
    }

    #[Api([
        'zh-CN:title' => '绑定闭包第一个参数为 IOC 容器本身',
    ])]
    public function testContainerAsFirstArgs(): void
    {
        $container = new Container();
        $container->bind('test', function ($container) {
            return $container;
        });

        static::assertSame($container, $container->make('test'));
    }

    #[Api([
        'zh-CN:title' => '数组访问 ArrayAccess 支持',
    ])]
    public function testArrayAccess(): void
    {
        $container = new Container();
        $container['foo'] = function () {
            return 'bar';
        };

        static::assertTrue(isset($container['foo']));
        static::assertSame('bar', $container['foo']);
        unset($container['foo']);
        static::assertFalse(isset($container['foo']));
    }

    #[Api([
        'zh-CN:title' => 'alias 设置别名',
    ])]
    public function testAliases(): void
    {
        $container = new Container();
        $container['foo'] = 'bar';
        $container->alias('foo', 'foo2');
        $container->alias('foo', ['foo3', 'foo4']);
        $container->alias(['foo' => ['foo5', 'foo6']]);
        $container->alias(['foo' => 'foo7']);

        static::assertNull($container->make('foo', throw: false));
        static::assertNull($container->make('foo2', throw: false));
        static::assertNull($container->make('foo3', throw: false));
        static::assertNull($container->make('foo4', throw: false));
        static::assertNull($container->make('foo5', throw: false));
        static::assertNull($container->make('foo6', throw: false));
        static::assertNull($container->make('foo7', throw: false));
    }

    #[Api([
        'zh-CN:title' => 'make 创建容器服务并返回支持参数',
    ])]
    public function testMakeWithArgs(): void
    {
        $container = new Container();
        $container['foo'] = function ($container, $arg1, $arg2) {
            return [
                $arg1,
                $arg2,
            ];
        };

        static::assertSame([1, 2], $container->make('foo', [1, 2, 3]));
    }

    #[Api([
        'zh-CN:title' => 'get 创建容器服务并返回',
    ])]
    public function testGet(): void
    {
        $container = new Container();
        $container['foo'] = function ($container) {
            return [1, 2];
        };

        static::assertSame([1, 2], $container->make('foo'));
    }

    #[Api([
        'zh-CN:title' => 'bind 注册到容器支持覆盖',
    ])]
    public function testOverridden(): void
    {
        $container = new Container();
        $container['foo'] = fn () => 'bar';
        static::assertSame('bar', $container['foo']);

        $container['foo'] = fn () => 'bar2';
        static::assertSame('bar2', $container['foo']);
    }

    #[Api([
        'zh-CN:title' => 'instance 注册为实例',
    ])]
    public function testInstance(): void
    {
        $container = new Container();
        $instance = new \stdClass();
        $container->instance('foo', $instance);

        static::assertSame($instance, $container->make('foo'));
    }

    #[Api([
        'zh-CN:title' => '默认参数支持',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Di\Fixtures\Test5**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test5::class)]}
```

**Tests\Di\Fixtures\ITest3**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\ITest3::class)]}
```
EOT,
    ])]
    public function testDefaultArgs(): void
    {
        $container = new Container();
        $container->bind(ITest2::class, Test2::class);
        $container->bind('foo', Test5::class);
        $test5 = $container->make('foo');

        static::assertInstanceOf(ITest3::class, $test5);
        static::assertSame('hello default', $test5->arg2);
    }

    #[Api([
        'zh-CN:title' => '必填参数校验',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Di\Fixtures\Test6**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test6::class)]}
```
EOT,
    ])]
    public function testArgsRequiredContainerInvalidArgumentException(): void
    {
        $this->expectException(\Leevel\Di\ContainerInvalidArgumentException::class);
        $this->expectExceptionMessage(
            'There are 3 required args,but 0 given.'
        );

        $container = new Container();
        $container->make(Test6::class, []);
    }

    #[Api([
        'zh-CN:title' => '接口必须绑定服务',
    ])]
    public function testInterfaceContainerInvalidArgumentException(): void
    {
        $this->expectException(\Leevel\Di\ContainerInvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Interface Tests\\Di\\Fixtures\\ITest2 cannot be normalize because not bound.'
        );

        $container = new Container();
        $container->make(ITest2::class, []);
    }

    #[Api([
        'zh-CN:title' => 'call 回调自动依赖注入',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Di\Fixtures\Test7**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test7::class)]}
```

**Tests\Di\Fixtures\Test8**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test8::class)]}
```
EOT,
    ])]
    public function testCall(): void
    {
        $container = new Container();
        $result = $container->call(function (Test7 $arg1, array $arg2 = []) {
            return \func_get_args();
        });

        static::assertInstanceOf(Test7::class, $result[0]);
        static::assertSame([], $result[1]);

        $result = $container->call(function (Test7 $arg1, array $arg2 = [], $arg3 = null) {
            return \func_get_args();
        }, ['arg3' => 'hello']);

        static::assertInstanceOf(Test7::class, $result[0]);
        static::assertSame([], $result[1]);
        static::assertSame('hello', $result[2]);

        $test7 = new Test7();
        $result = $container->call(function (Test7 $arg1, $arg2 = 'hello') {
            return \func_get_args();
        }, [Test7::class => $test7, 'arg2' => 'hello world']);

        static::assertSame($test7, $result[0]);
        static::assertSame('hello world', $result[1]);

        $test8 = new Test8();
        $result = $container->call(function ($arg1, $arg2, $arg3, ?ITest8 $arg4 = null, ?Test8 $arg5 = null) {
            return \func_get_args();
        }, ['arg1' => 'foo', 'arg3' => 'world2', Test8::class => $test8]);

        static::assertSame('foo', $result[0]);
        static::assertNull($result[1]);
        static::assertSame('world2', $result[2]);
        static::assertNull($result[3]);
        static::assertSame($result[4], $test8);
    }

    public function testCallNotFoundClass(): void
    {
        $this->expectException(\ReflectionException::class);
        $this->expectExceptionMessage(
            'Class "Test8" does not exist'
        );

        $container = new Container();
        $container->call('Test8');
    }

    #[Api([
        'zh-CN:title' => 'call 回调自动依赖注入支持字符串或者数组类回调',
    ])]
    public function testCallWithArrayOrString(): void
    {
        $container = new Container();

        $result = $container->call([Test8::class, 'func1'], ['foo', 'bar']);
        static::assertSame(['foo', 'bar'], $result);

        $result = $container->call(Test8::class.'@func1', ['foo', 'bar']);
        static::assertSame(['foo', 'bar'], $result);

        $result = $container->call([Test8::class], ['foo', 'bar']);
        static::assertSame(['call handle'], $result);

        $result = $container->call(Test8::class.'@', ['foo', 'bar']);
        static::assertSame(['call handle'], $result);

        $result = $container->call(Test8::class.'@func2');
        static::assertSame('hello', $result[0]);

        $result = $container->call(Test8::class.'@func2', ['world', 'foo', 'bar']);
        static::assertSame('world', $result[0]);
        static::assertSame('foo', $result[1]);
        static::assertSame('bar', $result[2]);

        $result = $container->call(Test8::class.'@func2', ['world', 'arg1' => 'foo', 'bar']);
        static::assertSame('foo', $result[0]);
        static::assertSame('world', $result[1]);
        static::assertSame('bar', $result[2]);
    }

    #[Api([
        'zh-CN:title' => 'call 回调自动依赖注入支持实例和方法数组回调',
    ])]
    public function testCallWithCallableArray(): void
    {
        $container = new Container();
        $test8 = new Test8();
        $result = $container->call([$test8, 'func1'], ['foo', 'bar']);

        static::assertSame(['foo', 'bar'], $result);
    }

    #[Api([
        'zh-CN:title' => 'call 回调自动依赖注入支持静态回调',
    ])]
    public function testCallStatic(): void
    {
        $container = new Container();

        $result = $container->call(Test8::class.'::staticFunc3', ['hello', 'world']);
        static::assertSame(['hello', 'world'], $result);
    }

    public function testCallInvalidClass(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'The class name must be string.'
        );

        $container = new Container();
        $container->call([1, 'bar']);
    }

    #[Api([
        'zh-CN:title' => 'remove 删除服务和实例',
    ])]
    public function testRemove(): void
    {
        $container = new Container();

        $test8 = new Test8();
        $container->instance(Test8::class, $test8);
        static::assertTrue($container->exists(Test8::class));

        $container->remove(Test8::class);
        static::assertFalse($container->exists(Test8::class));
    }

    #[Api([
        'zh-CN:title' => 'exists 或者 has 服务或者实例是否存在',
    ])]
    public function testExistsOrHas(): void
    {
        $container = new Container();

        $test8 = new Test8();
        $container->instance(Test8::class, $test8);
        static::assertTrue($container->exists(Test8::class));
        static::assertTrue($container->has(Test8::class));

        $container->remove(Test8::class);
        static::assertFalse($container->exists(Test8::class));
        static::assertFalse($container->has(Test8::class));
    }

    #[Api([
        'zh-CN:title' => '实例数组访问 ArrayAccess.offsetUnset 支持',
    ])]
    public function testUnsetInstances(): void
    {
        $container = new Container();

        $container->instance('foo', 'bar');
        $container->alias('foo', 'foo2');
        $container->alias('foo', 'foo3');

        static::assertTrue(isset($container['foo']));
        static::assertTrue(isset($container['foo2']));
        static::assertTrue(isset($container['foo3']));

        unset($container['foo']);

        static::assertFalse(isset($container['foo']));
        static::assertFalse(isset($container['foo2']));
        static::assertFalse(isset($container['foo3']));
    }

    #[Api([
        'zh-CN:title' => '类依赖注入构造器必须为 public',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Di\Fixtures\Test9**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test9::class)]}
```
EOT,
    ])]
    public function testNotInstantiable(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Class Tests\\Di\\Fixtures\\Test9 is not instantiable.'
        );

        $container = new Container();
        static::assertSame('world9', $container->make(Test9::class)->hello());
    }

    public function testErrorCallTypes(): void
    {
        $this->expectException(\TypeError::class);
        $container = new Container();
        $container->call(false);
    }

    public function testServiceNotFound(): void
    {
        $this->expectException(\Leevel\Di\ServiceNotFoundException::class);
        $this->expectExceptionMessage(
            'Service hello was not found.'
        );

        $container = new Container();
        $container->call(['hello', 'notfound']);
    }

    public function testUnsupportedCallbackTypes(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Unsupported callback types.'
        );

        $container = new Container();
        $container->call(['stdClass', 'notfound']);
    }

    #[Api([
        'zh-CN:title' => 'bind 注册到容器可以支持各种数据',
    ])]
    public function testMakeServiceBool(): void
    {
        $container = new Container();

        $container->bind('foo', false);
        static::assertFalse($container->make('foo'));
    }

    #[Api([
        'zh-CN:title' => 'bind 注册到容器支持传递数组来设置别名',
    ])]
    public function testBindArrayAsAlias(): void
    {
        $container = new Container();
        $container->bind(['foo' => 'bar'], false);

        static::assertFalse($container->make('foo'));
        static::assertFalse($container->make('bar'));
    }

    #[Api([
        'zh-CN:title' => '依赖注入的方法中类参数不存在例子',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Di\Fixtures\Test10**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test10::class)]}
```
EOT,
    ])]
    public function testParseReflectionException(): void
    {
        $this->expectException(\Leevel\Di\ServiceNotFoundException::class);
        $this->expectExceptionMessage(
            'Service Tests\\Di\\Fixtures\\TestNotFound was not found.'
        );

        $container = new Container();
        $container->call([new Test10(), 'hello']);
    }

    #[Api([
        'zh-CN:title' => 'instance 注册为实例支持传递数组来设置别名',
    ])]
    public function testInstanceWithArray(): void
    {
        $container = new Container();
        $instance = new \stdClass();
        $container->instance(['foo' => 'bar'], $instance);

        static::assertSame($instance, $container->make('foo'));
        static::assertSame($instance, $container->make('bar'));
    }

    #[Api([
        'zh-CN:title' => 'instance 注册为实例未传递第二个参数会注册自身',
        'zh-CN:description' => <<<'EOT'
比如说系统中中间件注册。

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Session\Provider\Register::class, 'middleware', 'define')]}
```
EOT,
    ])]
    public function testInstanceItSelf(): void
    {
        $container = new Container();
        $container->instance('foo');
        static::assertSame('foo', $container->make('foo'));

        $container->instance('Leevel\\Foo\\Middleware\\Bar');
        static::assertSame('Leevel\\Foo\\Middleware\\Bar', $container->make('Leevel\\Foo\\Middleware\\Bar'));
    }

    #[Api([
        'zh-CN:title' => '参数为类实例例子',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Di\Fixtures\Test20**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test20::class)]}
```

**Tests\Di\Fixtures\Test21**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test21::class)]}
```

**Tests\Di\Fixtures\Test22**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test22::class)]}
```
EOT,
    ])]
    public function testCallWithClassArgsAndItInstance(): void
    {
        $container = new Container();
        $obj = new Test20();
        $args = [new Test21('hello'), new Test22('world')];
        $result = $container->call([$obj, 'handle'], $args);

        static::assertSame(['test21' => 'hello', 'test22' => 'world'], $result);
    }

    #[Api([
        'zh-CN:title' => '参数为类实例例子和其它参数混合',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Di\Fixtures\Test23**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test23::class)]}
```

**Tests\Di\Fixtures\Test24**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test24::class)]}
```

**Tests\Di\Fixtures\Test25**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test25::class)]}
```
EOT,
    ])]
    public function testCallWithClassArgsAndItInstanceAndMore(): void
    {
        $container = new Container();
        $obj = new Test23();
        $args = [new Test24('hello'), new Test25('world'), 'more'];
        $result = $container->call([$obj, 'handle'], $args);

        static::assertSame(['test24' => 'hello', 'test25' => 'world', 'three' => 'more'], $result);
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

    #[Api([
        'zh-CN:title' => 'make 创建容器服务并返回支持类名生成服务',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Di\Fixtures\Test28**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\Test28::class)]}
```
EOT,
    ])]
    public function testClassArgsASingleClass(): void
    {
        $container = new Container();
        $test = $container->make(Test28::class);
        static::assertSame('world', $test->hello());
    }

    #[Api([
        'zh-CN:title' => '魔术方法 __get 支持',
    ])]
    public function testMagicGet(): void
    {
        $container = new Container();
        $container->instance('foo', 'bar');

        static::assertSame('bar', $container->foo);
    }

    #[Api([
        'zh-CN:title' => '魔术方法 __set 支持',
    ])]
    public function testMagicSet(): void
    {
        $container = new Container();
        $container->foo = function (): string {
            return 'bar';
        };

        static::assertSame('bar', $container->foo);
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

    #[Api([
        'zh-CN:title' => 'clear 清理容器',
    ])]
    public function testClear(): void
    {
        $container = new Container();
        $container->instance('foo', 'bar');

        static::assertSame('bar', $container->make('foo'));
        static::assertNull($container->make('notfound', throw: false));

        $container->clear();
        static::assertNull($container->make('foo', throw: false));
    }

    #[Api([
        'zh-CN:title' => 'IOC 容器禁止克隆',
    ])]
    public function testClone(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'IOC container disallowed clone.'
        );

        $container = new Container();
        $container2 = clone $container;
    }

    #[Api([
        'zh-CN:title' => 'makeProvider 创建服务提供者',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Di\Fixtures\ProviderTest1**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\ProviderTest1::class)]}
```
EOT,
    ])]
    public function testMakeProvider(): void
    {
        $container = new Container();
        $container->makeProvider(ProviderTest1::class);

        static::assertSame(1, $_SERVER['testMakeProvider']);
        unset($_SERVER['testMakeProvider']);
    }

    #[Api([
        'zh-CN:title' => 'callProviderBootstrap 执行服务提供者 bootstrap',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Di\Fixtures\ProviderTest2**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\ProviderTest2::class)]}
```
EOT,
    ])]
    public function testCallProviderBootstrap(): void
    {
        $container = new Container();
        $container->callProviderBootstrap(new ProviderTest1($container));

        static::assertSame(1, $_SERVER['testMakeProvider']);
        unset($_SERVER['testMakeProvider']);

        $container->callProviderBootstrap(new ProviderTest2($container));
        static::assertSame(1, $_SERVER['testCallProviderBootstrap']);
        unset($_SERVER['testCallProviderBootstrap']);
    }

    #[Api([
        'zh-CN:title' => 'registerProviders 注册服务提供者',
    ])]
    public function testRegisterProviders(): void
    {
        $container = new Container();

        static::assertFalse($container->isBootstrap());
        $container->registerProviders([], [], []);
        static::assertTrue($container->isBootstrap());

        // do nothing
        $container->registerProviders([], [], []);
    }

    #[Api([
        'zh-CN:title' => 'registerProviders 注册服务提供者支持延迟写入',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Di\Fixtures\DeferredProvider**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Di\Fixtures\DeferredProvider::class)]}
```
EOT,
    ])]
    public function testDeferredProvider(): void
    {
        $deferredProviders = [
            'test_deferred' => 'Tests\\Di\\Fixtures\\DeferredProvider',
        ];

        $deferredAlias = [
            'Tests\\Di\\Fixtures\\DeferredProvider' => [
                'test_deferred' => 'bar',
            ],
        ];

        $container = new Container();

        static::assertFalse($container->isBootstrap());
        $container->registerProviders([], $deferredProviders, $deferredAlias);
        static::assertTrue($container->isBootstrap());
        static::assertNull($container->make('bar', throw: false));
        static::assertNull($container->make('test_deferred', throw: false));
        static::assertSame(1, $_SERVER['testDeferredProvider']);

        unset($_SERVER['testDeferredProvider']);
    }

    public function testCallWithClosureBug1(): void
    {
        $container = new Container();
        $result = $container->call(function (string $event, string $level, string $message, array $context = []): void {
            $this->assertSame('log.log', $event);
            $this->assertSame('info', $level);
            $this->assertSame('test_log', $message);
            $this->assertSame(['exends' => 'bar'], $context);
        }, ['log.log', 'info', 'test_log', ['exends' => 'bar'],
        ]);
    }

    public function testCallWithClosureBug2(): void
    {
        $container = new Container();
        $result = $container->call(function (string $event, string $level, string $message, array $context): void {
            $this->assertSame('log.log', $event);
            $this->assertSame('info', $level);
            $this->assertSame('test_log', $message);
            $this->assertSame(['exends' => 'bar'], $context);
        }, ['log.log', 'info', 'test_log', ['exends' => 'bar'],
        ]);
    }

    public function testCallWithReflectionUnionTypeBug(): void
    {
        $container = new Container();
        $container->call(function (string $event, string|array $level, string $message, array $context): void {
            $this->assertSame('log.log', $event);
            $this->assertSame('info', $level);
            $this->assertSame('test_log', $message);
            $this->assertSame(['exends' => 'bar'], $context);
        }, ['log.log', 'info', 'test_log', ['exends' => 'bar'],
        ]);
    }

    public function testCallWithMustBePassedByReferenceValueGivenBug(): void
    {
        $hello = 'info';
        $container = new Container();
        $container->call(function (string $event, string|array &$level, string $message, array $context): void {
            $this->assertSame('log.log', $event);
            $this->assertSame('info', $level);
            $this->assertSame('test_log', $message);
            $this->assertSame(['exends' => 'bar'], $context);
            // 无效
            $level = 'newinfo';
        }, ['log.log', $hello, 'test_log', ['exends' => 'bar'],
        ]);

        static::assertSame('info', $hello);
    }

    public function test1(): void
    {
        $container = new Container();
        $container->instance('foo', 'bar');
        static::assertSame('bar', $container->make('foo'));
        static::assertSame('bar', $container->get('foo'));
    }
}
