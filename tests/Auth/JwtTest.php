<?php

declare(strict_types=1);

namespace Tests\Auth;

use Leevel\Auth\Jwt;
use Leevel\Support\Arr\Except;
use Tests\TestCase;

final class JwtTest extends TestCase
{
    public function testBaseUse(): void
    {
        $token = new Jwt(['auth_key' => 'hello', 'token' => 'hello']);

        self::assertFalse($token->isLogin());
        self::assertSame([], $token->getLogin());

        $tokenResult = $token->login(['foo' => 'bar', 'hello' => 'world'], 10);
        $token->setTokenName($tokenResult);

        self::assertTrue($token->isLogin());
        $login = Except::handle($token->getLogin(), ['jwt_extend']);
        self::assertSame(['foo' => 'bar', 'hello' => 'world'], $login);

        self::assertNull($token->logout());
        // JWT 无法注销，只能重新弄一个
        $token->setTokenName($tokenResult.'notfound');
        self::assertFalse($token->isLogin());
        self::assertSame([], $token->getLogin());
    }

    public function testTokenNameWasNotSet(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Token name was not set.'
        );

        $token = new Jwt(['auth_key' => 'hello', 'token' => '']);
        $token->isLogin();
    }
}
