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

namespace Leevel\Database\Ddd;

use Closure;
use Leevel\Database\IDatabase;

/**
 * 工作单元接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.10.14
 *
 * @version 1.0
 */
interface IUnitOfWork
{
    /**
     * 已经被管理的实体状态.
     *
     * @var int
     */
    public const STATE_MANAGED = 1;

    /**
     * 尚未被管理的实体状态.
     *
     * @var int
     */
    public const STATE_NEW = 2;

    /**
     * 已经持久化并且脱落管理的实体状态.
     *
     * @var int
     */
    public const STATE_DETACHED = 3;

    /**
     * 被标识为删除的实体状态.
     *
     * @var int
     */
    public const STATE_REMOVED = 4;

    /**
     * 是否处于协程上下文.
     *
     * @return bool
     */
    public static function coroutineContext(): bool;

    /**
     * 创建一个工作单元.
     *
     * @param \Leevel\Database\Ddd\IEntity $rootEntity
     * @param mixed                        $connect
     *
     * @return static
     */
    public static function make(IEntity $rootEntity = null, $connect = null): self;

    /**
     * 执行数据库事务.
     */
    public function flush(): void;

    /**
     * 保持实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param string                       $method
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function persistBefore(IEntity $entity, string $method = 'save'): self;

    /**
     * 保持实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param string                       $method
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function persist(IEntity $entity, string $method = 'save'): self;

    /**
     * 保持实体到后置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param string                       $method
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function persisteAfter(IEntity $entity, string $method = 'save'): self;

    /**
     * 移除实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function removeBefore(IEntity $entity, int $priority = 500): self;

    /**
     * 移除实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function remove(IEntity $entity, int $priority = 500): self;

    /**
     * 移除实体到后置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function removeAfter(IEntity $entity, int $priority = 500): self;

    /**
     * 注册新建实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function createBefore(IEntity $entity, int $priority = 500): self;

    /**
     * 注册新建实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function create(IEntity $entity, int $priority = 500): self;

    /**
     * 注册新建实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function createAfter(IEntity $entity, int $priority = 500): self;

    /**
     * 实体是否已经注册新增.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return bool
     */
    public function created(IEntity $entity): bool;

    /**
     * 注册更新实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function updateBefore(IEntity $entity, int $priority = 500): self;

    /**
     * 注册更新实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function update(IEntity $entity, int $priority = 500): self;

    /**
     * 注册更新实体到后置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function updateAfter(IEntity $entity, int $priority = 500): self;

    /**
     * 实体是否已经注册更新.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return bool
     */
    public function updated(IEntity $entity): bool;

    /**
     * 注册不存在则新增否则更新实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priorit
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function replaceBefore(IEntity $entity, int $priority = 500): self;

    /**
     * 注册不存在则新增否则更新实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priorit
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function replace(IEntity $entity, int $priority = 500): self;

    /**
     * 注册不存在则新增否则更新实体到后置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priorit
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function replaceAfter(IEntity $entity, int $priority = 500): self;

    /**
     * 实体是否已经注册不存在则新增否则更新.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return bool
     */
    public function replaced(IEntity $entity): bool;

    /**
     * 注册删除实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function deleteBefore(IEntity $entity, int $priority = 500): self;

    /**
     * 注册删除实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function delete(IEntity $entity, int $priority = 500): self;

    /**
     * 注册删除实体到后置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function deleteAfter(IEntity $entity, int $priority = 500): self;

    /**
     * 实体是否已经注册删除.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return bool
     */
    public function deleted(IEntity $entity): bool;

    /**
     * 实体是否已经注册.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return bool
     */
    public function registered(IEntity $entity): bool;

    /**
     * 刷新实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function refresh(IEntity $entity): self;

    /**
     * 注册实体为管理状态.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     */
    public function registerManaged(IEntity $entity): void;

    /**
     * 设置根实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $rootEntity
     */
    public function setRootEntity(IEntity $rootEntity): void;

    /**
     * 设置连接.
     *
     * @param mixed $connect
     */
    public function setConnect($connect): void;

    /**
     * 返回数据库连接.
     *
     * @return \Leevel\Database\IDatabase
     */
    public function connect(): IDatabase;

    /**
     * 启动事物.
     */
    public function beginTransaction(): void;

    /**
     * 事务回滚.
     */
    public function rollBack(): void;

    /**
     * 事务自动提交.
     */
    public function commit(): void;

    /**
     * 执行数据库事务.
     *
     * @param \Closure $action
     *
     * @return mixed
     */
    public function transaction(Closure $action);

    /**
     * 清理工作单元.
     */
    public function clear(): void;

    /**
     * 关闭.
     */
    public function close(): void;

    /**
     * 取得实体仓储.
     *
     * @param \Leevel\Database\Ddd\IEntity|string $entity
     *
     * @return \Leevel\Database\Ddd\IRepository
     */
    public function repository($entity): IRepository;

    /**
     * 取得实体状态.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $defaults
     *
     * @return int
     */
    public function getEntityState(IEntity $entity, ?int $defaults = null): int;
}
