<?php

declare(strict_types=1);

namespace Tests\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Http\Request;
use Leevel\Router\Proxy\Request as ProxyRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Tests\TestCase;

/**
 * @internal
 */
final class RequestTest extends TestCase
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
        $container = $this->createContainer();
        $request = $this->createRequest();
        $container->singleton('request', function () use ($request): Request {
            return $request;
        });

        static::assertInstanceOf(SymfonyRequest::class, $request);
        static::assertSame('bar', $request->get('foo'));
        static::assertTrue($request->exists(['foo']));
        static::assertTrue($request->exists(['foo', 'hello']));
        static::assertFalse($request->exists(['notFound']));
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $request = $this->createRequest();
        $container->singleton('request', function () use ($request): Request {
            return $request;
        });

        static::assertInstanceOf(SymfonyRequest::class, $request);
        static::assertSame('bar', ProxyRequest::get('foo'));
        static::assertTrue(ProxyRequest::exists(['foo']));
        static::assertTrue(ProxyRequest::exists(['foo', 'hello']));
        static::assertFalse(ProxyRequest::exists(['notFound']));
    }

    protected function createRequest(): Request
    {
        return new Request(['foo' => 'bar', 'hello' => 'world']);
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
