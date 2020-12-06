<?php

declare(strict_types=1);

namespace Tests\Event;

use Leevel\Di\Container;
use Leevel\Event\Dispatch;
use Leevel\Event\EventProvider;
use Leevel\Event\Observer;
use Tests\TestCase;

class EventProviderTest extends TestCase
{
    public function testBaseUse(): void
    {
        $container = new Container();

        $provider = new TestEventProvider($container);

        $dispatch = new Dispatch($container);

        $this->assertNull($provider->register());

        $provider->bootstrap($dispatch);

        $_SERVER['runtime'] = [];

        $dispatch->handle(new TestEvent('hello blog'));

        $this->assertSame(['test3', 'test2', 'hello blog', 'test1'], $_SERVER['runtime']);

        unset($_SERVER['runtime']);
    }
}

class TestEventProvider extends EventProvider
{
    protected array $listeners = [
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

abstract class TestListener extends Observer
{
}

class TestListener1 extends TestListener
{
    public function __construct()
    {
    }

    public function handle($event)
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

    public function handle()
    {
        $_SERVER['runtime'][] = 'test2';
    }
}

class TestListener3 extends TestListener
{
    public function __construct()
    {
    }

    public function handle()
    {
        $_SERVER['runtime'][] = 'test3';
    }
}
