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
use InvalidArgumentException;
use Leevel\Database\IDatabase;
use Throwable;

/**
 * 事务工作单元.
 *
 * - 事务工作单元大量参考了 Doctrine2 以及 Java Bean 的实现和设计.
 * - 最早基于 .NET 里面关于领域驱动设计代码实现，事务工作单元、仓储等概念均来源于此.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.10.14
 * @since 2018.10 参考 Doctrine2 进行一次重构
 * @since v1.0.0-beta.1@2019.04.25 增加前置后置的概念
 *
 * @version 1.0
 */
class UnitOfWork implements IUnitOfWork
{
    /**
     * 根实体.
     *
     * @var \Leevel\Database\Ddd\IEntity
     */
    protected $rootEntity;

    /**
     * 注入的新建实体.
     *
     * @var array
     */
    protected $entityCreates = [];

    /**
     * 注入的不存在则新建否则更新实体.
     *
     * @var array
     */
    protected $entityReplaces = [];

    /**
     * 注入的更新实体.
     *
     * @var array
     */
    protected $entityUpdates = [];

    /**
     * 注入的删除实体.
     *
     * @var array
     */
    protected $entityDeletes = [];

    /**
     * 注入的新建实体到前置区域的标识.
     *
     * @var array
     */
    protected $createsFlagBefore = [];

    /**
     * 注入的不存在则新建否则更新实体到前置区域的标识.
     *
     * @var array
     */
    protected $replacesFlagBefore = [];

    /**
     * 注入的更新实体到前置区域的标识.
     *
     * @var array
     */
    protected $updatesFlagBefore = [];

    /**
     * 注入的删除实体到前置区域的标识.
     *
     * @var array
     */
    protected $deletesFlagBefore = [];

    /**
     * 注入的新建实体到主区域的标识.
     *
     * @var array
     */
    protected $createsFlag = [];

    /**
     * 注入的不存在则新建否则更新实体到主区域的标识.
     *
     * @var array
     */
    protected $replacesFlag = [];

    /**
     * 注入的更新实体到主区域的标识.
     *
     * @var array
     */
    protected $updatesFlag = [];

    /**
     * 注入的删除实体到主区域的标识.
     *
     * @var array
     */
    protected $deletesFlag = [];

    /**
     * 注入的新建实体到后置区域的标识.
     *
     * @var array
     */
    protected $createsFlagAfter = [];

    /**
     * 注入的不存在则新建否则更新实体到后置区域的标识.
     *
     * @var array
     */
    protected $replacesFlagAfter = [];

    /**
     * 注入的更新实体到后置区域的标识.
     *
     * @var array
     */
    protected $updatesFlagAfter = [];

    /**
     * 注入的删除实体到后置区域的标识.
     *
     * @var array
     */
    protected $deletesFlagAfter = [];

    /**
     * 实体当前状态
     *
     * @var array
     */
    protected $entityStates = [];

    /**
     * 响应回调.
     *
     * @var \Closure[]
     */
    protected $onCallbacks = [];

    /**
     * 事务工作单元是否关闭.
     *
     * @var bool
     */
    protected $closed = false;

    /**
     * 强制删除标识.
     *
     * @var array
     */
    protected $forceDeleteFlag = [];

    /**
     * 构造函数.
     */
    public function __construct()
    {
        $this->rootEntity = new class() extends Entity {
            const TABLE = '';
            const ID = null;
            const AUTO = null;
            const STRUCT = [];
            private static $leevelConnect;

            public function setter(string $prop, $value): IEntity
            {
                $this->{$this->realProp($prop)} = $value;

                return $this;
            }

            public function getter(string $prop)
            {
                return $this->{$this->realProp($prop)};
            }

            public static function withConnect($connect): void
            {
                static::$leevelConnect = $connect;
            }

            public static function connect()
            {
                return static::$leevelConnect;
            }
        };
    }

    /**
     * 创建一个事务工作单元.
     *
     * @return static
     */
    public static function make(): IUnitOfWork
    {
        return new static();
    }

    /**
     * 执行数据库事务.
     */
    public function flush(): void
    {
        $this->validateClosed();

        if (!($this->entityCreates ||
            $this->entityUpdates ||
            $this->entityDeletes ||
            $this->entityReplaces)) {
            return;
        }

        $this->beginTransaction();

        try {
            $this->handleRepository();
            $this->commit();
        } catch (Throwable $e) {
            $this->close();
            $this->rollBack();

            throw $e;
        }
    }

