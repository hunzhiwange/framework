<?php

declare(strict_types=1);

namespace Tests\Cache\Proxy;

use Leevel\Cache\Load;
use Leevel\Cache\Proxy\Load as ProxyLoad;
use Leevel\Di\Container;
use Leevel\Filesystem\Helper;
use Tests\Cache\Pieces\Test1;
use Tests\TestCase;

class LoadTest extends TestCase
{
    protected function setUp(): void
    {
        $this->tearDown();
    }

    protected function tearDown(): void
    {
        Helper::deleteDirectory(\dirname(__DIR__).'/Pieces/cacheLoad');
        Container::singletons()->clear();
    }

    public function testBaseUse(): void
    {
        $container = $this->createContainer();
        $load = $this->createLoad($container);
        $container->singleton('cache.load', function () use ($load): Load {
            return $load;
        });

        $result = $load->data([Test1::class]);
        static::assertSame(['foo' => 'bar'], $result);
        $result = $load->data([Test1::class]);
        static::assertSame(['foo' => 'bar'], $result);
        $load->refresh([Test1::class]);
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $load = $this->createLoad($container);
        $container->singleton('cache.load', function () use ($load): Load {
            return $load;
        });

        $result = ProxyLoad::data([Test1::class]);
        static::assertSame(['foo' => 'bar'], $result);
        $result = ProxyLoad::data([Test1::class]);
        static::assertSame(['foo' => 'bar'], $result);
        ProxyLoad::refresh([Test1::class]);
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }

    protected function createLoad(Container $container): Load
    {
        return new Load($container);
    }
}
