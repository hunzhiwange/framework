<?php

declare(strict_types=1);

namespace Tests\Event;

use Leevel\Di\Container;
use Leevel\Event\Dispatch;
use Leevel\Event\Observer;
use Leevel\Kernel\Utils\Api;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '事件',
    'path' => 'architecture/event',
    'zh-CN:description' => <<<'EOT'
QueryPHP 提供了一个事件组件 `\Leevel\Event\Dispatch` 对象。

事件适合一些业务后续处理的扩展，比如提交订单的后续通知消息接入，不但提高了可扩展性，而且还降低了系统的耦合性。
EOT,
])]
final class DispatchTest extends TestCase
{
    #[Api([
        'zh-CN:title' => '事件基本使用',
        'zh-CN:description' => <<<'EOT'
事件系统使用 `register` 注册监听器，`handle` 会执行一个事件。

**register 函数原型**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Event\Dispatch::class, 'register', 'define')]}
```

**handle 函数原型**

``` php
{[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Event\Dispatch::class, 'handle', 'define')]}
```

**fixture 定义**

**Tests\Event\Listener1**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\Listener1::class)]}
```

一般来说监听器需要继承至 `\Leevel\Event\Observer`，本质上事件使用的是观察者设计模式，而监听器是观察者角色。

**Tests\Event\Listener**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\Listener::class)]}
```
EOT,
    ])]
    public function testBaseUse(): void
    {
        if (isset($_SERVER['test'])) {
            unset($_SERVER['test']);
        }

        if (isset($_SERVER['event_name'])) {
            unset($_SERVER['event_name']);
        }

        $dispatch = new Dispatch(new Container());
        $dispatch->register('event1', Listener1::class);
        $dispatch->handle('event1');

        static::assertSame($_SERVER['test'], 'hello');
        static::assertSame($_SERVER['event_name'], 'event1');

        unset($_SERVER['test'], $_SERVER['event_name']);
    }

    #[Api([
        'zh-CN:title' => 'register 注册监听器支持监听器对象实例',
        'zh-CN:description' => <<<'EOT'
第二个参数 `$listener` 支持传递对象实例。

**fixture 定义**

**Tests\Event\Listener2**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\Listener2::class)]}
```
EOT,
    ])]
    public function testListenerInstance(): void
    {
        if (isset($_SERVER['test'])) {
            unset($_SERVER['test']);
        }

        $dispatch = new Dispatch(new Container());
        $dispatch->register('event1', new Listener2('arg_foo'));
        $dispatch->handle('event1');

        static::assertSame($_SERVER['test'], 'arg_foo');

        unset($_SERVER['test']);
    }

    #[Api([
        'zh-CN:title' => 'register 注册监听器支持事件对象实例',
        'zh-CN:description' => <<<'EOT'
第一个参数 `$event` 支持传递对象实例。

**fixture 定义**

**Tests\Event\Event1**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\Event1::class)]}
```

**Tests\Event\Listener3**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\Listener3::class)]}
```
EOT,
    ])]
    public function testEventInstance(): void
    {
        if (isset($_SERVER['test'])) {
            unset($_SERVER['test']);
        }

        $dispatch = new Dispatch(new Container());
        $dispatch->register($event = new Event1('event_arg_foo'), Listener3::class);
        $dispatch->handle($event);

        static::assertSame($_SERVER['test'], 'event_arg_foo');

        unset($_SERVER['test']);
    }

    #[Api([
        'zh-CN:title' => 'register 注册监听器支持同时为多个事件绑定监听器',
    ])]
    public function testEventAsArray(): void
    {
        $dispatch = new Dispatch(new Container());
        $event = new Event1('event_arg_foo');
        $dispatch->register([$event], Listener3::class);
        $dispatch->handle($event);

        static::assertSame($_SERVER['test'], 'event_arg_foo');

        unset($_SERVER['test']);
    }

    #[Api([
        'zh-CN:title' => 'register 注册监听器支持优先级',
        'zh-CN:description' => <<<'EOT'
第三个参数 `$priority` 表示注册的监听器的优先级，越小越靠前执行，默认为 500。

**fixture 定义**