    /**
     * 保持实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param string                       $method
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function persistBefore(IEntity $entity, string $method = 'save'): IUnitOfWork
    {
        return $this->persistEntity('Before', $entity, $method);
    }

    /**
     * 保持实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param string                       $method
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function persist(IEntity $entity, string $method = 'save'): IUnitOfWork
    {
        return $this->persistEntity('', $entity, $method);
    }

    /**
     * 保持实体到后置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param string                       $method
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function persistAfter(IEntity $entity, string $method = 'save'): IUnitOfWork
    {
        return $this->persistEntity('After', $entity, $method);
    }

    /**
     * 移除实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function removeBefore(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        return $this->removeEntity('Before', $entity, $priority);
    }

    /**
     * 移除实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function remove(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        return $this->removeEntity('', $entity, $priority);
    }

    /**
     * 移除实体到后置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function removeAfter(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        return $this->removeEntity('After', $entity, $priority);
    }

    /**
     * 移除实体(强制删除)到前置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function forceRemoveBefore(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->forceDeleteFlag($entity);

        return $this->removeBefore($entity, $priority);
    }

    /**
     * 移除实体(强制删除).
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function forceRemove(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->forceDeleteFlag($entity);

        return $this->remove($entity, $priority);
    }

    /**
     * 移除实体(强制删除)到后置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function forceRemoveAfter(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->forceDeleteFlag($entity);

        return $this->removeAfter($entity, $priority);
    }

    /**
     * 注册新建实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function createBefore(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->createEntity($entity);
        $this->createsFlagBefore[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册新建实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function create(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->createEntity($entity);
        $this->createsFlag[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册新建实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function createAfter(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->createEntity($entity);
        $this->createsFlagAfter[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 实体是否已经注册新增.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return bool
     */
    public function created(IEntity $entity, int $priority = 500): bool
    {
        return isset($this->entityCreates[spl_object_id($entity)]);
    }

    /**
     * 注册更新实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function updateBefore(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->updateEntity($entity);
        $this->updatesFlagBefore[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册更新实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function update(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->updateEntity($entity);
        $this->updatesFlag[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册更新实体到后置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function updateAfter(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->updateEntity($entity);
        $this->updatesFlagAfter[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 实体是否已经注册更新.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return bool
     */
    public function updated(IEntity $entity): bool
    {
        return isset($this->entityUpdates[spl_object_id($entity)]);
    }

    /**
     * 注册不存在则新增否则更新实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priorit
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function replaceBefore(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->replaceEntity($entity);
        $this->replacesFlagBefore[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册不存在则新增否则更新实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priorit
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function replace(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->replaceEntity($entity);
        $this->replacesFlag[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册不存在则新增否则更新实体到后置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priorit
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function replaceAfter(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->replaceEntity($entity);
        $this->replacesFlagAfter[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 实体是否已经注册不存在则新增否则更新.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return bool
     */
    public function replaced(IEntity $entity): bool
    {
        return isset($this->entityReplaces[spl_object_id($entity)]);
    }

    /**
     * 注册删除实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function deleteBefore(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->deleteEntity($entity);
        $this->deletesFlagBefore[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册删除实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function delete(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->deleteEntity($entity);
        $this->deletesFlag[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册删除实体到后置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function deleteAfter(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->deleteEntity($entity);
        $this->deletesFlagAfter[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册删除实体(强制删除)到前置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function forceDeleteBefore(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->forceDeleteFlag($entity);

        return $this->deleteBefore($entity, $priority);
    }

    /**
     * 注册删除实体(强制删除).
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function forceDelete(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->forceDeleteFlag($entity);

        return $this->delete($entity, $priority);
    }

    /**
     * 注册删除实体(强制删除)到后置区域.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function forceDeleteAfter(IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $this->forceDeleteFlag($entity);

        return $this->deleteAfter($entity, $priority);
    }

    /**
     * 实体是否已经注册删除.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return bool
     */
    public function deleted(IEntity $entity): bool
    {
        return isset($this->entityDeletes[spl_object_id($entity)]);
    }

    /**
     * 实体是否已经注册.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return bool
     */
    public function registered(IEntity $entity): bool
    {
        $id = spl_object_id($entity);

        return isset($this->entityCreates[$id]) ||
            isset($this->entityUpdates[$id]) ||
            isset($this->entityDeletes[$id]) ||
            isset($this->entityReplaces[$id]);
    }

