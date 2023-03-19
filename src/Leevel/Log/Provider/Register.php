<?php

declare(strict_types=1);

namespace Leevel\Log\Provider;

use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Log\ILog;
use Leevel\Log\Log;
use Leevel\Log\Manager;

/**
 * 日志服务提供者.
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
    }

    /**
     * {@inheritDoc}
     */
    public static function providers(): array
    {
        return [
            'logs' => Manager::class,
            'log' => [ILog::class, Log::class],
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
            )
        ;
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
            )
        ;
    }
}
