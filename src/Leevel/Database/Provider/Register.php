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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Database\Provider;

use Leevel\Database\Database;
use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\IUnitOfWork;
use Leevel\Database\Ddd\Meta;
use Leevel\Database\Ddd\UnitOfWork;
use Leevel\Database\IDatabase;
use Leevel\Database\Manager;
use Leevel\Di\IContainer;
use Leevel\Di\Provider;
use Leevel\Event\IDispatch;

/**
 * database 服务提供者.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.12
 *
 * @version 1.0
 */
class Register extends Provider
{
    /**
     * 注册服务.
     */
    public function register(): void
    {
        $this->databases();
        $this->database();
        $this->unitofwork();
        $this->databaseLazyload();
    }

    /**
     * bootstrap.
     *
     * @param \Leevel\Event\IDispatch $event
     */
    public function bootstrap(IDispatch $event): void
    {
        $this->eventDispatch($event);

        $this->meta();
    }

    /**
     * 可用服务提供者.
     *
     * @return array
     */
    public static function providers(): array
    {
        return [
            'databases'  => Manager::class,
            'database'   => [IDatabase::class, Database::class],
            'unitofwork' => [IUnitOfWork::class, UnitOfWork::class],
            'database.lazyload',
        ];
    }

    /**
     * 是否延迟载入.
     *
     * @return bool
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
                function (IContainer $container): Manager {
                    return new Manager($container);
                },
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
                function (IContainer $container): IDatabase {
                    return $container['databases']->connect();
                },
            );
    }

    /**
     * 注册 unitofwork 服务.
     */
    protected function unitofwork(): void
    {
        $this->container
            ->bind(
                'unitofwork',
                function (): UnitOfWork {
                    return new UnitOfWork();
                },
            );
    }

    /**
     * 注册 database.lazyload 服务.
     * 仅仅用于占位，必要时用于唤醒数据库服务提供者.
     */
    protected function databaseLazyload(): void
    {
        $this->container->singleton('database.lazyload');
    }

    /**
     * 设置模型实体事件.
     *
     * @param \Leevel\Event\IDispatch $event
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
        Meta::setDatabaseResolver(function (): Manager {
            return $this->container['databases'];
        });
    }
}
