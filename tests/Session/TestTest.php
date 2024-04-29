<?php

declare(strict_types=1);

namespace Tests\Session;

use Leevel\Session\ISession;
use Leevel\Session\Test;
use Tests\TestCase;

final class TestTest extends TestCase
{
    public function testBaseUse(): void
    {
        $session = $this->createTestSessionHandler();

        $this->assertInstanceof(ISession::class, $session);

        self::assertFalse($session->isStart());
        self::assertSame('', $session->getId());
        self::assertSame('UID', $session->getName());

        $session->start();
        self::assertTrue($session->isStart());

        $session->set('hello', 'world');
        self::assertSame(['hello' => 'world'], $session->all());
        self::assertTrue($session->has('hello'));
        self::assertSame('world', $session->get('hello'));

        $session->delete('hello');
        self::assertSame([], $session->all());
        self::assertFalse($session->has('hello'));
        self::assertNull($session->get('hello'));

        $session->start();
        self::assertTrue($session->isStart());
        self::assertTrue($session->open('foo', 'bar'));
        self::assertTrue($session->close());
        self::assertTrue($session->destroy('foo'));
        self::assertSame(0, $session->gc(500));
    }

    protected function createTestSessionHandler(): Test
    {
        return new Test();
    }
}
