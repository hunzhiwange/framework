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

namespace Tests\Kernel\Proxy;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\IApp;
use Leevel\Kernel\Proxy\App as ProxyApp;
use Tests\TestCase;

class AppTest extends TestCase
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
        $app = $this->createAppInstance($container);
        $container->singleton('app', function () use ($app): Apps {
            return $app;
        });

        $appPath = __DIR__.'/app';
        $this->assertSame($appPath, $app->path());
        $this->assertSame($appPath.'/foobar', $app->path('foobar'));
        $this->assertInstanceOf(Apps::class, $app->container()->make('app'));
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $app = $this->createAppInstance($container);
        $container->singleton('app', function () use ($app): Apps {
            return $app;
        });

        $appPath = __DIR__.'/app';
        $this->assertSame($appPath, ProxyApp::path());
        $this->assertSame($appPath.'/foobar', ProxyApp::path('foobar'));
        $this->assertInstanceOf(Apps::class, ProxyApp::make('app'));
    }

    public function testProxyNotFound(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('Method `notFound` is not exits.');

        $container = $this->createContainer();
        $app = $this->createAppInstance($container);
        $container->singleton('app', function () use ($app): Apps {
            return $app;
        });

        ProxyApp::notFound();
    }

    public function testProxyTypeError(): void
    {
        $this->expectException(\TypeError::class);

        $container = $this->createContainer();
        $app = $this->createAppInstance($container);
        $container->singleton('app', function () use ($app): Apps {
            return $app;
        });

        ProxyApp::path(11);
    }

    protected function createAppInstance(Container $container): Apps
    {
        $app = new Apps($container, __DIR__.'/app');

        $this->assertInstanceof(IContainer::class, $container);
        $this->assertInstanceof(Container::class, $container);
        $this->assertInstanceof(IApp::class, $app);
        $this->assertInstanceof(Apps::class, $app);

        return $app;
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
