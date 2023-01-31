<?php

declare(strict_types=1);

namespace Leevel\Database\Provider;

use Leevel\Database\Database;
use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Meta;
use Leevel\Database\IDatabase;
use Leevel\Database\Manager;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Event\IDispatch;

/**
 * 数据库服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritDoc}
     */
    public function register(): void
    {
        $this->databases();
        $this->database();
        $this->databaseLazyload();
    }

    /**
     * bootstrap.
     */
    public function bootstrap(IDispatch $event): void
    {
        $this->eventDispatch($event);
        $this->meta();
    }

    /**
     * {@inheritDoc}
     */
    public static function providers(): array
    {
        return [
            'databases'          => Manager::class,
            'database'           => [IDatabase::class, Database::class],
            'database.lazyload',
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
     * 注册 databases 服务.
     */
    protected function databases(): void
    {
        $this->container
            ->singleton(
                'databases',
                fn (IContainer $container): Manager => new Manager($container),
            );
    }

    /**
     * 注册 database 服务.
     */
    protected function database(): void
    {
        $this->container
            ->singleton(
                'database',
                fn (IContainer $container): IDatabase => $container['databases']->connect(),
            );
    }

    /**
     * 注册 database.lazyload 服务.
     *
     * - 仅仅用于占位，必要时用于唤醒数据库服务提供者.
     */
    protected function databaseLazyload(): void
    {
        $this->container->singleton('database.lazyload');
    }

    /**
     * 设置实体事件.
     */
    protected function eventDispatch(IDispatch $event): void
    {
        Entity::withEventDispatch($event);
    }

    /**
     * Meta 设置数据库管理.
     */
    protected function meta(): void
    {
        Meta::setDatabaseResolver(fn (): Manager => $this->container['databases']);
    }
}
