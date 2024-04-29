<?php

declare(strict_types=1);

namespace Leevel\Session\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Session\ISession;
use Leevel\Session\Manager;
use Leevel\Session\Middleware\Session as MiddlewareSession;
use Leevel\Session\Session;

/**
 * Session 服务提供者.
 */
class Register extends Provider
{
    public function register(): void
    {
        $this->sessions();
        $this->session();
        $this->middleware();
    }

    public static function providers(): array
    {
        return [
            'sessions' => Manager::class,
            'session' => [ISession::class, Session::class],
            MiddlewareSession::class,
        ];
    }

    public static function isDeferred(): bool
    {
        return true;
    }

    /**
     * 注册 sessions 服务.
     */
    protected function sessions(): void
    {
        $this->container
            ->singleton(
                'sessions',
                static fn (IContainer $container): Manager => new Manager($container),
            )
        ;
    }

    /**
     * 注册 session 服务.
     */
    protected function session(): void
    {
        $this->container
            ->singleton(
                'session',
                static fn (IContainer $container): ISession => $container['sessions']->connect(),
            )
        ;
    }

    /**
     * 注册 middleware 服务.
     */
    protected function middleware(): void
    {
        $this->container->singleton(MiddlewareSession::class);
    }
}
