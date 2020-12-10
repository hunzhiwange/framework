<?php

declare(strict_types=1);

namespace Leevel\Cache\Provider;

use Leevel\Cache\Cache;
use Leevel\Cache\ICache;
use Leevel\Cache\ILoad;
use Leevel\Cache\Load;
use Leevel\Cache\Manager;
use Leevel\Cache\Redis\IRedis;
use Leevel\Cache\Redis\PhpRedis;
use Leevel\Cache\Redis\RedisPool;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;

/**
 * 缓存服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->redis();
        $this->caches();
        $this->cache();
        $this->cacheLoad();
        $this->redisPool();
    }

    /**
     * {@inheritDoc}
     */
    public static function providers(): array
    {
        return [
            'redis'      => [IRedis::class, PhpRedis::class],
            'caches'     => Manager::class,
            'cache'      => [ICache::class, Cache::class],
            'cache.load' => [ILoad::class, Load::class],
            'redis.pool' => RedisPool::class,
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
     * 注册 redis 服务.
     */
    protected function redis(): void
    {
        $this->container
            ->singleton(
                'redis',
                function (IContainer $container): PhpRedis {
                    /** @var \Leevel\Option\IOption $option */
                    $option = $container->make('option');
                    $options = $option->get('cache\\connect.redis');

                    return new PhpRedis($options);
                },
            );
    }

    /**
     * 注册 caches 服务.
     */
    protected function caches(): void
    {
        $this->container
            ->singleton(
                'caches',
                fn (IContainer $container): Manager => new Manager($container),
            );
    }

    /**
     * 注册 cache 服务.
     */
    protected function cache(): void
    {
        $this->container
            ->singleton(
                'cache',
                fn (IContainer $container): Cache => $container['caches']->connect(),
            );
    }

    /**
     * 注册 cache.load 服务.
     */
    protected function cacheLoad(): void
    {
        $this->container
            ->singleton(
                'cache.load',
                fn (IContainer $container): Load => new Load($container),
            );
    }

    /**
     * 注册 redis.pool 服务.
     */
    protected function redisPool(): void
    {
        $this->container
            ->singleton(
                'redis.pool',
                function (IContainer $container): RedisPool {
                    $options = $container
                        ->make('option')
                        ->get('cache\\connect.redisPool');
                    $manager = $container->make('caches');

                    return new RedisPool($manager, $options['redis_connect'], $options);
                },
            );
    }
}
