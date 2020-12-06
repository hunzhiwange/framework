<?php

declare(strict_types=1);

namespace Tests\Session;

use Leevel\Session\ISession;
use Leevel\Session\Test;
use Tests\TestCase;

class TestTest extends TestCase
{
    public function testBaseUse(): void
    {
        $session = $this->createTestSessionHandler();

        $this->assertInstanceof(ISession::class, $session);

        $this->assertFalse($session->isStart());
        $this->assertSame('', $session->getId());
        $this->assertSame('UID', $session->getName());

        $session->start();
        $this->assertTrue($session->isStart());

        $session->set('hello', 'world');
        $this->assertSame(['hello' => 'world'], $session->all());
        $this->assertTrue($session->has('hello'));
        $this->assertSame('world', $session->get('hello'));

        $session->delete('hello');
        $this->assertSame([], $session->all());
        $this->assertFalse($session->has('hello'));
        $this->assertNull($session->get('hello'));

        $session->start();
        $this->assertTrue($session->isStart());
        $this->assertTrue($session->open('foo', 'bar'));
        $this->assertTrue($session->close());
        $this->assertTrue($session->destroy('foo'));
        $this->assertSame(0, $session->gc(500));
    }

    protected function createTestSessionHandler(): Test
    {
        return new Test();
    }
}
