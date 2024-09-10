<?php

declare(strict_types=1);

namespace Tests\Kernel\Proxy;

use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Kernel\App as Apps;
use Leevel\Kernel\IApp;
use Leevel\Kernel\Proxy\App as ProxyApp;
use Tests\TestCase;

final class AppTest extends TestCase
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
        $app = $this->createAppInstance($container);
        $container->singleton('app', static function () use ($app): Apps {
            return $app;
        });

        $appPath = __DIR__.'/app';
        self::assertSame($appPath, $app->path());
        self::assertSame($appPath.'/foobar', $app->path('foobar'));
        self::assertInstanceOf(Apps::class, $app->container()->make('app'));
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $app = $this->createAppInstance($container);
        $container->singleton('app', static function () use ($app): Apps {
            return $app;
        });

        $appPath = __DIR__.'/app';
        self::assertSame($appPath, ProxyApp::proxy()->path());
        self::assertSame($appPath.'/foobar', ProxyApp::proxy()->path('foobar'));
        self::assertInstanceOf(Apps::class, ProxyApp::proxy()->make('app'));
    }

    public function testProxyNotFound(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Call to undefined method Leevel\\Kernel\\App::notFound()');

        $container = $this->createContainer();
        $app = $this->createAppInstance($container);
        $container->singleton('app', static function () use ($app): Apps {
            return $app;
        });

        ProxyApp::proxy()->notFound();
    }

    public function testProxyTypeError(): void
    {
        $this->expectException(\TypeError::class);

        $container = $this->createContainer();
        $app = $this->createAppInstance($container);
        $container->singleton('app', static function () use ($app): Apps {
            return $app;
        });

        ProxyApp::proxy()->path(11);
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