    /**
     * 刷新实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function refresh(IEntity $entity): IUnitOfWork
    {
        $this->validateClosed();
        if (self::STATE_MANAGED !== $this->getEntityState($entity)) {
            $e = sprintf('Entity `%s` was not managed.', get_class($entity));

            throw new InvalidArgumentException($e);
        }
        $this->repository($entity)->refresh($entity);

        return $this;
    }

    /**
     * 注册实体为管理状态.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     */
    public function registerManaged(IEntity $entity): void
    {
        $this->entityStates[spl_object_id($entity)] = self::STATE_MANAGED;
    }

    /**
     * 设置根实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $rootEntity
     * @param null|mixed                   $connect
     */
    public function setRootEntity(IEntity $rootEntity, $connect = null): void
    {
        $this->rootEntity = $rootEntity;
        if ($connect) {
            $this->withConnect($connect);
        }
    }

    /**
     * 设置连接.
     *
     * @param mixed $connect
     */
    public function setConnect($connect): void
    {
        $this->rootEntity->withConnect($connect);
    }

    /**
     * 返回数据库连接.
     *
     * @return \Leevel\Database\IDatabase
     */
    public function connect(): IDatabase
    {
        return $this->rootEntity->select()->databaseConnect();
    }

    /**
     * 启动事物.
     */
    public function beginTransaction(): void
    {
        $this->connect()->beginTransaction();
    }

    /**
     * 事务回滚.
     */
    public function rollBack(): void
    {
        $this->connect()->rollBack();
    }

    /**
     * 事务自动提交.
     */
    public function commit(): void
    {
        $this->connect()->commit();
    }

    /**
     * 执行数据库事务.
     *
     * @param \Closure $action
     *
     * @return mixed
     */
    public function transaction(Closure $action)
    {
        $this->beginTransaction();

        try {
            $result = $action($this);
            $this->flush();
            $this->commit();

            return $result;
        } catch (Throwable $e) {
            $this->rollBack();
            $this->close();

            throw $e;
        }
    }

    /**
     * 清理事务工作单元.
     */
    public function clear(): void
    {
        $this->entityCreates = [];
        $this->entityUpdates = [];
        $this->entityDeletes = [];
        $this->entityReplaces = [];
        $this->entityStates = [];
        $this->onCallbacks = [];
        $this->createsFlagBefore = [];
        $this->createsFlag = [];
        $this->createsFlagAfter = [];
        $this->replacesFlagBefore = [];
        $this->replacesFlag = [];
        $this->replacesFlagAfter = [];
        $this->updatesFlagBefore = [];
        $this->updatesFlag = [];
        $this->updatesFlagAfter = [];
        $this->deletesFlagBefore = [];
        $this->deletesFlag = [];
        $this->deletesFlagAfter = [];
        $this->forceDeleteFlag = [];
    }

    /**
     * 关闭.
     */
    public function close(): void
    {
        $this->clear();
        $this->closed = true;
    }

    /**
     * 响应回调.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param \Closure                     $callbacks
     */
    public function on(IEntity $entity, Closure $callbacks): void
    {
        $this->onCallbacks[spl_object_id($entity)][] = $callbacks;
    }

    /**
     * 取得实体仓储.
     *
     * @param \Leevel\Database\Ddd\IEntity|string $entity
     *
     * @return \Leevel\Database\Ddd\IRepository
     */
    public function repository($entity): IRepository
    {
        if (is_string($entity)) {
            $entity = new $entity();
        }

        if (defined(get_class($entity).'::REPOSITORY')) {
            $name = $entity::REPOSITORY;
            $repository = new $name($entity);
        } else {
            $repository = new Repository($entity);
        }

        return $repository;
    }

    /**
     * 取得实体状态.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param null|int                     $defaults
     *
     * @return int
     */
    public function getEntityState(IEntity $entity, ?int $defaults = null): int
    {
        $id = spl_object_id($entity);
        if (isset($this->entityStates[$id])) {
            return $this->entityStates[$id];
        }

        if (null !== $defaults) {
            return $defaults;
        }

        if (!$entity->id()) {
            return self::STATE_NEW;
        }

        return self::STATE_DETACHED;
    }

