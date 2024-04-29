<?php

declare(strict_types=1);

namespace Tests\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Http\Request;
use Leevel\Router\Proxy\Request as ProxyRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Tests\TestCase;

final class RequestTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        $this->tearDown();
    }

    #[\Override]
    protected function tearDown(): void
    {
        Container::singletons()->clear();
    }

    public function testBaseUse(): void
    {
        $container = $this->createContainer();
        $request = $this->createRequest();
        $container->singleton('request', static function () use ($request): Request {
            return $request;
        });

        self::assertInstanceOf(SymfonyRequest::class, $request);
        self::assertSame('bar', $request->get('foo'));
        self::assertTrue($request->exists(['foo']));
        self::assertTrue($request->exists(['foo', 'hello']));
        self::assertFalse($request->exists(['notFound']));
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $request = $this->createRequest();
        $container->singleton('request', static function () use ($request): Request {
            return $request;
        });

        self::assertInstanceOf(SymfonyRequest::class, $request);
        self::assertSame('bar', ProxyRequest::get('foo'));
        self::assertTrue(ProxyRequest::exists(['foo']));
        self::assertTrue(ProxyRequest::exists(['foo', 'hello']));
        self::assertFalse(ProxyRequest::exists(['notFound']));
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
