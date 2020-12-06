<?php

declare(strict_types=1);

namespace Tests\Session\Middleware;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\Request;
use Leevel\Http\Response;
use Leevel\Option\Option;
use Leevel\Session\Manager;
use Leevel\Session\Middleware\Session as MiddlewareSession;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Tests\TestCase;

class SessionTest extends TestCase
{
    public function testBaseUse(): void
    {
        $session = $this->createSession();
        $middleware = new MiddlewareSession($session);
        $request = $this->createRequest('http://127.0.0.1');

        $this->assertNull($middleware->handle(function ($request) {
            $this->assertInstanceof(Request::class, $request);
            $this->assertSame('http://127.0.0.1', $request->getUri());
        }, $request));
    }

    public function testTerminate(): void
    {
        $session = $this->createSession();
        $middleware = new MiddlewareSession($session);
        $request = $this->createRequest('http://127.0.0.1');
        $response = $this->createResponse();

        $this->assertNull($middleware->handle(function ($request) {
            $this->assertInstanceof(Request::class, $request);
            $this->assertSame('http://127.0.0.1', $request->getUri());
        }, $request));

        $this->assertNull($middleware->terminate(function ($request, $response) {
            $this->assertInstanceof(Request::class, $request);
            $this->assertSame('http://127.0.0.1', $request->getUri());
            $this->assertInstanceof(Response::class, $response);
            $this->assertSame('content', $response->getContent());
        }, $request, $response));
    }

    public function testTerminateWithoutHandle(): void
    {
        $session = $this->createSession();
        $middleware = new MiddlewareSession($session);
        $request = $this->createRequest('http://127.0.0.1');
        $response = $this->createResponse();

        $this->assertNull($middleware->terminate(function ($request, $response) {
            $this->assertInstanceof(Request::class, $request);
            $this->assertSame('http://127.0.0.1', $request->getUri());
            $this->assertInstanceof(Response::class, $response);
            $this->assertSame('content', $response->getContent());
        }, $request, $response));
    }

    protected function createRequest(string $url): Request
    {
        $request = $this->createMock(Request::class);
        $request->cookies = new ParameterBag();
        $request->method('getUri')->willReturn($url);
        $this->assertEquals($url, $request->getUri());

        return $request;
    }

    protected function createResponse(): Response
    {
        $response = $this->createMock(Response::class);
        $response->method('getContent')->willReturn('content');
        $this->assertEquals('content', $response->getContent());
        $response->headers = new ResponseHeaderBag();

        return $response;
    }

    protected function createSession(): Manager
    {
        $container = new Container();
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'session' => [
                'default'       => 'test',
                'id'            => null,
                'name'          => 'UID',
                'expire'        => 86400,
                'connect'       => [
                    'test' => [
                        'driver' => 'test',
                    ],
                ],
            ],
        ]);
        $container->singleton('option', $option);

        return $manager;
    }
}
