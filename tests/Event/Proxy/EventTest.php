<?php

declare(strict_types=1);

namespace Tests\Event\Proxy;

use Leevel\Di\Container;
use Leevel\Event\Dispatch;
use Leevel\Event\Observer;
use Leevel\Event\Proxy\Event;
use Tests\TestCase;

class EventTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testBaseUse(): void
    {
        $container = $this->createContainer();
        $dispatch = $this->createDispatch($container);
        $container->singleton('event', function () use ($dispatch): Dispatch {
            return $dispatch;
        });

        if (isset($_SERVER['test'])) {
            unset($_SERVER['test']);
        }

        if (isset($_SERVER['event_name'])) {
            unset($_SERVER['event_name']);
        }

        $dispatch->register('event1', Listener1::class);
        $dispatch->handle('event1');

        $this->assertSame($_SERVER['test'], 'hello');
        $this->assertSame($_SERVER['event_name'], 'event1');

        unset($_SERVER['test'], $_SERVER['event_name']);
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $dispatch = $this->createDispatch($container);
        $container->singleton('event', function () use ($dispatch): Dispatch {
            return $dispatch;
        });

        if (isset($_SERVER['test'])) {
            unset($_SERVER['test']);
        }

        if (isset($_SERVER['event_name'])) {
            unset($_SERVER['event_name']);
        }

        Event::register('event1', Listener1::class);
        Event::handle('event1');

        $this->assertSame($_SERVER['test'], 'hello');
        $this->assertSame($_SERVER['event_name'], 'event1');

        unset($_SERVER['test'], $_SERVER['event_name']);
    }

    protected function createDispatch(Container $container): Dispatch
    {
        return new Dispatch($container);
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
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
