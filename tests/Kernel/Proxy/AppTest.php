<?php

declare(strict_types=1);

namespace Tests\Kernel\Proxy;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\IApp;
use Leevel\Kernel\Proxy\App as ProxyApp;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
final class AppTest extends TestCase
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
        static::assertSame($appPath, $app->path());
        static::assertSame($appPath.'/foobar', $app->path('foobar'));
        static::assertInstanceOf(Apps::class, $app->container()->make('app'));
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $app = $this->createAppInstance($container);
        $container->singleton('app', function () use ($app): Apps {
            return $app;
        });

        $appPath = __DIR__.'/app';
        static::assertSame($appPath, ProxyApp::path());
        static::assertSame($appPath.'/foobar', ProxyApp::path('foobar'));
        static::assertInstanceOf(Apps::class, ProxyApp::make('app'));
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
