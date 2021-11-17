<?php

declare(strict_types=1);

namespace Tests\Throttler\Middleware;

use Leevel\Cache\File;
use Leevel\Filesystem\Helper;
use Leevel\Http\Request;
use Leevel\Throttler\Middleware\Throttler as MiddlewareThrottler;
use Leevel\Throttler\Throttler;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ThrottlerTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        $dirPath = __DIR__.'/cache';
        if (is_dir($dirPath)) {
            Helper::deleteDirectory($dirPath);
        }
    }

    public function testBaseUse(): void
    {
        $throttler = $this->createThrottler();
        $middleware = new MiddlewareThrottler($throttler);

        $request = $this->createRequest('helloworld');

        $middleware->handle(function (Request $request): Response {
            $this->assertSame('127.0.0.1', $request->getClientIp());
            $this->assertSame('helloworld', $request->getBaseUrl());

            return new Response();
        }, $request, 5, 10);

        $key = sha1('127.0.0.1@helloworld');
        $path = __DIR__.'/cache';
        $this->assertTrue(is_file($path.'/'.$key.'.php'));
    }

    public function testAttempt(): void
    {
        $this->expectException(\Leevel\Kernel\Exceptions\TooManyRequestsHttpException::class);
        $this->expectExceptionMessage(
            'Too many attempts.'
        );

        $throttler = $this->createThrottler();

        $middleware = new MiddlewareThrottler($throttler);

        $request = $this->createRequest('foobar');

        $middleware->handle(function (Request $request): Response {
            $this->assertSame('127.0.0.1', $request->getClientIp());
            $this->assertSame('foobar', $request->getBaseUrl());

            return new Response();
        }, $request, 5, 10);

        for ($i = 0; $i < 10; $i++) {
            $throttler->hit();
        }

        $middleware->handle(function (Request $request): Response {
            $this->assertSame('127.0.0.1', $request->getClientIp());
            $this->assertSame('foobar', $request->getBaseUrl());

            return new Response();
        }, $request, 5, 10);
    }

    protected function createRequest(string $node): Request
    {
        $request = $this->createMock(Request::class);

        $ip = '127.0.0.1';

        $request->method('getClientIp')->willReturn($ip);
        $this->assertEquals($ip, $request->getClientIp());

        $request->method('getBaseUrl')->willReturn($node);
        $this->assertEquals($node, $request->getBaseUrl());

        return $request;
    }

    protected function createThrottler(): Throttler
    {
        $cache = new File([
            'path' => __DIR__.'/cache',
        ]);

        return new Throttler($cache);
    }
}
