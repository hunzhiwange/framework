<?php

declare(strict_types=1);

namespace Tests\Auth;

use Leevel\Auth\AuthException;
use Leevel\Auth\Session;
use Leevel\Cache\File as CacheFile;
use Leevel\Session\File;
use Tests\TestCase;

/**
 * @internal
 */
final class SessionTest extends TestCase
{
    public function testBaseUse(): void
    {
        $session = new Session($this->createSession(), ['token' => 'token']);

        static::assertFalse($session->isLogin());
        static::assertSame([], $session->getLogin());

        static::assertSame('token', $session->login(['foo' => 'bar', 'hello' => 'world'], 10));

        static::assertTrue($session->isLogin());
        static::assertSame(['foo' => 'bar', 'hello' => 'world'], $session->getLogin());

        static::assertNull($session->logout());

        static::assertFalse($session->isLogin());
        static::assertSame([], $session->getLogin());
    }

    public function testTokenNameWasNotSet(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Token name was not set.'
        );

        $session = new Session($this->createSession(), ['token' => null]);

        $session->isLogin();
    }

    public function testAuthExceptionReportable(): void
    {
        $e = new AuthException();
        static::assertFalse($e->reportable());
    }

    protected function createSession(): File
    {
        $session = new File(new CacheFile([
            'path' => __DIR__.'/cache',
        ]));

        $session->start();

        return $session;
    }
}
