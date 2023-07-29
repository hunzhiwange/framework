<?php

declare(strict_types=1);

namespace Tests\Auth\Middleware;

use Leevel\Auth\Manager;
use Leevel\Auth\Middleware\Auth as MiddlewareAuth;
use Leevel\Cache\File as CacheFile;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\Request;
use Leevel\Option\Option;
use Leevel\Session\File;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @internal
 */
final class AuthTest extends TestCase
{
    public function testBaseUse(): void
    {
        $auth = $this->createManager();

        $middleware = new MiddlewareAuth($auth);

        $request = $this->createRequest('http://127.0.0.1');

        $middleware->handle(function (Request $request): Response {
            $this->assertSame('http://127.0.0.1', $request->getUri());

            return new Response();
        }, $request);
    }

    public function testAuthFailed(): void
    {
        $this->expectException(\Leevel\Auth\AuthException::class);
        $this->expectExceptionMessage(
            'User authorization failed.'
        );

        $auth = $this->createManagerNotLogin();

        $middleware = new MiddlewareAuth($auth);

        $request = $this->createRequest('http://127.0.0.1');

        $middleware->handle(function (Request $request): Response {
            $this->assertSame('http://127.0.0.1', $request->getUri());

            return new Response();
        }, $request);
    }

    protected function createRequest(string $url): Request
    {
        $request = $this->createMock(Request::class);
        $request->method('getUri')->willReturn($url);
        static::assertSame($url, $request->getUri());

        return $request;
    }

    protected function createManager(): Manager
    {
        $container = new Container();
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'auth' => [
                'default' => 'web',
                'web_default' => 'session',
                'api_default' => 'token',
                'connect' => [
                    'session' => [
                        'driver' => 'session',
                        'token' => 'token',
                    ],
                    'token' => [
                        'driver' => 'token',
                        'token' => null,
                        'input_token' => 'token',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);
        $container->singleton('session', $this->createSession());

        $manager->login(['foo' => 'bar']);

        return $manager;
    }

    protected function createManagerNotLogin(): Manager
    {
        $container = new Container();
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $option = new Option([
            'auth' => [
                'default' => 'web',
                'web_default' => 'session',
                'api_default' => 'token',
                'connect' => [
                    'session' => [
                        'driver' => 'session',
                        'token' => 'token',
                    ],
                    'token' => [
                        'driver' => 'token',
                        'token' => null,
                        'input_token' => 'token',
                    ],
                ],
            ],
        ]);

        $container->singleton('option', $option);
        $container->singleton('session', $this->createSession());

        return $manager;
    }

    protected function createSession(): File
    {
        $session = new File(new CacheFile([
            'path' => __DIR__.'/cache',
        ]));
        $session->start();

        return $session;
    }
}
