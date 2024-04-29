<?php

declare(strict_types=1);

namespace Leevel\Cache\Provider;

use Leevel\Cache\Cache;
use Leevel\Cache\ICache;
use Leevel\Cache\Manager;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;

/**
 * 缓存服务提供者.
 */
class Register extends Provider
{
    public function register(): void
    {
        $this->caches();
        $this->cache();
    }

    public static function providers(): array
    {
        return [
            'caches' => Manager::class,
            'cache' => [ICache::class, Cache::class],
        ];
    }

    public static function isDeferred(): bool
    {
        return true;
    }

    /**
     * 注册 caches 服务.
     */
    protected function caches(): void
    {
        $this->container
            ->singleton(
                'caches',
                static fn (IContainer $container): Manager => new Manager($container),
            )
        ;
    }

    /**
     * 注册 cache 服务.
     */
    protected function cache(): void
    {
        $this->container
            ->singleton(
                'cache',
                static fn (IContainer $container): Cache => $container['caches']->connect(),
            )
        ;
    }
}
