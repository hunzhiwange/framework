<?php

declare(strict_types=1);

namespace Tests\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Http\Request;
use Leevel\Router\Proxy\Request as ProxyRequest;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Tests\TestCase;

class RequestTest extends TestCase
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

        $this->assertInstanceOf(SymfonyRequest::class, $request);
        $this->assertSame('bar', $request->get('foo'));
        $this->assertTrue($request->exists(['foo']));
        $this->assertTrue($request->exists(['foo', 'hello']));
        $this->assertFalse($request->exists(['notFound']));
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $request = $this->createRequest();
        $container->singleton('request', function () use ($request): Request {
            return $request;
        });

        $this->assertInstanceOf(SymfonyRequest::class, $request);
        $this->assertSame('bar', ProxyRequest::get('foo'));
        $this->assertTrue(ProxyRequest::exists(['foo']));
        $this->assertTrue(ProxyRequest::exists(['foo', 'hello']));
        $this->assertFalse(ProxyRequest::exists(['notFound']));
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
