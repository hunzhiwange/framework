<?php

declare(strict_types=1);

namespace Leevel\Server\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Server\IServer;
use Leevel\Server\Manager;
use Leevel\Server\Server;
use Swoole\Coroutine;
use Swoole\Coroutine\Context;

/**
 * 服务端服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->servers();
        $this->server();

        if ($this->container->enabledCoroutine()) {
            $this->setContextResolver();
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function providers(): array
    {
        return [
            'servers' => Manager::class,
            'server' => [IServer::class, Server::class],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public static function isDeferred(): bool
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public static function contextKeys(): array
    {
        return [
            'server',
        ];
    }

    /**
     * 注册 servers 服务.
     */
    protected function servers(): void
    {
        $this->container
            ->singleton(
                'servers',
                fn (IContainer $container): Manager => new Manager($container),
            )
        ;
    }

    /**
     * 注册 server 服务.
     */
    protected function server(): void
    {
        $this->container
            ->singleton(
                'server',
                // @phpstan-ignore-next-line
                fn (IContainer $container): IServer => $container['servers']->connect(),
            )
        ;
    }

    protected function setContextResolver(): void
    {
        $this->container->setContextResolver(fn (): Context => Coroutine::getContext());
    }
}
