<?php

declare(strict_types=1);

namespace Tests\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Http\JsonResponse;
use Leevel\Http\Request;
use Leevel\Kernel\App;
use Leevel\Option\Option;
use Leevel\Router\Proxy\Response as ProxyResponse;
use Leevel\Router\Redirect;
use Leevel\Router\Response as RouterResponse;
use Leevel\Router\Url;
use Leevel\View\IView;
use Leevel\View\Manager;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * @internal
 */
final class ResponseTest extends TestCase
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
        $view = $this->makeView();
        $redirect = $this->makeRedirect();
        $container = $this->createContainer();
        $factory = new RouterResponse($view, $redirect);
        $container->singleton('response', function () use ($factory): RouterResponse {
            return $factory;
        });

        $response = $factory->make('hello');

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        static::assertSame('hello', $response->getContent());
        static::assertSame(200, $response->getStatusCode());
        $headers = $response->headers->all();
        unset($headers['date']);
        static::assertSame(['cache-control' => ['no-cache, private']], $headers);
    }

    public function testProxy(): void
    {
        $view = $this->makeView();
        $redirect = $this->makeRedirect();
        $container = $this->createContainer();
        $factory = new RouterResponse($view, $redirect);
        $container->singleton('response', function () use ($factory): RouterResponse {
            return $factory;
        });

        $response = ProxyResponse::make('hello');

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);

        static::assertSame('hello', $response->getContent());
        static::assertSame(200, $response->getStatusCode());
        $headers = $response->headers->all();
        unset($headers['date']);
        static::assertSame(['cache-control' => ['no-cache, private']], $headers);

        $response = ProxyResponse::json();

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        static::assertSame('{}', $response->getContent());
        static::assertSame(200, $response->getStatusCode());
        static::assertSame(['content-type' => ['application/json']], $this->getFilterHeaders($response->headers->all()));
    }

    protected function createRequest(): Request
    {
        return new Request(['foo' => 'bar', 'hello' => 'world']);
    }

    protected function getFilterHeaders(array $headers): array
    {
        if (isset($headers['date'])) {
            unset($headers['date']);
        }

        if (isset($headers['cache-control'])) {
            unset($headers['cache-control']);
        }

        return $headers;
    }

    protected function makeView(): IView
    {
        return $this->createViewManager('phpui')->connect('phpui');
    }

    protected function makeRedirect(bool $isSecure = false): Redirect
    {
        $request = $this->makeRequest($isSecure);

        $url = new Url($request, [
            'domain' => 'queryphp.com',
        ]);

        return new Redirect($url);
    }

    protected function makeRequest(bool $isSecure = false): Request
    {
        $request = $this->createMock(Request::class);

        $request->method('getEnter')->willReturn('');
        static::assertSame('', $request->getEnter());

        $request->method('isSecure')->willReturn($isSecure);
        static::assertSame($isSecure, $request->isSecure($isSecure));

        return $request;
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }

    protected function createViewManager(string $connect = 'html'): Manager
    {
        $app = new ExtendAppForResponse($container = new Container(), '');
        $container->instance('app', $app);

        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        static::assertSame(__DIR__.'/assert', $app->themesPath());
        static::assertSame(__DIR__.'/cache_theme', $app->storagePath('theme'));

        $option = new Option([
            'view' => [
                'default' => $connect,
                'action_fail' => 'public/fail',
                'action_success' => 'public/success',
                'connect' => [
                    'html' => [
                        'driver' => 'html',
                        'suffix' => '.html',
                    ],
                    'phpui' => [
                        'driver' => 'phpui',
                        'suffix' => '.php',
                    ],
                ],
            ],
        ]);
        $container->singleton('option', $option);

        $request = new ExtendRequestForResponse();
        $container->singleton('request', $request);

        return $manager;
    }
}

class ExtendAppForResponse extends App
{
    public function development(): bool
    {
        return true;
    }

    public function themesPath(string $path = ''): string
    {
        return __DIR__.'/assert';
    }

    public function storagePath(string $path = ''): string
    {
        return __DIR__.'/cache_'.$path;
    }
}

class ExtendRequestForResponse
{
}
