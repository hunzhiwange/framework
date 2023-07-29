<?php

declare(strict_types=1);

namespace Tests\Session;

use Leevel\Session\ISession;
use Leevel\Session\Test;
use Tests\TestCase;

/**
 * @internal
 */
final class TestTest extends TestCase
{
    public function testBaseUse(): void
    {
        $session = $this->createTestSessionHandler();

        $this->assertInstanceof(ISession::class, $session);

        static::assertFalse($session->isStart());
        static::assertSame('', $session->getId());
        static::assertSame('UID', $session->getName());

        $session->start();
        static::assertTrue($session->isStart());

        $session->set('hello', 'world');
        static::assertSame(['hello' => 'world'], $session->all());
        static::assertTrue($session->has('hello'));
        static::assertSame('world', $session->get('hello'));

        $session->delete('hello');
        static::assertSame([], $session->all());
        static::assertFalse($session->has('hello'));
        static::assertNull($session->get('hello'));

        $session->start();
        static::assertTrue($session->isStart());
        static::assertTrue($session->open('foo', 'bar'));
        static::assertTrue($session->close());
        static::assertTrue($session->destroy('foo'));
        static::assertSame(0, $session->gc(500));
    }

    protected function createTestSessionHandler(): Test
    {
        return new Test();
    }
}
