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
use Leevel\Router\Proxy\Url as ProxyUrl;
use Leevel\Router\Url;
use Tests\TestCase;

class UrlTest extends TestCase
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
        $this->assertSame($url->make('test/hello'), '/test/hello');
        $this->assertSame($url->make('/hello-world'), '/hello-world');
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
        $this->assertSame(ProxyUrl::make('test/hello'), '/test/hello');
        $this->assertSame(ProxyUrl::make('/hello-world'), '/hello-world');
    }

    protected function makeRequest(bool $isSecure = false): Request
    {
        $request = $this->createMock(Request::class);

        $request->method('getEnter')->willReturn('');
        $this->assertSame('', $request->getEnter());

        $request->method('isSecure')->willReturn($isSecure);
        $this->assertSame($isSecure, $request->isSecure());

        return $request;
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
