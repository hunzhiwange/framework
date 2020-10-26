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

namespace Tests\Event;

use Leevel\Di\Container;
use Leevel\Event\Dispatch;
use Leevel\Event\Observer;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="事件",
 *     path="architecture/event",
 *     zh-CN:description="
 * QueryPHP 提供了一个事件组件 `\Leevel\Event\Dispatch` 对象。
 *
 * 事件适合一些业务后续处理的扩展，比如提交订单的后续通知消息接入，不但提高了可扩展性，而且还降低了系统的耦合性。
 * ",
 * )
 */
class DispatchTest extends TestCase
{
    /**
     * @api(
     *     zh-CN:title="事件基本使用",
     *     zh-CN:description="
     * 事件系统使用 `register` 注册监听器，`handle` 会执行一个事件。
     *
     * **register 函数原型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Event\Dispatch::class, 'register', 'define')]}
     * ```
     *
     * **handle 函数原型**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getMethodBody(\Leevel\Event\Dispatch::class, 'handle', 'define')]}
     * ```
     *
     * **fixture 定义**
     *
     * **Tests\Event\Listener1**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\Listener1::class)]}
     * ```
     *
     * 一般来说监听器需要继承至 `\Leevel\Event\Observer`，本质上事件使用的是观察者设计模式，而监听器是观察者角色。
     *
     * **Tests\Event\Listener**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\Listener::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
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

        $this->assertSame($_SERVER['test'], 'hello');
        $this->assertSame($_SERVER['event_name'], 'event1');

        unset($_SERVER['test'], $_SERVER['event_name']);
    }

    /**
     * @api(
     *     zh-CN:title="register 注册监听器支持监听器对象实例",
     *     zh-CN:description="
     * 第二个参数 `$listener` 支持传递对象实例。
     *
     * **fixture 定义**
     *
     * **Tests\Event\Listener2**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\Listener2::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testListenerInstance(): void
    {
        if (isset($_SERVER['test'])) {
            unset($_SERVER['test']);
        }

        $dispatch = new Dispatch(new Container());
        $dispatch->register('event1', new Listener2('arg_foo'));
        $dispatch->handle('event1');

        $this->assertSame($_SERVER['test'], 'arg_foo');

        unset($_SERVER['test']);
    }

    /**
     * @api(
     *     zh-CN:title="register 注册监听器支持事件对象实例",
     *     zh-CN:description="
     * 第一个参数 `$event` 支持传递对象实例。
     *
     * **fixture 定义**
     *
     * **Tests\Event\Event1**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\Event1::class)]}
     * ```
     *
     * **Tests\Event\Listener3**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\Listener3::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testEventInstance(): void
    {
        if (isset($_SERVER['test'])) {
            unset($_SERVER['test']);
        }

        $dispatch = new Dispatch(new Container());
        $dispatch->register($event = new Event1('event_arg_foo'), Listener3::class);
        $dispatch->handle($event);

        $this->assertSame($_SERVER['test'], 'event_arg_foo');

        unset($_SERVER['test']);
    }

    /**
     * @api(
     *     zh-CN:title="register 注册监听器支持同时为多个事件绑定监听器",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testEventAsArray(): void
    {
        $dispatch = new Dispatch(new Container());
        $event = new Event1('event_arg_foo');
        $dispatch->register([$event], Listener3::class);
        $dispatch->handle($event);

        $this->assertSame($_SERVER['test'], 'event_arg_foo');

        unset($_SERVER['test']);
    }

    /**
     * @api(
     *     zh-CN:title="register 注册监听器支持优先级",
     *     zh-CN:description="
     * 第三个参数 `$priority` 表示注册的监听器的优先级，越小越靠前执行，默认为 500。
     *
     * **fixture 定义**
     *
     * **Tests\Event\Listener4**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\Listener4::class)]}
     * ```
     *
     * **Tests\Event\Listener5**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\Listener5::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testPriority(): void
    {
        $dispatch = new Dispatch(new Container());
        $dispatch->register('foo', Listener4::class);
        $dispatch->register('foo', Listener5::class);
        $dispatch->handle('foo');

        $this->assertSame($_SERVER['test'], 'l5');

        $dispatch = new Dispatch(new Container());

        // 第三个参数标识优先级，越小越靠前执行，默认为 500
        $dispatch->register('foo', Listener4::class, 5);
        $dispatch->register('foo', Listener5::class, 4);
        $dispatch->handle('foo');

        $this->assertSame($_SERVER['test'], 'l4');
        unset($_SERVER['test']);
    }

    public function testListenNotFound(): void
    {
        $dispatch = new Dispatch(new Container());
        $this->assertNull($dispatch->handle('notFound'));
    }

    /**
     * @api(
     *     zh-CN:title="register 注册监听器支持事件通配符",
     *     zh-CN:description="
     * `*` 表示通配符事件，匹配的事件会执行对应的监听器。
     *
     * **fixture 定义**
     *
     * **Tests\Event\WildcardsListener**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\WildcardsListener::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testWildcards(): void
    {
        $dispatch = new Dispatch(new Container());
        $dispatch->register('wildcards*event', WildcardsListener::class);
        $dispatch->handle('wildcards123456event');

        $this->assertSame($_SERVER['wildcard'], 'wildcard');
        unset($_SERVER['wildcard']);

        $dispatch->handle('wildcards7896event');

        $this->assertSame($_SERVER['wildcard'], 'wildcard');
        unset($_SERVER['wildcard']);

        $dispatch->handle('wildcards_foobar_event');

        $this->assertSame($_SERVER['wildcard'], 'wildcard');
        unset($_SERVER['wildcard']);
    }

    /**
     * @api(
     *     zh-CN:title="delete 删除事件所有监听器",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Event\ForRemoveListener**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\ForRemoveListener::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testDeleteListeners(): void
    {
        $dispatch = new Dispatch(new Container());
        $dispatch->register('testevent', ForRemoveListener::class);
        $dispatch->handle('testevent');

        $this->assertSame($_SERVER['remove'], 'remove');
        unset($_SERVER['remove']);

        $dispatch->delete('testevent');
        $dispatch->handle('testevent');

        $this->assertFalse(isset($_SERVER['remove']));
    }

    /**
     * @api(
     *     zh-CN:title="delete 删除通配符事件所有监听器",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testDeleteWildcardListeners(): void
    {
        $dispatch = new Dispatch(new Container());
        $dispatch->register('wildcards*event', WildcardsListener::class);
        $dispatch->handle('wildcards123456event');

        $this->assertSame($_SERVER['wildcard'], 'wildcard');
        unset($_SERVER['wildcard']);

        $dispatch->delete('wildcards*event');
        $dispatch->handle('wildcards7896event');

        $this->assertFalse(isset($_SERVER['wildcard']));
    }

    /**
     * @api(
     *     zh-CN:title="has 判断事件监听器是否存在",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testHas(): void
    {
        $dispatch = new Dispatch(new Container());

        $this->assertSame([], $dispatch->get('testevent'));
        $this->assertFalse($dispatch->has('testevent'));

        $dispatch->register('testevent', Listener1::class);

        $this->assertSame([500 => [Listener1::class]], $dispatch->get('testevent'));
        $this->assertTrue($dispatch->has('testevent'));
    }

    /**
     * @api(
     *     zh-CN:title="独立类监听器必须包含 handle 方法",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Event\ListenerWithoutRunOrHandleMethod**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\ListenerWithoutRunOrHandleMethod::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="独立类监听器自动转换为 \Leevel\Event\Observer",
     *     zh-CN:description="
     * 一般来说监听器需要继承至 `\Leevel\Event\Observer`，本质上事件使用的是观察者设计模式，而监听器是观察者角色。
     *
     * 如果是未继承的独立类，系统会自动转换成 `\Leevel\Event\Observer` 而成为一个观察者角色。
     *
     * **fixture 定义**
     *
     * **Tests\Event\ListenerNotExtends**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\ListenerNotExtends::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testListenerNotInstanceofSplObserverWillAutoChange(): void
    {
        $dispatch = new Dispatch(new Container());
        $dispatch->register('testevent', ListenerNotExtends::class);
        $dispatch->handle('testevent');

        $this->assertSame($_SERVER['autochange'], 'autochange');
        unset($_SERVER['autochange']);
    }

    /**
     * @api(
     *     zh-CN:title="独立类监听器必须包含 handle 方法",
     *     zh-CN:description="
     * **fixture 定义**
     *
     * **Tests\Event\ListenerNotExtendsWithoutHandle**
     *
     * ``` php
     * {[\Leevel\Kernel\Utils\Doc::getClassBody(\Tests\Event\ListenerNotExtendsWithoutHandle::class)]}
     * ```
     * ",
     *     zh-CN:note="",
     * )
     */
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

