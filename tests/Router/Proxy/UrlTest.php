<?php

declare(strict_types=1);

namespace Tests\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Http\Request;
use Leevel\Router\Proxy\Url as ProxyUrl;
use Leevel\Router\Url;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class UrlTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testBaseUse(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request);
        $this->assertInstanceof(Request::class, $url->getRequest());

        $container = $this->createContainer();
        $container->singleton('url', function () use ($url): Url {
            return $url;
        });

        // 开始不带斜线，自动添加
        static::assertSame($url->make('test/hello'), '/test/hello');
        static::assertSame($url->make('/hello-world'), '/hello-world');
    }

    public function testProxy(): void
    {
        $request = $this->makeRequest();
        $url = new Url($request);
        $this->assertInstanceof(Request::class, $url->getRequest());

        $container = $this->createContainer();
        $container->singleton('url', function () use ($url): Url {
            return $url;
        });

        // 开始不带斜线，自动添加
        static::assertSame(ProxyUrl::make('test/hello'), '/test/hello');
        static::assertSame(ProxyUrl::make('/hello-world'), '/hello-world');
    }

    protected function makeRequest(bool $isSecure = false): Request
    {
        $request = $this->createMock(Request::class);

        $request->method('getEnter')->willReturn('');
        static::assertSame('', $request->getEnter());

        $request->method('isSecure')->willReturn($isSecure);
        static::assertSame($isSecure, $request->isSecure());

        return $request;
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
