<?php

declare(strict_types=1);

namespace Tests\Auth;

use Leevel\Auth\AuthException;
use Leevel\Auth\Session;
use Leevel\Cache\File as CacheFile;
use Leevel\Session\File;
use Tests\TestCase;

class SessionTest extends TestCase
{
    public function testBaseUse(): void
    {
        $session = new Session($this->createSession(), ['token' => 'token']);

        $this->assertFalse($session->isLogin());
        $this->assertSame([], $session->getLogin());

        $this->assertNull($session->login(['foo' => 'bar', 'hello' => 'world'], 10));

        $this->assertTrue($session->isLogin());
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $session->getLogin());

        $this->assertNull($session->logout());

        $this->assertFalse($session->isLogin());
        $this->assertSame([], $session->getLogin());
    }

    public function testTokenNameWasNotSet(): void
    {
        $this->expectException(\Leevel\Auth\AuthException::class);
        $this->expectExceptionMessage(
            'Token name was not set.'
        );

        $session = new Session($this->createSession(), ['token' => null]);

        $session->isLogin();
    }

    public function testAuthExceptionReportable(): void
    {
        $e = new AuthException();
        $this->assertFalse($e->reportable());
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
