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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Auth;

use Leevel\Auth\Token;
use Leevel\Cache\Cache;
use Leevel\Cache\File;
use Leevel\Filesystem\Fso;
use Leevel\Http\IRequest;
use Tests\TestCase;

/**
 * token test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.11.05
 *
 * @version 1.0
 */
class TokenTest extends TestCase
{
    protected function tearDown()
    {
        $path = __DIR__.'/cacheFile';

        if (is_dir($path)) {
            Fso::deleteDirectory($path, true);
        }
    }

    public function testBaseUse()
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

    public function testWithInputNotQuery()
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

    public function testTokenNameWasNotSet()
    {
        $this->expectException(\Leevel\Auth\AuthException::class);
        $this->expectExceptionMessage(
            'Token name was not set.'
        );

        $token = new Token($this->createCache(), $this->createRequestWithoutValue());

        $token->isLogin();
    }

    protected function createCache()
    {
        return new Cache(new File([
            'path' => __DIR__.'/cacheFile',
        ]));
    }

    protected function createRequest()
    {
        $request = $this->createMock(IRequest::class);

        $request->method('query')->willReturn('token');
        $this->assertSame('token', $request->query('input_token'));

        return $request;
    }

    protected function createRequestWithInput()
    {
        $request = $this->createMock(IRequest::class);

        $request->method('query')->willReturn(null);
        $this->assertNull($request->query('input_token'));

        $request->method('input')->willReturn('token');
        $this->assertSame('token', $request->input('input_token'));

        return $request;
    }

    protected function createRequestWithoutValue()
    {
        return $this->createMock(IRequest::class);
    }
}
