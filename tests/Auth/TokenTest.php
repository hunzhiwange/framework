<?php

declare(strict_types=1);

namespace Tests\Auth;

use Leevel\Auth\Token;
use Leevel\Filesystem\Helper\DeleteDirectory;
use Tests\TestCase;

final class TokenTest extends TestCase
{
    #[\Override]
    protected function tearDown(): void
    {
        $path = __DIR__.'/cacheFile';
        if (is_dir($path)) {
            DeleteDirectory::handle($path);
        }
    }

    public function testBaseUse(): void
    {
        $token = new Token($this->createCache());

        $token->setTokenName('token');

        self::assertFalse($token->isLogin());
        self::assertSame([], $token->getLogin());

        self::assertSame('token', $token->login(['foo' => 'bar', 'hello' => 'world'], 10));

        self::assertTrue($token->isLogin());
        self::assertSame(['foo' => 'bar', 'hello' => 'world'], $token->getLogin());

        self::assertNull($token->logout());

        self::assertFalse($token->isLogin());
        self::assertSame([], $token->getLogin());
    }

    public function testTokenNameWasNotSet(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Token name was not set.'
        );

        $token = new Token($this->createCache());

        $token->isLogin();
    }

    protected function createCache(): File
    {
        return new File([
            'path' => __DIR__.'/cacheFile',
        ]);
    }
}
