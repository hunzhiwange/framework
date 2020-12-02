<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Database\Provider;

use Leevel\Database\Database;
use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\Meta;
use Leevel\Database\IDatabase;
use Leevel\Database\Manager;
use Leevel\Database\Mysql\MysqlPool;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Event\IDispatch;

/**
 * database 服务提供者.
 */
class Register extends Provider
{
    /**
     * {@inheritdoc}
     */
    public function register(): void
    {
        $this->databases();
        $this->database();
        $this->databaseLazyload();
        $this->mysqlPool();
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
     * {@inheritdoc}
     */
    public static function providers(): array
    {
        return [
            'databases'          => Manager::class,
            'database'           => [IDatabase::class, Database::class],
            'database.lazyload',
            'mysql.pool'         => MysqlPool::class,
        ];
    }

    /**
     * {@inheritdoc}
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

    /**
     * 注册 mysql.pool 服务.
     */
    protected function mysqlPool(): void
    {
        $this->container
            ->singleton(
                'mysql.pool',
                function (IContainer $container): MysqlPool {
                    $options = $container
                        ->make('option')
                        ->get('database\\connect.mysqlPool');
                    $manager = $container->make('databases');

                    return new MysqlPool($manager, $options['mysql_connect'], $options);
                },
            );
    }
}
