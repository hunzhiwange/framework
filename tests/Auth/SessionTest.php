<?php

declare(strict_types=1);

namespace Tests\Auth;

use Leevel\Auth\AuthException;
use Leevel\Auth\Session;
use Leevel\Cache\File as CacheFile;
use Leevel\Session\File;
use Tests\TestCase;

final class SessionTest extends TestCase
{
    public function testBaseUse(): void
    {
        $session = new Session($this->createSession(), ['token' => 'token']);

        self::assertFalse($session->isLogin());
        self::assertSame([], $session->getLogin());

        self::assertSame('token', $session->login(['foo' => 'bar', 'hello' => 'world'], 10));

        self::assertTrue($session->isLogin());
        self::assertSame(['foo' => 'bar', 'hello' => 'world'], $session->getLogin());

        self::assertNull($session->logout());

        self::assertFalse($session->isLogin());
        self::assertSame([], $session->getLogin());
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
        self::assertFalse($e->reportable());
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