    /**
     * 强制删除标识.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     */
    protected function forceDeleteFlag(IEntity $entity): void
    {
        $this->forceDeleteFlag[spl_object_id($entity)] = true;
    }

    /**
     * 保持实体.
     *
     * @param string                       $position
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param string                       $method
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    protected function persistEntity(string $position, IEntity $entity, string $method = 'save'): IUnitOfWork
    {
        $this->validateClosed();
        $id = spl_object_id($entity);
        $entityState = $this->getEntityState($entity, self::STATE_NEW);

        switch ($entityState) {
            case self::STATE_MANAGED:
                break;
            case self::STATE_NEW:
                $this->persistNewEntry($position, $method, $entity);

                break;
            case self::STATE_REMOVED:
                if (isset($this->entityDeletes[$id])) {
                    unset($this->entityDeletes[$id]);
                    foreach (['deletesFlagBefore', 'deletesFlag', 'deletesFlagAfter'] as $flag) {
                        if (isset($this->{$flag}[$id])) {
                            unset($this->{$flag}[$id]);
                        }
                    }
                    if (isset($this->forceDeleteFlag[$id])) {
                        unset($this->forceDeleteFlag[$id]);
                    }
                }

                $this->entityStates[$id] = self::STATE_MANAGED;

                break;
            case self::STATE_DETACHED:
            default:
                $e = sprintf('Detached entity `%s` cannot be persist.', get_class($entity));

                throw new InvalidArgumentException($e);
        }

        return $this;
    }

    /**
     * 移除实体.
     *
     * @param string                       $position
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    protected function removeEntity(string $position, IEntity $entity, int $priority = 500): IUnitOfWork
    {
        $entityState = $this->getEntityState($entity);

        switch ($entityState) {
            case self::STATE_NEW:
            case self::STATE_REMOVED:
                break;
            case self::STATE_MANAGED:
                $this->{'delete'.$position}($entity, $priority);

                break;
             case self::STATE_DETACHED:
             default:
                $e = sprintf('Detached entity `%s` cannot be remove.', get_class($entity));

                throw new InvalidArgumentException($e);
        }

        return $this;
    }

    /**
     * 注册新建实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param int                          $priority
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    protected function createEntity(IEntity $entity): IUnitOfWork
    {
        $this->validateClosed();
        $id = spl_object_id($entity);

        if (isset($this->entityUpdates[$id])) {
            $e = sprintf('Updated entity `%s` cannot be added for create.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        if (isset($this->entityDeletes[$id])) {
            $e = sprintf('Deleted entity `%s` cannot be added for create.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        if (isset($this->entityReplaces[$id])) {
            $e = sprintf('Replaced entity `%s` cannot be added for create.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        if (isset($this->entityCreates[$id])) {
            $e = sprintf('Entity `%s` cannot be added for twice.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        $this->entityCreates[$id] = $entity;
        $this->entityStates[$id] = self::STATE_MANAGED;

        return $this;
    }

    /**
     * 注册更新实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    protected function updateEntity(IEntity $entity): IUnitOfWork
    {
        $this->validateClosed();
        $id = spl_object_id($entity);

        if (!$entity->id()) {
            $e = sprintf('Entity `%s` has no identity for update.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        if (isset($this->entityDeletes[$id])) {
            $e = sprintf('Deleted entity `%s` cannot be added for update.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        if (isset($this->entityCreates[$id])) {
            $e = sprintf('Created entity `%s` cannot be added for update.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        if (isset($this->entityReplaces[$id])) {
            $e = sprintf('Replaced entity `%s` cannot be added for update.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        if (isset($this->entityUpdates[$id])) {
            $e = sprintf('Entity `%s` cannot be updated for twice.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        $this->entityUpdates[$id] = $entity;
        $this->entityStates[$id] = self::STATE_MANAGED;

        return $this;
    }

    /**
     * 注册不存在则新增否则更新实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    protected function replaceEntity(IEntity $entity): IUnitOfWork
    {
        $this->validateClosed();
        $id = spl_object_id($entity);

        if (isset($this->entityDeletes[$id])) {
            $e = sprintf('Deleted entity `%s` cannot be added for replace.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        if (isset($this->entityCreates[$id])) {
            $e = sprintf('Created entity `%s` cannot be added for replace.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        if (isset($this->entityUpdates[$id])) {
            $e = sprintf('Updated entity `%s` cannot be added for replace.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        if (isset($this->entityReplaces[$id])) {
            $e = sprintf('Entity `%s` cannot be replaced for twice.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        $this->entityReplaces[$id] = $entity;
        $this->entityStates[$id] = self::STATE_MANAGED;

        return $this;
    }

    /**
     * 注册删除实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    protected function deleteEntity(IEntity $entity): IUnitOfWork
    {
        $this->validateClosed();
        $id = spl_object_id($entity);

        if (isset($this->entityCreates[$id])) {
            unset($this->entityCreates[$id], $this->entityStates[$id]);

            foreach (['createsFlagBefore', 'createsFlag', 'createsFlagAfter'] as $flag) {
                if (isset($this->{$flag}[$id])) {
                    unset($this->{$flag}[$id]);
                }
            }

            return $this;
        }

        if (isset($this->entityReplaces[$id])) {
            unset($this->entityReplaces[$id]);

            foreach (['replacesFlagBefore', 'replacesFlag', 'replacesFlagAfter'] as $flag) {
                if (isset($this->{$flag}[$id])) {
                    unset($this->{$flag}[$id]);
                }
            }

            if (!$entity->id()) {
                return $this;
            }
        }

        if (!$entity->id()) {
            $e = sprintf('Entity `%s` has no identity for delete.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        if (isset($this->entityUpdates[$id])) {
            unset($this->entityUpdates[$id]);
            foreach (['updatesFlagBefore', 'updatesFlag', 'updatesFlagAfter'] as $flag) {
                if (isset($this->{$flag}[$id])) {
                    unset($this->{$flag}[$id]);
                }
            }
        }

        if (isset($this->entityDeletes[$id])) {
            $e = sprintf('Entity `%s` cannot be deleted for twice.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        $this->entityDeletes[$id] = $entity;
        $this->entityStates[$id] = self::STATE_REMOVED;

        return $this;
    }

    /**
     * 响应仓储.
     */
    protected function handleRepository(): void
    {
        foreach (['Before', '', 'After'] as $position) {
            foreach (['creates', 'replaces', 'updates', 'deletes'] as $type) {
                foreach ($this->normalizeRepositoryEntity($type.'Flag'.$position) as $id => $_) {
                    $this->flushRepositoryEntity($type, $id);
                    $this->entityStates[$id] = self::STATE_DETACHED;
                }
            }
        }

        $oldStates = $this->entityStates;
        $this->clear();
        $this->entityStates = $oldStates;
    }

