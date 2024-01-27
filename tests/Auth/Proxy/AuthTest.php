<?php

declare(strict_types=1);

namespace Tests\Auth\Proxy;

use Leevel\Auth\Manager;
use Leevel\Auth\Proxy\Auth;
use Leevel\Cache\File as CacheFile;
use Leevel\Config\Config;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Session\File as SessionFile;
use Tests\TestCase;

final class AuthTest extends TestCase
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
        $manager = $this->createManager($container);
        $container->singleton('auths', function () use ($manager): Manager {
            return $manager;
        });
        static::assertFalse($manager->isLogin());
        static::assertSame([], $manager->getLogin());
    }

    public function testProxy(): void
    {
        $container = $this->createContainer();
        $manager = $this->createManager($container);
        $container->singleton('auths', function () use ($manager): Manager {
            return $manager;
        });
        static::assertFalse(Auth::isLogin());
        static::assertSame([], Auth::getLogin());
    }

    protected function createManager(Container $container): Manager
    {
        $manager = new Manager($container);

        $this->assertInstanceof(IContainer::class, $manager->container());
        $this->assertInstanceof(Container::class, $manager->container());

        $config = new Config([
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

        $container->singleton('config', $config);
        $container->singleton('session', $this->createSession());

        return $manager;
    }

    protected function createSession(): SessionFile
    {
        $session = new SessionFile(new CacheFile([
            'path' => __DIR__.'/cache',
        ]));

        $session->start();

        return $session;
    }

    protected function createContainer(): Container
    {
        $container = Container::singletons();
        $container->clear();

        return $container;
    }
}
