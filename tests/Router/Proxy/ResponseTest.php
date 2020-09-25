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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Router\Proxy;

use Leevel\Di\Container;
use Leevel\Http\JsonResponse;
use Leevel\Http\Request;
use Leevel\Router\Proxy\Response as ProxyResponse;
use Leevel\Router\Redirect;
use Leevel\Router\Response as RouterResponse;
use Leevel\Router\Url;
use Leevel\Router\View;
use Leevel\View\Phpui;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ResponseTest extends TestCase
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

        $this->assertSame('hello', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $headers = $response->headers->all();
        unset($headers['date']);
        $this->assertSame(['cache-control' => ['no-cache, private']], $headers);
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

        $this->assertSame('hello', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $headers = $response->headers->all();
        unset($headers['date']);
        $this->assertSame(['cache-control' => ['no-cache, private']], $headers);

        $response = ProxyResponse::json();

        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(Response::class, $response);
        $this->assertInstanceof(JsonResponse::class, $response);

        $this->assertSame('{}', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['content-type' => ['application/json']], $this->getFilterHeaders($response->headers->all()));
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

    protected function makeView(): View
    {
        return new View(
            new Phpui([
                'theme_path' => __DIR__.'/assert',
            ])
        );
    }

    protected function makeRedirect(bool $isSecure = false): Redirect
    {
        $request = $this->makeRequest($isSecure);

        $url = new Url($request, [
            'domain' => 'queryphp.com',
        ]);

        $redirect = new Redirect($url);

        return $redirect;
    }

    protected function makeRequest(bool $isSecure = false): Request
    {
        $request = $this->createMock(Request::class);

        $request->method('getEnter')->willReturn('');
        $this->assertSame('', $request->getEnter());

        $request->method('isSecure')->willReturn($isSecure);
        $this->assertSame($isSecure, $request->isSecure($isSecure));

        return $request;
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