**Tests\Event\Listener4**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\Listener4::class)]}
```

**Tests\Event\Listener5**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\Listener5::class)]}
```
EOT,
    ])]
    public function testPriority(): void
    {
        $dispatch = new Dispatch(new Container());
        $dispatch->register('foo', Listener4::class);
        $dispatch->register('foo', Listener5::class);
        $dispatch->handle('foo');

        static::assertSame($_SERVER['test'], 'l5');

        $dispatch = new Dispatch(new Container());

        // 第三个参数标识优先级，越小越靠前执行，默认为 500
        $dispatch->register('foo', Listener4::class, 5);
        $dispatch->register('foo', Listener5::class, 4);
        $dispatch->handle('foo');

        static::assertSame($_SERVER['test'], 'l4');
        unset($_SERVER['test']);
    }

    public function testListenNotFound(): void
    {
        $dispatch = new Dispatch(new Container());
        static::assertNull($dispatch->handle('notFound'));
    }

    #[Api([
        'zh-CN:title' => 'register 注册监听器支持事件通配符',
        'zh-CN:description' => <<<'EOT'
`*` 表示通配符事件，匹配的事件会执行对应的监听器。

**fixture 定义**

**Tests\Event\WildcardsListener**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\WildcardsListener::class)]}
```
EOT,
    ])]
    public function testWildcards(): void
    {
        $dispatch = new Dispatch(new Container());
        $dispatch->register('wildcards*event', WildcardsListener::class);
        $dispatch->handle('wildcards123456event');

        static::assertSame($_SERVER['wildcard'], 'wildcard');
        unset($_SERVER['wildcard']);

        $dispatch->handle('wildcards7896event');

        static::assertSame($_SERVER['wildcard'], 'wildcard');
        unset($_SERVER['wildcard']);

        $dispatch->handle('wildcards_foobar_event');

        static::assertSame($_SERVER['wildcard'], 'wildcard');
        unset($_SERVER['wildcard']);
    }

    #[Api([
        'zh-CN:title' => 'delete 删除事件所有监听器',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Event\ForRemoveListener**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\ForRemoveListener::class)]}
```
EOT,
    ])]
    public function testDeleteListeners(): void
    {
        $dispatch = new Dispatch(new Container());
        $dispatch->register('testevent', ForRemoveListener::class);
        $dispatch->handle('testevent');

        static::assertSame($_SERVER['remove'], 'remove');
        unset($_SERVER['remove']);

        $dispatch->delete('testevent');
        $dispatch->handle('testevent');

        static::assertFalse(isset($_SERVER['remove']));
    }

    #[Api([
        'zh-CN:title' => 'delete 删除通配符事件所有监听器',
    ])]
    public function testDeleteWildcardListeners(): void
    {
        $dispatch = new Dispatch(new Container());
        $dispatch->register('wildcards*event', WildcardsListener::class);
        $dispatch->handle('wildcards123456event');

        static::assertSame($_SERVER['wildcard'], 'wildcard');
        unset($_SERVER['wildcard']);

        $dispatch->delete('wildcards*event');
        $dispatch->handle('wildcards7896event');

        static::assertFalse(isset($_SERVER['wildcard']));
    }

    #[Api([
        'zh-CN:title' => 'has 判断事件监听器是否存在',
    ])]
    public function testHas(): void
    {
        $dispatch = new Dispatch(new Container());

        static::assertSame([], $dispatch->get('testevent'));
        static::assertFalse($dispatch->has('testevent'));

        $dispatch->register('testevent', Listener1::class);

        static::assertSame([500 => [Listener1::class]], $dispatch->get('testevent'));
        static::assertTrue($dispatch->has('testevent'));
    }

    #[Api([
        'zh-CN:title' => '独立类监听器必须包含 handle 方法',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Event\ListenerWithoutRunOrHandleMethod**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\ListenerWithoutRunOrHandleMethod::class)]}
