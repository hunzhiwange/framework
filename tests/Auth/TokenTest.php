<?php

declare(strict_types=1);

namespace Tests\Auth;

use Leevel\Auth\Token;
use Leevel\Cache\File;
use Leevel\Filesystem\Helper;
use Leevel\Http\Request;
use Tests\TestCase;

class TokenTest extends TestCase
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
        $token = new Token($this->createCache(), $this->createRequest());

        $token->setTokenName('token');

        $this->assertFalse($token->isLogin());
        $this->assertSame([], $token->getLogin());

        $this->assertNull($token->login(['foo' => 'bar', 'hello' => 'world'], 10));

        $this->assertTrue($token->isLogin());
        $this->assertSame(['foo' => 'bar', 'hello' => 'world'], $token->getLogin());

        $this->assertNull($token->logout());

        $this->assertFalse($token->isLogin());
        $this->assertSame([], $token->getLogin());
    }

    public function testTokenNameWasNotSet(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Token name was not set.'
        );

        $token = new Token($this->createCache(), $this->createRequestWithEmptyValue());

        $token->isLogin();
    }

    protected function createCache(): File
    {
        return new File([
            'path' => __DIR__.'/cacheFile',
        ]);
    }

    protected function createRequest(): Request
    {
        $request = $this->createMock(Request::class);

        $request->method('get')->willReturn('token');
        $this->assertSame('token', $request->get('input_token'));

        return $request;
    }

    protected function createRequestWithEmptyValue(): Request
    {
        $request = $this->createMock(Request::class);

        $request->method('get')->willReturn('');
        $this->assertSame('', $request->get('input_token'));

        return $request;
    }
}
