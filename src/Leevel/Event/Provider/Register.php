<?php

declare(strict_types=1);

namespace Leevel\Event\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Event\Dispatch;
use Leevel\Event\IDispatch;

/**
 * 事件服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->container
            ->singleton(
                'event',
                fn (IContainer $container): Dispatch => new Dispatch($container),
            )
        ;
    }

    /**
     * {@inheritDoc}
     */
    public static function providers(): array
    {
        return [
            'event' => [IDispatch::class, Dispatch::class],
        ];
    }
}
