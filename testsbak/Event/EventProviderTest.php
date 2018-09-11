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
use Leevel\Event\EventProvider;
use Leevel\Event\Observer;
use Tests\TestCase;

/**
 * eventProvider test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.07.27
 *
 * @version 1.0
 */
class EventProviderTest extends TestCase
{
    public function testBaseUse()
    {
        $container = new Container();

        $provider = new TestEventProvider($container);

        $dispatch = new Dispatch($container);

        $this->assertNull($provider->register());

        $provider->bootstrap($dispatch);

        $_SERVER['runtime'] = [];

        $dispatch->run(new TestEvent('hello blog'));

        $this->assertSame(['test3', 'test2', 'hello blog', 'test1'], $_SERVER['runtime']);

        unset($_SERVER['runtime']);
    }
}

class TestEventProvider extends EventProvider
{
    protected $listeners = [
        TestEvent::class => [
            // 优先级支持写法，数字越小越早执行，默认为 500
            TestListener1::class,
            TestListener2::class => 5,
            TestListener3::class => 2,
        ],
    ];
}

class TestEvent
{
    protected $blog;

    public function __construct($blog)
    {
        $this->blog = $blog;
    }

    public function blog()
    {
        return $this->blog;
    }
}

/**
 * 必须继承至 \Leevel\Event\Observer，因为系统基于 Spl 观察者模式实现的事件.
 */
abstract class TestListener extends Observer
{
}

class TestListener1 extends TestListener
{
    public function __construct()
    {
    }

    public function run($event)
    {
        $_SERVER['runtime'][] = $event->blog();
        $_SERVER['runtime'][] = 'test1';
    }
}

class TestListener2 extends TestListener
{
    public function __construct()
    {
    }

    public function run()
    {
        $_SERVER['runtime'][] = 'test2';
    }
}

class TestListener3 extends TestListener
{
    public function __construct()
    {
    }

    public function run()
    {
        $_SERVER['runtime'][] = 'test3';
    }
}
