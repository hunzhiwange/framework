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
 */
class UnitOfWork
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
     * 根实体.
     *
     * @var \Leevel\Database\Ddd\Entity
     */
    protected Entity $rootEntity;

    /**
     * 注入的新建实体.
     *
     * @var array
     */
    protected array $entityCreates = [];

    /**
     * 注入的不存在则新建否则更新实体.
     *
     * @var array
     */
    protected array $entityReplaces = [];

    /**
     * 注入的更新实体.
     *
     * @var array
     */
    protected array $entityUpdates = [];

    /**
     * 注入的删除实体.
     *
     * @var array
     */
    protected array $entityDeletes = [];

    /**
     * 注入的新建实体到前置区域的标识.
     *
     * @var array
     */
    protected array $createsFlagBefore = [];

    /**
     * 注入的不存在则新建否则更新实体到前置区域的标识.
     *
     * @var array
     */
    protected array $replacesFlagBefore = [];

    /**
     * 注入的更新实体到前置区域的标识.
     *
     * @var array
     */
    protected array $updatesFlagBefore = [];

    /**
     * 注入的删除实体到前置区域的标识.
     *
     * @var array
     */
    protected array $deletesFlagBefore = [];

    /**
     * 注入的新建实体到主区域的标识.
     *
     * @var array
     */
    protected array $createsFlag = [];

    /**
     * 注入的不存在则新建否则更新实体到主区域的标识.
     *
     * @var array
     */
    protected array $replacesFlag = [];

    /**
     * 注入的更新实体到主区域的标识.
     *
     * @var array
     */
    protected array $updatesFlag = [];

    /**
     * 注入的删除实体到主区域的标识.
     *
     * @var array
     */
    protected array $deletesFlag = [];

    /**
     * 注入的新建实体到后置区域的标识.
     *
     * @var array
     */
    protected array $createsFlagAfter = [];

    /**
     * 注入的不存在则新建否则更新实体到后置区域的标识.
     *
     * @var array
     */
    protected array $replacesFlagAfter = [];

    /**
     * 注入的更新实体到后置区域的标识.
     *
     * @var array
     */
    protected array $updatesFlagAfter = [];

    /**
     * 注入的删除实体到后置区域的标识.
     *
     * @var array
     */
    protected array $deletesFlagAfter = [];

    /**
     * 实体当前状态
     *
     * @var array
     */
    protected array $entityStates = [];

    /**
     * 响应回调.
     *
     * @var \Closure[]
     */
    protected array $onCallbacks = [];

    /**
     * 事务工作单元是否关闭.
     *
     * @var bool
     */
    protected bool $closed = false;

    /**
     * 强制删除标识.
     *
     * @var array
     */
    protected array $forceDeleteFlag = [];

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
            private array $data = [];
            private static ?string $connect = null;

            /**
             * @codeCoverageIgnore
             *
             * @param mixed $value
             */
            public function setter(string $prop, $value): Entity
            {
                $this->data[$this->realProp($prop)] = $value;

                return $this;
            }

            /**
             * @codeCoverageIgnore
             */
            public function getter(string $prop)
            {
                return $this->data[$this->realProp($prop)] ?? null;
            }

            public static function withConnect(?string $connect = null): void
            {
                static::$connect = $connect;
            }

            public static function connect(): string
            {
                return static::$connect;
            }
        };
    }

    /**
     * 创建一个事务工作单元.
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public static function make(): self
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
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function persistBefore(Entity $entity, string $method = 'save'): self
    {
        return $this->persistEntity('Before', $entity, $method);
    }

    /**
     * 保持实体.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function persist(Entity $entity, string $method = 'save'): self
    {
        return $this->persistEntity('', $entity, $method);
    }

    /**
     * 保持实体到后置区域.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function persistAfter(Entity $entity, string $method = 'save'): self
    {
        return $this->persistEntity('After', $entity, $method);
    }

    /**
     * 移除实体到前置区域.
     *
     * - 已经被管理的实体直接清理管理状态，但是不做删除然后直接返回
     * - 未被管理的实体为直接删除
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function removeBefore(Entity $entity, int $priority = 500): self
    {
        return $this->removeEntity('Before', $entity, $priority);
    }

    /**
     * 移除实体.
     *
     * - 已经被管理的实体直接清理管理状态，但是不做删除然后直接返回
     * - 未被管理的实体为直接删除
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function remove(Entity $entity, int $priority = 500): self
    {
        return $this->removeEntity('', $entity, $priority);
    }

    /**
     * 移除实体到后置区域.
     *
     * - 已经被管理的实体直接清理管理状态，但是不做删除然后直接返回
     * - 未被管理的实体为直接删除
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function removeAfter(Entity $entity, int $priority = 500): self
    {
        return $this->removeEntity('After', $entity, $priority);
    }

    /**
     * 移除实体(强制删除)到前置区域.
     *
     * - 已经被管理的实体直接清理管理状态，但是不做删除然后直接返回
     * - 未被管理的实体为直接删除
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function forceRemoveBefore(Entity $entity, int $priority = 500): self
    {
        $this->forceDeleteFlag($entity);

        return $this->removeBefore($entity, $priority);
    }

    /**
     * 移除实体(强制删除).
     *
     * - 已经被管理的实体直接清理管理状态，但是不做删除然后直接返回
     * - 未被管理的实体为直接删除
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function forceRemove(Entity $entity, int $priority = 500): self
    {
        $this->forceDeleteFlag($entity);

        return $this->remove($entity, $priority);
    }

    /**
     * 移除实体(强制删除)到后置区域.
     *
     * - 已经被管理的实体直接清理管理状态，但是不做删除然后直接返回
     * - 未被管理的实体为直接删除
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function forceRemoveAfter(Entity $entity, int $priority = 500): self
    {
        $this->forceDeleteFlag($entity);

        return $this->removeAfter($entity, $priority);
    }

    /**
     * 注册新建实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function createBefore(Entity $entity, int $priority = 500): self
    {
        $this->createEntity($entity);
        $this->createsFlagBefore[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册新建实体.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function create(Entity $entity, int $priority = 500): self
    {
        $this->createEntity($entity);
        $this->createsFlag[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册新建实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function createAfter(Entity $entity, int $priority = 500): self
    {
        $this->createEntity($entity);
        $this->createsFlagAfter[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 实体是否已经注册新增.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     */
    public function created(Entity $entity, int $priority = 500): bool
    {
        return isset($this->entityCreates[spl_object_id($entity)]);
    }

    /**
     * 注册更新实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function updateBefore(Entity $entity, int $priority = 500): self
    {
        $this->updateEntity($entity);
        $this->updatesFlagBefore[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册更新实体.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function update(Entity $entity, int $priority = 500): self
    {
        $this->updateEntity($entity);
        $this->updatesFlag[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册更新实体到后置区域.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function updateAfter(Entity $entity, int $priority = 500): self
    {
        $this->updateEntity($entity);
        $this->updatesFlagAfter[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 实体是否已经注册更新.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     */
    public function updated(Entity $entity): bool
    {
        return isset($this->entityUpdates[spl_object_id($entity)]);
    }

    /**
     * 注册不存在则新增否则更新实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function replaceBefore(Entity $entity, int $priority = 500): self
    {
        $this->replaceEntity($entity);
        $this->replacesFlagBefore[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册不存在则新增否则更新实体.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function replace(Entity $entity, int $priority = 500): self
    {
        $this->replaceEntity($entity);
        $this->replacesFlag[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册不存在则新增否则更新实体到后置区域.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function replaceAfter(Entity $entity, int $priority = 500): self
    {
        $this->replaceEntity($entity);
        $this->replacesFlagAfter[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 实体是否已经注册不存在则新增否则更新.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     */
    public function replaced(Entity $entity): bool
    {
        return isset($this->entityReplaces[spl_object_id($entity)]);
    }

    /**
     * 注册删除实体到前置区域.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function deleteBefore(Entity $entity, int $priority = 500): self
    {
        return $this->deleteEntity($entity, 'Before', $priority);
    }

    /**
     * 注册删除实体.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function delete(Entity $entity, int $priority = 500): self
    {
        return $this->deleteEntity($entity, '', $priority);
    }

    /**
     * 注册删除实体到后置区域.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function deleteAfter(Entity $entity, int $priority = 500): self
    {
        return $this->deleteEntity($entity, 'After', $priority);
    }

    /**
     * 注册删除实体(强制删除)到前置区域.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function forceDeleteBefore(Entity $entity, int $priority = 500): self
    {
        $this->forceDeleteFlag($entity);

        return $this->deleteBefore($entity, $priority);
    }

    /**
     * 注册删除实体(强制删除).
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function forceDelete(Entity $entity, int $priority = 500): self
    {
        $this->forceDeleteFlag($entity);

        return $this->delete($entity, $priority);
    }

    /**
     * 注册删除实体(强制删除)到后置区域.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function forceDeleteAfter(Entity $entity, int $priority = 500): self
    {
        $this->forceDeleteFlag($entity);

        return $this->deleteAfter($entity, $priority);
    }

    /**
     * 实体是否已经注册删除.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     */
    public function deleted(Entity $entity): bool
    {
        return isset($this->entityDeletes[spl_object_id($entity)]);
    }

    /**
     * 实体是否已经注册.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     */
    public function registered(Entity $entity): bool
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
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function refresh(Entity $entity): self
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
     * @param \Leevel\Database\Ddd\Entity $entity
     */
    public function registerManaged(Entity $entity): void
    {
        $this->entityStates[spl_object_id($entity)] = self::STATE_MANAGED;
    }

    /**
     * 设置根实体.
     *
     * @param \Leevel\Database\Ddd\Entity $rootEntity
     * @param null|mixed                  $connect
     */
    public function setRootEntity(Entity $rootEntity, $connect = null): void
    {
        $this->rootEntity = $rootEntity;
        if (null !== $connect) {
            $this->setConnect($connect);
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
     * @param \Leevel\Database\Ddd\Entity $entity
     */
    public function on(Entity $entity, Closure $callbacks): void
    {
        $this->onCallbacks[spl_object_id($entity)][] = $callbacks;
    }

    /**
     * 取得实体仓储.
     *
     * @param \Leevel\Database\Ddd\Entity|string $entity
     *
     * @return \Leevel\Database\Ddd\Repository
     */
    public function repository($entity): Repository
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
     * @param \Leevel\Database\Ddd\Entity $entity
     */
    public function getEntityState(Entity $entity, ?int $defaults = null): int
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
     * @param \Leevel\Database\Ddd\Entity $entity
     */
    protected function forceDeleteFlag(Entity $entity): void
    {
        $this->forceDeleteFlag[spl_object_id($entity)] = true;
    }

    /**
     * 保持实体.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    protected function persistEntity(string $position, Entity $entity, string $method = 'save'): self
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
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    protected function removeEntity(string $position, Entity $entity, int $priority = 500): self
    {
        $entityState = $this->getEntityState($entity);

        switch ($entityState) {
            case self::STATE_NEW:
            case self::STATE_REMOVED:
                break;
            case self::STATE_MANAGED:
                $this->deleteEntity($entity, $position, $priority, true);

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
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    protected function createEntity(Entity $entity): self
    {
        $this->validateClosed();
        $id = spl_object_id($entity);
        $this->validateUpdateAlreadyExists($entity, 'create');
        $this->validateDeleteAlreadyExists($entity, 'create');
        $this->validateReplaceAlreadyExists($entity, 'create');

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
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    protected function updateEntity(Entity $entity): self
    {
        $this->validateClosed();
        $id = spl_object_id($entity);
        $this->validatePrimaryData($entity, 'update');
        $this->validateDeleteAlreadyExists($entity, 'update');
        $this->validateCreateAlreadyExists($entity, 'update');
        $this->validateReplaceAlreadyExists($entity, 'update');

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
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    protected function replaceEntity(Entity $entity): self
    {
        $this->validateClosed();
        $id = spl_object_id($entity);
        $this->validateDeleteAlreadyExists($entity, 'replace');
        $this->validateCreateAlreadyExists($entity, 'replace');
        $this->validateUpdateAlreadyExists($entity, 'replace');

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
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @throws \InvalidArgumentException
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    protected function deleteEntity(Entity $entity, string $position, int $priority = 500, bool $remove = false): self
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

            if (true === $remove) {
                return $this;
            }
        }

        if (isset($this->entityReplaces[$id])) {
            unset($this->entityReplaces[$id], $this->entityStates[$id]);
            foreach (['replacesFlagBefore', 'replacesFlag', 'replacesFlagAfter'] as $flag) {
                if (isset($this->{$flag}[$id])) {
                    unset($this->{$flag}[$id]);
                }
            }

            if (true === $remove) {
                return $this;
            }
        }

        if (isset($this->entityUpdates[$id])) {
            unset($this->entityUpdates[$id], $this->entityStates[$id]);
            foreach (['updatesFlagBefore', 'updatesFlag', 'updatesFlagAfter'] as $flag) {
                if (isset($this->{$flag}[$id])) {
                    unset($this->{$flag}[$id]);
                }
            }

            if (true === $remove) {
                return $this;
            }
        }

        $this->validatePrimaryData($entity, 'delete');

        if (isset($this->entityDeletes[$id])) {
            $e = sprintf('Entity `%s` cannot be deleted for twice.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        $this->{'deletesFlag'.$position}[spl_object_id($entity)] = $priority;
        $this->entityDeletes[$id] = $entity;
        $this->entityStates[$id] = self::STATE_REMOVED;

        return $this;
    }

    /**
     * 校验是否已经为新建实体.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @throws \InvalidArgumentException
     */
    protected function validateCreateAlreadyExists(Entity $entity, string $type): void
    {
        if (isset($this->entityCreates[spl_object_id($entity)])) {
            $e = sprintf('Created entity `%s` cannot be added for %s.', get_class($entity), $type);

            throw new InvalidArgumentException($e);
        }
    }

    /**
     * 校验是否已经为更新实体.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @throws \InvalidArgumentException
     */
    protected function validateUpdateAlreadyExists(Entity $entity, string $type): void
    {
        if (isset($this->entityUpdates[spl_object_id($entity)])) {
            $e = sprintf('Updated entity `%s` cannot be added for %s.', get_class($entity), $type);

            throw new InvalidArgumentException($e);
        }
    }

    /**
     * 校验是否已经为不存在则新增否则更新实体.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @throws \InvalidArgumentException
     */
    protected function validateReplaceAlreadyExists(Entity $entity, string $type): void
    {
        if (isset($this->entityReplaces[spl_object_id($entity)])) {
            $e = sprintf('Replaced entity `%s` cannot be added for %s.', get_class($entity), $type);

            throw new InvalidArgumentException($e);
        }
    }

    /**
     * 校验是否已经为删除实体.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @throws \InvalieletentException
     */
    protected function validateDeleteAlreadyExists(Entity $entity, string $type): void
    {
        if (isset($this->entityDeletes[spl_object_id($entity)])) {
            $e = sprintf('Deleted entity `%s` cannot be added for %s.', get_class($entity), $type);

            throw new InvalidArgumentException($e);
        }
    }

    /**
     * 校验实体主键值.
     *
     * @param \Leevel\Database\Ddd\Entity $entity
     *
     * @throws \InvalidArgumentException
     */
    protected function validatePrimaryData(Entity $entity, string $type): void
    {
        if (!$entity->id()) {
            $e = sprintf('Entity `%s` has no identity for %s.', get_class($entity), $type);

            throw new InvalidArgumentException($e);
        }
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
     * @param \Leevel\Database\Ddd\Entity $entity
     */
    protected function persistNewEntry(string $position, string $method, Entity $entity): void
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
