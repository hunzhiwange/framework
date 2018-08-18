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

namespace Tests\Throttler\Middleware;

use Leevel\Cache\Cache;
use Leevel\Cache\File;
use Leevel\Http\IRequest;
use Leevel\Throttler\Middleware\Throttler as MiddlewareThrottler;
use Leevel\Throttler\Provider\Register;
use Leevel\Throttler\Throttler;
use Tests\TestCase;

/**
 * register test.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.15
 *
 * @version 1.0
 */
class ThrottlerTest extends TestCase
{
    protected function tearDown()
    {
        $key = sha1('127.0.0.1@foobar');
        $dirPath = __DIR__.'/cache';
        $cachePath = $dirPath.'/'.$key.'.php';

        if (is_file($cachePath)) {
            unlink($cachePath);
        }

        if (is_dir($dirPath)) {
            rmdir($dirPath);
        }
    }

    public function testBaseUse()
    {
        $throttler = $this->createThrottler();

        $middleware = new MiddlewareThrottler($throttler);

        $request = $this->createRequest('helloworld');

        $this->assertNull($middleware->handle(function ($request) {
            $this->assertInstanceof(IRequest::class, $request);
            $this->assertSame('127.0.0.1', $request->getClientIp());
            $this->assertSame('helloworld', $request->getNode());
        }, $request, 5, 10));

        $key = sha1('127.0.0.1@helloworld');

        $path = __DIR__.'/cache';

        unlink($path.'/'.$key.'.php');
    }

    public function testAttempt()
    {
        $this->expectException(\Leevel\Kernel\Exception\TooManyRequestsHttpException::class);
        $this->expectExceptionMessage(
            'Too many attempts.'
        );

        $throttler = $this->createThrottler();

        $middleware = new MiddlewareThrottler($throttler);

        $request = $this->createRequest('foobar');

        $this->assertNull($middleware->handle(function ($request) {
            $this->assertInstanceof(IRequest::class, $request);
            $this->assertSame('127.0.0.1', $request->getClientIp());
            $this->assertSame('foobar', $request->getNode());
        }, $request, 5, 10));

        for ($i = 0; $i < 10; $i++) {
            $throttler->hit();
        }

        $middleware->handle(function ($request) {
            $this->assertInstanceof(IRequest::class, $request);
            $this->assertSame('127.0.0.1', $request->getClientIp());
            $this->assertSame('foobar', $request->getNode());
        }, $request, 5, 10);
    }

    protected function createRequest(string $node): IRequest
    {
        $request = $this->createMock(IRequest::class);

        $ip = '127.0.0.1';

        $request->method('getClientIp')->willReturn($ip);
        $this->assertEquals($ip, $request->getClientIp());

        $request->method('getNode')->willReturn($node);
        $this->assertEquals($node, $request->getNode());

        return $request;
    }

    protected function createThrottler(): Throttler
    {
        $cache = new Cache(new File([
            'path' => __DIR__.'/cache',
        ]));

        return new Throttler($cache);
    }
}
