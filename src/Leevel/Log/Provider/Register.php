<?php

declare(strict_types=1);

namespace Leevel\Log\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Log\ILog;
use Leevel\Log\Log;
use Leevel\Log\Manager;
use Leevel\Log\Middleware\Log as MiddlewareLog;

/**
 * log 服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->logs();
        $this->log();
        $this->middleware();
    }

    /**
     * {@inheritDoc}
     */
    public static function providers(): array
    {
        return [
            'logs' => Manager::class,
            'log'  => [ILog::class, Log::class],
            MiddlewareLog::class,
        ];
    }

    /**
     * 注册 logs 服务.
     */
    protected function logs(): void
    {
        $this->container
            ->singleton(
                'logs',
                fn (IContainer $container): Manager => new Manager($container),
            );
    }

    /**
     * 注册 log 服务.
     */
    protected function log(): void
    {
        $this->container
            ->singleton(
                'log',
                fn (IContainer $container): ILog => $container['logs']->connect(),
            );
    }

    /**
     * 注册 middleware 服务.
     */
    protected function middleware(): void
    {
        $this->container->singleton(MiddlewareLog::class);
    }
}
