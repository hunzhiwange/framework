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

namespace Tests\Event;

use Leevel\Di\Container;
use Leevel\Event\Dispatch;
use Leevel\Event\Observer;
use Tests\TestCase;

/**
 * dispatch test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.05.29
 *
 * @version 1.0
 */
class DispatchTest extends TestCase
{
    public function testBaseUse()
    {
        if (isset($_SERVER['test'])) {
            unset($_SERVER['test']);
        }

        if (isset($_SERVER['event_name'])) {
            unset($_SERVER['event_name']);
        }

        $dispatch = new Dispatch(new Container());

        $dispatch->register('event', Listener1::class);

        $dispatch->handle('event');

        $this->assertSame($_SERVER['test'], 'hello');
        $this->assertSame($_SERVER['event_name'], 'event');

        unset($_SERVER['test'], $_SERVER['event_name']);
    }

    public function testListenerInstance()
    {
        if (isset($_SERVER['test'])) {
            unset($_SERVER['test']);
        }

        $dispatch = new Dispatch(new Container());

        $dispatch->register('event', new Listener2('arg_foo'));

        $dispatch->handle('event');

        $this->assertSame($_SERVER['test'], 'arg_foo');

        unset($_SERVER['test']);
    }

    public function testEventInstance()
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

    public function testEventAsArray()
    {
        $dispatch = new Dispatch(new Container());

        $event = new Event1('event_arg_foo');

        $dispatch->register([$event], Listener3::class);

        $dispatch->handle($event);

        $this->assertSame($_SERVER['test'], 'event_arg_foo');

        unset($_SERVER['test']);
    }

    public function testPriority()
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

    public function testListenNotFound()
    {
        $dispatch = new Dispatch(new Container());

        $this->assertNull($dispatch->handle('notFound'));
    }

    public function testWildcards()
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

    public function testDeleteListeners()
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

    public function testDeleteListeners2()
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

    public function testHas()
    {
        $dispatch = new Dispatch(new Container());

        $this->assertSame([], $dispatch->get('testevent'));
        $this->assertFalse($dispatch->has('testevent'));

        $dispatch->register('testevent', Listener1::class);

        $this->assertSame([500 => ['Tests\Event\Listener1']], $dispatch->get('testevent'));
        $this->assertTrue($dispatch->has('testevent'));
    }

    public function testListenerWithoutRunOrHandleMethod()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Observer Tests\Event\ListenerWithoutRunOrHandleMethod must has handle method.'
        );

        $dispatch = new Dispatch(new Container());

        $dispatch->register('testevent', ListenerWithoutRunOrHandleMethod::class);

        $dispatch->handle('testevent');
    }

    public function testListenerIsInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Observer `Tests\\Event\\NotFoundListener` is invalid.'
        );

        $dispatch = new Dispatch(new Container());

        $dispatch->register('testevent', 'Tests\Event\NotFoundListener');

        $dispatch->handle('testevent');
    }

    public function testListenerNotInstanceofSplObserverWillAuthChange()
    {
        $dispatch = new Dispatch(new Container());

        $dispatch->register('testevent', ListenerNotExtends::class);

        $dispatch->handle('testevent');

        $this->assertSame($_SERVER['autochange'], 'autochange');
        unset($_SERVER['autochange']);
    }

    public function testListenerNotInstanceofSplObserverWithoutHandle()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Observer `Tests\\Event\\ListenerNotExtendsWithoutHandle` is invalid.'
        );

        $dispatch = new Dispatch(new Container());

        $dispatch->register('testevent', ListenerNotExtendsWithoutHandle::class);

        $dispatch->handle('testevent');
    }

    public function testListenerWithoutSetHandleMustSetHandleClosure()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Observer Leevel\\Event\\Observer must has handle method.'
        );

        $dispatch = new Dispatch(new Container());

        $dispatch->register('testevent', new Observer());

        $dispatch->handle('testevent');
    }

    public function testListenerIsClosure()
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
