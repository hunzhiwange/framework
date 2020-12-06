<?php

declare(strict_types=1);

namespace Leevel\Throttler\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Throttler\IThrottler;
use Leevel\Throttler\Middleware\Throttler as MiddlewareThrottler;
use Leevel\Throttler\Throttler;

/**
 * throttler 服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->throttler();
        $this->middleware();
    }

    /**
     * {@inheritDoc}
     */
    public static function providers(): array
    {
        return [
            'throttler' => [IThrottler::class, Throttler::class],
            MiddlewareThrottler::class,
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
     * 注册 throttler 服务.
     */
    protected function throttler(): void
    {
        $this->container
            ->singleton(
                'throttler',
                function (IContainer $container): Throttler {
                    $cache = $container['caches']
                        ->connect($container['option']['throttler\\driver']);

                    return (new Throttler($cache))
                        ->setRequest($container['request']);
                },
            );
    }

    /**
     * 注册 middleware 服务.
     */
    protected function middleware(): void
    {
        $this->container->singleton(MiddlewareThrottler::class);
    }
}
