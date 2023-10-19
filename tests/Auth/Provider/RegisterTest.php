<?php

declare(strict_types=1);

namespace Tests\Auth\Provider;

use Leevel\Auth\Provider\Register;
use Leevel\Auth\Session;
use Leevel\Cache\File as CacheFile;
use Leevel\Di\Container;
use Leevel\Option\Option;
use Leevel\Session\File as SessionFile;
use Tests\TestCase;

final class RegisterTest extends TestCase
{
    public function testBaseUse(): void
    {
        $test = new Register($container = $this->createContainer());
        $test->register();
        $container->alias($test->providers());

        // auths
        $manager = $container->make('auths');
        static::assertFalse($manager->isLogin());
        static::assertSame([], $manager->getLogin());
        static::assertSame('token', $manager->login(['foo' => 'bar', 'hello' => 'world'], 10));
        static::assertTrue($manager->isLogin());
        static::assertSame(['foo' => 'bar', 'hello' => 'world'], $manager->getLogin());
        static::assertNull($manager->logout());
        static::assertFalse($manager->isLogin());
        static::assertSame([], $manager->getLogin());

        // auth
        $session = $container->make('auth');
        static::assertInstanceOf(Session::class, $session);
        static::assertFalse($session->isLogin());
        static::assertSame([], $session->getLogin());
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
        $container = new Container();

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

        return $container;
    }
}
