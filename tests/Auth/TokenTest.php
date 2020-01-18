<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Auth;

use Leevel\Auth\Token;
use Leevel\Cache\File;
use Leevel\Filesystem\Helper;
use Leevel\Http\IRequest;
use Tests\TestCase;

class TokenTest extends TestCase
{
    protected function tearDown(): void
    {
        $path = __DIR__.'/cacheFile';

        if (is_dir($path)) {
            Helper::deleteDirectory($path, true);
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

    public function testWithInputNotQuery(): void
    {
        $token = new Token($this->createCache(), $this->createRequestWithInput());

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
        $this->expectException(\Leevel\Auth\AuthException::class);
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

    protected function createRequest(): IRequest
    {
        $request = $this->createMock(IRequest::class);

        $request->method('query')->willReturn('token');
        $this->assertSame('token', $request->query('input_token'));

        return $request;
    }

    protected function createRequestWithInput(): IRequest
    {
        $request = $this->createMock(IRequest::class);

        $this->assertNull($request->query('input_token'));

        $request->method('input')->willReturn('token');
        $this->assertSame('token', $request->input('input_token'));

        return $request;
    }

    protected function createRequestWithEmptyValue(): IRequest
    {
        $request = $this->createMock(IRequest::class);

        $request->method('input')->willReturn('');
        $this->assertSame('', $request->input('input_token'));

        return $request;
    }
}
