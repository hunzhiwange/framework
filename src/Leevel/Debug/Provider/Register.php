<?php

declare(strict_types=1);

namespace Leevel\Debug\Provider;

use Leevel\Debug\Debug;
use Leevel\Debug\Middleware\Debug as MiddlewareDebug;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;

/**
 * 调试器服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->debug();
        $this->middleware();
    }

    /**
     * {@inheritDoc}
     */
    public static function providers(): array
    {
        return [
            'debug' => Debug::class,
            MiddlewareDebug::class,
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
     * 注册 debug 服务.
     */
    protected function debug(): void
    {
        $this->container
            ->singleton(
                'debug',
                // @phpstan-ignore-next-line
                fn (IContainer $container): Debug => new Debug($container, $container->make('config')->get('debug\\')),
            )
        ;
    }

    /**
     * 注册 middleware 服务.
     */
    protected function middleware(): void
    {
        $this->container->singleton(MiddlewareDebug::class);
    }
}
