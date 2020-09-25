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