```
EOT,
    ])]
    public function testListenerWithoutRunOrHandleMethod(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Observer Tests\\Event\\ListenerWithoutRunOrHandleMethod must has handle method.'
        );

        $dispatch = new Dispatch(new Container());
        $dispatch->register('testevent', ListenerWithoutRunOrHandleMethod::class);
        $dispatch->handle('testevent');
    }

    public function testListenerIsInvalid(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Observer `Tests\\Event\\NotFoundListener` is invalid.'
        );

        $dispatch = new Dispatch(new Container());
        $dispatch->register('testevent', 'Tests\\Event\\NotFoundListener');
        $dispatch->handle('testevent');
    }

    #[Api([
        'zh-CN:title' => '独立类监听器自动转换为 \Leevel\Event\Observer',
        'zh-CN:description' => <<<'EOT'
一般来说监听器需要继承至 `\Leevel\Event\Observer`，本质上事件使用的是观察者设计模式，而监听器是观察者角色。

如果是未继承的独立类，系统会自动转换成 `\Leevel\Event\Observer` 而成为一个观察者角色。

**fixture 定义**

**Tests\Event\ListenerNotExtends**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\ListenerNotExtends::class)]}
```
EOT,
    ])]
    public function testListenerNotInstanceofSplObserverWillAutoChange(): void
    {
        $dispatch = new Dispatch(new Container());
        $dispatch->register('testevent', ListenerNotExtends::class);
        $dispatch->handle('testevent');

        static::assertSame($_SERVER['autochange'], 'autochange');
        unset($_SERVER['autochange']);
    }

    #[Api([
        'zh-CN:title' => '独立类监听器必须包含 handle 方法',
        'zh-CN:description' => <<<'EOT'
**fixture 定义**

**Tests\Event\ListenerNotExtendsWithoutHandle**

``` php
{[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\ListenerNotExtendsWithoutHandle::class)]}
```
EOT,
    ])]
    public function testListenerNotInstanceofSplObserverWithoutHandle(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Observer `Tests\\Event\\ListenerNotExtendsWithoutHandle` is invalid.'
        );

        $dispatch = new Dispatch(new Container());
        $dispatch->register('testevent', ListenerNotExtendsWithoutHandle::class);
        $dispatch->handle('testevent');
    }

    public function testListenerWithoutSetHandleMustSetHandleClosure(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Observer Leevel\\Event\\Observer must has handle method.'
        );

        $dispatch = new Dispatch(new Container());
        $dispatch->register('testevent', new Observer());
        $dispatch->handle('testevent');
    }

    #[Api([
        'zh-CN:title' => '监听器支持闭包',
        'zh-CN:description' => <<<'EOT'
一般来说监听器需要继承至 `\Leevel\Event\Observer`，本质上事件使用的是观察者设计模式，而监听器是观察者角色。

如果是闭包，系统会自动转换成 `\Leevel\Event\Observer` 而成为一个观察者角色。
EOT,
    ])]
    public function testListenerIsClosure(): void
    {
        $dispatch = new Dispatch(new Container());
        $dispatch->register('testevent', function (): void {
            $_SERVER['isclosure'] = 'isclosure';
        });
        $dispatch->handle('testevent');

        static::assertSame($_SERVER['isclosure'], 'isclosure');
        unset($_SERVER['isclosure']);
    }
}

abstract class Listener extends Observer
{
}

class Listener1 extends Listener
{
    public function handle($event): void
    {
        $_SERVER['event_name'] = $event;
        $_SERVER['test'] = 'hello';
    }
}

class Listener2 extends Listener
{
    public $arg1;

    public function __construct($arg1)
    {
        $this->arg1 = $arg1;
    }

    public function handle(): void
    {
        $_SERVER['test'] = $this->arg1;
    }
}

class Listener3 extends Listener
{
    public function handle($event): void
    {
        $_SERVER['test'] = $event->arg1;
    }
}

class Listener4 extends Listener
{
    public function handle($event): void
    {
        $_SERVER['test'] = 'l4';
    }
}

class Listener5 extends Listener
{
    public function handle($event): void
    {
        $_SERVER['test'] = 'l5';
    }
}

class WildcardsListener extends Listener
{
    public function handle($event): void
    {
        $_SERVER['wildcard'] = 'wildcard';
    }
}

class ForRemoveListener extends Listener
{
    public function handle($event): void
    {
        $_SERVER['remove'] = 'remove';
    }
}

class ListenerWithoutRunOrHandleMethod extends Listener
{
    public function notFound($event): void
    {
    }
}

class ListenerNotExtends
{
    public function handle(): void
    {
        $_SERVER['autochange'] = 'autochange';
    }
}

class ListenerNotExtendsWithoutHandle
{
}

class Event1
{
    public $arg1;

    public function __construct($arg1)
    {
        $this->arg1 = $arg1;
    }
}

class Event2
{
    public $arg1;

    public function __construct($arg1 = 'default_arg1')
    {
        $this->arg1 = $arg1;
    }
}