    /**
     * 整理仓储实体.
     *
     * @param string $flag
     *
     * @return array
     */
    protected function normalizeRepositoryEntity(string $flag): array
    {
        if (!$this->{$flag}) {
            return [];
        }

        $entitys = $this->{$flag};
        asort($entitys);

        return $entitys;
    }

    /**
     * 释放仓储实体.
     *
     * @param string $type
     * @param int    $id
     */
    protected function flushRepositoryEntity(string $type, int $id): void
    {
        $entity = $this->{'entity'.ucfirst($type)}[$id];

        if ('deletes' === $type) {
            $params = [$entity, isset($this->forceDeleteFlag[$id])];
        } else {
            $params = [$entity];
        }

        $this->repository($entity)->{substr($type, 0, -1)}(...$params);

        if (isset($this->onCallbacks[$id])) {
            foreach ($this->onCallbacks[$id] as $callback) {
                $callback($entity, $this);
            }
        }
    }

    /**
     * 处理持久化.
     *
     * @param string                       $position
     * @param string                       $method
     * @param \Leevel\Database\Ddd\IEntity $entity
     */
    protected function persistNewEntry(string $position, string $method, IEntity $entity): void
    {
        switch (strtolower($method)) {
            case 'create':
                $this->{'create'.$position}($entity);

                break;
            case 'update':
                $this->{'update'.$position}($entity);

                break;
            case 'replace':
                $this->{'replace'.$position}($entity);

                break;
            case 'save':
            default:
                $ids = $entity->id();

                if (is_array($ids)) {
                    $this->{'replace'.$position}($entity);
                } else {
                    if (empty($ids)) {
                        $this->{'create'.$position}($entity);
                    } else {
                        $this->{'update'.$position}($entity);
                    }
                }

                break;
        }
    }

    /**
     * 校验事务工作单元是否关闭.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateClosed(): void
    {
        if ($this->closed) {
            $e = 'Unit of work has closed.';

            throw new InvalidArgumentException($e);
        }
    }
}