    /**
     * @api(
     *     zh-CN:title="监听器支持闭包",
     *     zh-CN:description="
     * 一般来说监听器需要继承至 `\Leevel\Event\Observer`，本质上事件使用的是观察者设计模式，而监听器是观察者角色。
     *
     * 如果是闭包，系统会自动转换成 `\Leevel\Event\Observer` 而成为一个观察者角色。
     * ",
     *     zh-CN:note="",
     * )
     */
    public function testListenerIsClosure(): void
    {
        $dispatch = new Dispatch(new Container());
        $dispatch->register('testevent', function () {
            $_SERVER['isclosure'] = 'isclosure';
        });
        $dispatch->handle('testevent');

        $this->assertSame($_SERVER['isclosure'], 'isclosure');
        unset($_SERVER['isclosure']);
    }
}

abstract class Listener extends Observer
{
}

class Listener1 extends Listener
{
    public function handle($event)
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

    public function handle()
    {
        $_SERVER['test'] = $this->arg1;
    }
}

class Listener3 extends Listener
{
    public function handle($event)
    {
        $_SERVER['test'] = $event->arg1;
    }
}

class Listener4 extends Listener
{
    public function handle($event)
    {
        $_SERVER['test'] = 'l4';
    }
}

class Listener5 extends Listener
{
    public function handle($event)
    {
        $_SERVER['test'] = 'l5';
    }
}

class WildcardsListener extends Listener
{
    public function handle($event)
    {
        $_SERVER['wildcard'] = 'wildcard';
    }
}

class ForRemoveListener extends Listener
{
    public function handle($event)
    {
        $_SERVER['remove'] = 'remove';
    }
}

class ListenerWithoutRunOrHandleMethod extends Listener
{
    public function notFound($event)
    {
    }
}

class ListenerNotExtends
{
    public function handle()
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
