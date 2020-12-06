<?php

declare(strict_types=1);

namespace Tests\Event\Provider;

use Leevel\Di\Container;
use Leevel\Event\Observer;
use Leevel\Event\Provider\Register;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    public function testBaseUse(): void
    {
        if (isset($_SERVER['test'])) {
            unset($_SERVER['test']);
        }

        if (isset($_SERVER['event_name'])) {
            unset($_SERVER['event_name']);
        }

        $test = new Register($container = new Container());

        $test->register();

        $dispatch = $container->make('event');

        $dispatch->register('test', Listener1::class);

        $dispatch->handle('test');

        $this->assertSame($_SERVER['test'], 'hello');
        $this->assertSame($_SERVER['event_name'], 'test');

        unset($_SERVER['test'], $_SERVER['event_name']);
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
