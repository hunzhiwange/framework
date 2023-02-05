<?php

declare(strict_types=1);

namespace Tests\Auth;

use Leevel\Auth\Token;
use Leevel\Cache\File;
use Leevel\Filesystem\Helper;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class TokenTest extends TestCase
{
    protected function tearDown(): void
    {
        $path = __DIR__.'/cacheFile';
        if (is_dir($path)) {
            Helper::deleteDirectory($path);
        }
    }

    public function testBaseUse(): void
    {
        $token = new Token($this->createCache());

        $token->setTokenName('token');

        static::assertFalse($token->isLogin());
        static::assertSame([], $token->getLogin());

        static::assertNull($token->login(['foo' => 'bar', 'hello' => 'world'], 10));

        static::assertTrue($token->isLogin());
        static::assertSame(['foo' => 'bar', 'hello' => 'world'], $token->getLogin());

        static::assertNull($token->logout());

        static::assertFalse($token->isLogin());
        static::assertSame([], $token->getLogin());
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
