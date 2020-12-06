<?php

declare(strict_types=1);

namespace Tests\Event;

use Leevel\Di\Container;
use Leevel\Event\Observer;
use Leevel\Event\Subject;
use Tests\TestCase;

class SubjectTest extends TestCase
{
    public function testBaseUse(): void
    {
        $container = new Container();

        $subject = new Subject($container);

        $subject->attach($observer1 = new Observer1());

        $_SERVER['runtime'] = [];

        $subject->notify('hello');

        $this->assertSame(['hello'], $_SERVER['runtime']);

        $_SERVER['runtime'] = [];

        $subject->detach($observer1);

        $this->assertSame([], $_SERVER['runtime']);

        unset($_SERVER['runtime']);
    }
}

class Observer1 extends Observer
{
    public function handle($arg1)
    {
        $_SERVER['runtime'][] = $arg1;
    }
}
