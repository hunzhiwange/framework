<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

use Leevel\Database\IDatabase;

/**
 * 事务工作单元.
 */
class UnitOfWork
{
    /**
     * 已经被管理的实体状态.
     */
    public const STATE_MANAGED = 1;

    /**
     * 尚未被管理的实体状态.
     */
    public const STATE_NEW = 2;

    /**
     * 已经持久化并且脱离管理的实体状态.
     */
    public const STATE_DETACHED = 3;

    /**
     * 被标识为删除的实体状态.
     */
    public const STATE_REMOVED = 4;

    /**
     * 根实体.
     */
    protected ?Entity $entity = null;

    /**
     * 数据库连接.
     */
    protected ?IDatabase $connection = null;

    /**
     * 注入的新增实体.
     */
    protected array $entityCreates = [];

    /**
     * 注入的替换实体.
     */
    protected array $entityReplaces = [];

    /**
     * 注入的更新实体.
     */
    protected array $entityUpdates = [];

    /**
     * 注入的删除实体.
     */
    protected array $entityDeletes = [];

    /**
     * 注入的新增实体到前置区域的标识.
     */
    protected array $createsFlagBefore = [];

    /**
     * 注入的替换实体到前置区域的标识.
     */
    protected array $replacesFlagBefore = [];

    /**
     * 注入的更新实体到前置区域的标识.
     */
    protected array $updatesFlagBefore = [];

    /**
     * 注入的删除实体到前置区域的标识.
     */
    protected array $deletesFlagBefore = [];

    /**
     * 注入的新增实体到主区域的标识.
     */
    protected array $createsFlag = [];

    /**
     * 注入的替换实体到主区域的标识.
     */
    protected array $replacesFlag = [];

    /**
     * 注入的更新实体到主区域的标识.
     */
    protected array $updatesFlag = [];

    /**
     * 注入的删除实体到主区域的标识.
     */
    protected array $deletesFlag = [];

    /**
     * 注入的新增实体到后置区域的标识.
     */
    protected array $createsFlagAfter = [];

    /**
     * 注入的替换实体到后置区域的标识.
     */
    protected array $replacesFlagAfter = [];

    /**
     * 注入的更新实体到后置区域的标识.
     */
    protected array $updatesFlagAfter = [];

    /**
     * 注入的删除实体到后置区域的标识.
     */
    protected array $deletesFlagAfter = [];

    /**
     * 实体当前状态
     */
    protected array $entityStates = [];

    /**
     * 响应回调.
     */
    protected array $onCallbacks = [];

    /**
     * 事务工作单元是否关闭.
     */
    protected bool $closed = false;

    /**
     * 强制删除标识.
     */
    protected array $forceDeleteFlag = [];

    /**
     * 事务执行结果.
     */
    protected array $flushResult = [];

    /**
     * 构造函数.
     *
     * - 事务工作单元大量参考了 Doctrine2 以及 Java Bean 的实现和设计.
     * - 最早基于 .NET 里面关于领域驱动设计代码实现，事务工作单元、仓储等概念均来源于此.
     */
    public function __construct(?Entity $entity = null)
    {
        if ($entity) {
            $this->setEntity($entity);
        }
    }

    /**
     * 创建一个事务工作单元.
     */
    public static function make(?Entity $entity = null): static
    {
        return new static($entity);
    }

    /**
     * 执行数据库事务.
     */
    public function flush(): void
    {
        $this->validateClosed();

        if (!($this->entityCreates
            || $this->entityUpdates
            || $this->entityDeletes
            || $this->entityReplaces)) {
            return;
        }

        $this->beginTransaction();

        try {
            $this->processingEntities();
            $this->commit();
        } catch (\Throwable $e) {
            $this->rollBack();

            throw $e;
        }
    }

    /**
     * 获取事务执行结果.
     */
    public function getFlushResult(Entity|\Closure $entity): mixed
    {
        return $this->flushResult[spl_object_id($entity)] ?? null;
    }

    /**
     * 保持实体到前置区域.
     */
    public function persistBefore(Entity|\Closure $entity, string $method = 'save'): self
    {
        return $this->persistEntity('Before', $entity, $method);
    }

    /**
     * 保持实体.
     */
    public function persist(Entity|\Closure $entity, string $method = 'save'): self
    {
        return $this->persistEntity('', $entity, $method);
    }

    /**
     * 保持实体到后置区域.
     */
    public function persistAfter(Entity|\Closure $entity, string $method = 'save'): self
    {
        return $this->persistEntity('After', $entity, $method);
    }

    /**
     * 移除实体到前置区域.
     *
     * - 已经被管理的实体直接清理管理状态，但是不做删除然后直接返回
     * - 未被管理的实体和已删除的实体不做任何处理直接返回
     */
    public function removeBefore(Entity|\Closure $entity, int $priority = 500): self
    {
        return $this->removeEntity('Before', $entity, $priority);
    }

    /**
     * 移除实体.
     *
     * - 已经被管理的实体直接清理管理状态，但是不做删除然后直接返回
     * - 未被管理的实体和已删除的实体不做任何处理直接返回
     */
    public function remove(Entity|\Closure $entity, int $priority = 500): self
    {
        return $this->removeEntity('', $entity, $priority);
    }

    /**
     * 移除实体到后置区域.
     *
     * - 已经被管理的实体直接清理管理状态，但是不做删除然后直接返回
     * - 未被管理的实体和已删除的实体不做任何处理直接返回
     */
    public function removeAfter(Entity|\Closure $entity, int $priority = 500): self
    {
        return $this->removeEntity('After', $entity, $priority);
    }

    /**
     * 强制移除实体到前置区域.
     *
     * - 已经被管理的实体直接清理管理状态，但是不做删除然后直接返回
     * - 未被管理的实体和已删除的实体不做任何处理直接返回
     */
    public function forceRemoveBefore(Entity|\Closure $entity, int $priority = 500): self
    {
        $this->forceDeleteFlag($entity);

        return $this->removeBefore($entity, $priority);
    }

    /**
     * 强制移除实体.
     *
     * - 已经被管理的实体直接清理管理状态，但是不做删除然后直接返回
     * - 未被管理的实体和已删除的实体不做任何处理直接返回
     */
    public function forceRemove(Entity|\Closure $entity, int $priority = 500): self
    {
        $this->forceDeleteFlag($entity);

        return $this->remove($entity, $priority);
    }

    /**
     * 强制移除实体到后置区域.
     *
     * - 已经被管理的实体直接清理管理状态，但是不做删除然后直接返回
     * - 未被管理的实体和已删除的实体不做任何处理直接返回
     */
    public function forceRemoveAfter(Entity|\Closure $entity, int $priority = 500): self
    {
        $this->forceDeleteFlag($entity);

        return $this->removeAfter($entity, $priority);
    }

    /**
     * 注册新增实体到前置区域.
     */
    public function createBefore(Entity|\Closure $entity, int $priority = 500): self
    {
        $this->createEntity($entity);
        $this->createsFlagBefore[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册新增实体.
     */
    public function create(Entity|\Closure $entity, int $priority = 500): self
    {
        $this->createEntity($entity);
        $this->createsFlag[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册新增实体到前置区域.
     */
    public function createAfter(Entity|\Closure $entity, int $priority = 500): self
    {
        $this->createEntity($entity);
        $this->createsFlagAfter[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 实体是否已经注册新增.
     */
    public function created(Entity|\Closure $entity): bool
    {
        return isset($this->entityCreates[spl_object_id($entity)]);
    }

    /**
     * 注册更新实体到前置区域.
     */
    public function updateBefore(Entity|\Closure $entity, int $priority = 500): self
    {
        $this->updateEntity($entity);
        $this->updatesFlagBefore[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册更新实体.
     */
    public function update(Entity|\Closure $entity, int $priority = 500): self
    {
        $this->updateEntity($entity);
        $this->updatesFlag[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册更新实体到后置区域.
     */
    public function updateAfter(Entity|\Closure $entity, int $priority = 500): self
    {
        $this->updateEntity($entity);
        $this->updatesFlagAfter[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 实体是否已经注册更新.
     */
    public function updated(Entity|\Closure $entity): bool
    {
        return isset($this->entityUpdates[spl_object_id($entity)]);
    }

    /**
     * 注册替换实体到前置区域.
     */
    public function replaceBefore(Entity|\Closure $entity, int $priority = 500): self
    {
        $this->replaceEntity($entity);
        $this->replacesFlagBefore[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册替换实体.
     */
    public function replace(Entity|\Closure $entity, int $priority = 500): self
    {
        $this->replaceEntity($entity);
        $this->replacesFlag[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 注册替换实体到后置区域.
     */
    public function replaceAfter(Entity|\Closure $entity, int $priority = 500): self
    {
        $this->replaceEntity($entity);
        $this->replacesFlagAfter[spl_object_id($entity)] = $priority;

        return $this;
    }

    /**
     * 实体是否已经注册不存在则新增否则更新.
     */
    public function replaced(Entity|\Closure $entity): bool
    {
        return isset($this->entityReplaces[spl_object_id($entity)]);
    }

    /**
     * 注册删除实体到前置区域.
     */
    public function deleteBefore(Entity|\Closure $entity, int $priority = 500): self
    {
        return $this->deleteEntity($entity, 'Before', $priority);
    }

    /**
     * 注册删除实体.
     */
    public function delete(Entity|\Closure $entity, int $priority = 500): self
    {
        return $this->deleteEntity($entity, '', $priority);
    }

    /**
     * 注册删除实体到后置区域.
     */
    public function deleteAfter(Entity|\Closure $entity, int $priority = 500): self
    {
        return $this->deleteEntity($entity, 'After', $priority);
    }

    /**
     * 注册删除实体(强制删除)到前置区域.
     */
    public function forceDeleteBefore(Entity|\Closure $entity, int $priority = 500): self
    {
        $this->forceDeleteFlag($entity);

        return $this->deleteBefore($entity, $priority);
    }

    /**
     * 注册删除实体(强制删除).
     */
    public function forceDelete(Entity|\Closure $entity, int $priority = 500): self
    {
        $this->forceDeleteFlag($entity);

        return $this->delete($entity, $priority);
    }

    /**
     * 注册删除实体(强制删除)到后置区域.
     */
    public function forceDeleteAfter(Entity|\Closure $entity, int $priority = 500): self
    {
        $this->forceDeleteFlag($entity);

        return $this->deleteAfter($entity, $priority);
    }

    /**
     * 实体是否已经注册删除.
     */
    public function deleted(Entity|\Closure $entity): bool
    {
        return isset($this->entityDeletes[spl_object_id($entity)]);
    }

    /**
     * 实体是否已经注册.
     */
    public function registered(Entity|\Closure $entity): bool
    {
        $id = spl_object_id($entity);

        return isset($this->entityCreates[$id])
            || isset($this->entityUpdates[$id])
            || isset($this->entityDeletes[$id])
            || isset($this->entityReplaces[$id]);
    }

    /**
     * 刷新实体.
     *
     * @throws \InvalidArgumentException
     */
    public function refresh(Entity $entity): self
    {
        if (self::STATE_MANAGED !== $this->getEntityState($entity)) {
            throw new \InvalidArgumentException(sprintf('Entity `%s` was not managed.', $entity::class));
        }
        $this->repository($entity)->refreshEntity($entity);

        return $this;
    }

    /**
     * 设置实体.
     */
    public function setEntity(?Entity $entity = null): self
    {
        $this->entity = $entity;
        $this->connection = null;

        return $this;
    }

    /**
     * 返回数据库连接.
     */
    public function connect(): IDatabase
    {
        if (!$this->entity) {
            $this->initDefaultEntity();
        }

        if ($this->connection) {
            return $this->connection;
        }

        // @phpstan-ignore-next-line
        return $this->connection = $this->entity->select()->databaseConnect();
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
        $this->close();
    }

    /**
     * 事务自动提交.
     */
    public function commit(): void
    {
        $this->connect()->commit();
        $this->close();
    }

    /**
     * 执行数据库事务.
     */
    public function transaction(\Closure $action): mixed
    {
        $this->create($closure = function () use ($action): void {
            $action($this);
        });

        $this->flush();

        return $this->getFlushResult($closure);
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
        $this->closed = true;
        $this->entity = null;
        $this->connection = null;
    }

    /**
     * 实体回调.
     */
    public function on(Entity|\Closure $entity, \Closure $callbacks): void
    {
        $this->onCallbacks[spl_object_id($entity)][] = $callbacks;
    }

    /**
     * 取得实体仓储.
     */
    public function repository(Entity|string $entity): Repository
    {
        return $entity::repository();
    }

    /**
     * 取得实体状态.
     */
    public function getEntityState(Entity|\Closure $entity, ?int $defaults = null): int
    {
        $id = spl_object_id($entity);
        if (isset($this->entityStates[$id])) {
            return $this->entityStates[$id];
        }

        if (null !== $defaults) {
            return $defaults;
        }

        return self::STATE_NEW;
    }

    protected function initDefaultEntity(): void
    {
        $this->entity = new class() extends Entity {
            public const TABLE = '';
            public const ID = null;
            public const AUTO = null;
        };
    }

    protected function setDefaultEntity(null|Entity|\Closure $entity): void
    {
        if (null === $entity && $this->entity) {
            $this->setEntity();
        } elseif (!$this->entity && $entity instanceof Entity) {
            $this->entity = $entity;
        }
    }

    /**
     * 强制删除标识.
     */
    protected function forceDeleteFlag(Entity $entity): void
    {
        $this->forceDeleteFlag[spl_object_id($entity)] = true;
    }

    /**
     * 保持实体.
     *
     * @throws \InvalidArgumentException
     */
    protected function persistEntity(string $position, Entity|\Closure $entity, string $method = 'save'): self
    {
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
                unset($this->entityStates[$id]);

                break;

            case self::STATE_DETACHED:
            default:
                throw new \InvalidArgumentException(sprintf('Detached entity `%s` cannot be persist.', $entity::class));
        }

        return $this;
    }

    /**
     * 移除实体.
     *
     * @throws \InvalidArgumentException
     */
    protected function removeEntity(string $position, Entity|\Closure $entity, int $priority = 500): self
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
                throw new \InvalidArgumentException(sprintf('Detached entity `%s` cannot be remove.', $entity::class));
        }

        return $this;
    }

    /**
     * 注册新增实体.
     *
     * @throws \InvalidArgumentException
     */
    protected function createEntity(Entity|\Closure $entity): self
    {
        $id = spl_object_id($entity);
        $this->validateUpdateAlreadyExists($entity, 'create');
        $this->validateDeleteAlreadyExists($entity, 'create');
        $this->validateReplaceAlreadyExists($entity, 'create');

        if (isset($this->entityCreates[$id])) {
            throw new \InvalidArgumentException(sprintf('Entity `%s` cannot be added for twice.', $entity::class));
        }

        $this->entityCreates[$id] = $entity;
        $this->entityStates[$id] = self::STATE_MANAGED;
        $this->setDefaultEntity($entity);

        return $this;
    }

    /**
     * 注册更新实体.
     *
     * @throws \InvalidArgumentException
     */
    protected function updateEntity(Entity|\Closure $entity): self
    {
        $id = spl_object_id($entity);
        if ($entity instanceof Entity) {
            $this->validateUniqueKeyData($entity, 'update');
        }
        $this->validateDeleteAlreadyExists($entity, 'update');
        $this->validateCreateAlreadyExists($entity, 'update');
        $this->validateReplaceAlreadyExists($entity, 'update');

        if (isset($this->entityUpdates[$id])) {
            throw new \InvalidArgumentException(sprintf('Entity `%s` cannot be updated for twice.', $entity::class));
        }

        $this->entityUpdates[$id] = $entity;
        $this->entityStates[$id] = self::STATE_MANAGED;
        $this->setDefaultEntity($entity);

        return $this;
    }

    /**
     * 注册替换实体.
     *
     * @throws \InvalidArgumentException
     */
    protected function replaceEntity(Entity|\Closure $entity): self
    {
        $id = spl_object_id($entity);
        $this->validateDeleteAlreadyExists($entity, 'replace');
        $this->validateCreateAlreadyExists($entity, 'replace');
        $this->validateUpdateAlreadyExists($entity, 'replace');

        if (isset($this->entityReplaces[$id])) {
            throw new \InvalidArgumentException(sprintf('Entity `%s` cannot be replaced for twice.', $entity::class));
        }

        $this->entityReplaces[$id] = $entity;
        $this->entityStates[$id] = self::STATE_MANAGED;
        $this->setDefaultEntity($entity);

        return $this;
    }

    /**
     * 注册删除实体.
     *
     * @throws \InvalidArgumentException
     */
    protected function deleteEntity(Entity|\Closure $entity, string $position, int $priority = 500, bool $remove = false): self
    {
        $id = spl_object_id($entity);

        if (isset($this->entityCreates[$id])) {
            unset($this->entityCreates[$id], $this->entityStates[$id]);
            foreach (['createsFlagBefore', 'createsFlag', 'createsFlagAfter'] as $flag) {
                if (isset($this->{$flag}[$id])) {
                    unset($this->{$flag}[$id]);
                }
            }

            if ($remove) {
                if ($this->entity && spl_object_id($this->entity) === $id) {
                    $this->setDefaultEntity(null);
                }

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

            if ($remove) {
                if ($this->entity && spl_object_id($this->entity) === $id) {
                    $this->setDefaultEntity(null);
                }

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

            if ($remove) {
                if ($this->entity && spl_object_id($this->entity) === $id) {
                    $this->setDefaultEntity(null);
                }

                return $this;
            }
        }

        if ($entity instanceof Entity) {
            $this->validateUniqueKeyData($entity, 'delete');
        }

        if (isset($this->entityDeletes[$id])) {
            throw new \InvalidArgumentException(sprintf('Entity `%s` cannot be deleted for twice.', $entity::class));
        }

        $this->{'deletesFlag'.$position}[spl_object_id($entity)] = $priority;
        $this->entityDeletes[$id] = $entity;
        $this->entityStates[$id] = self::STATE_REMOVED;
        $this->setDefaultEntity($entity);

        return $this;
    }

    /**
     * 校验是否已经为新增实体.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateCreateAlreadyExists(Entity|\Closure $entity, string $type): void
    {
        if (isset($this->entityCreates[spl_object_id($entity)])) {
            throw new \InvalidArgumentException(sprintf('Created entity `%s` cannot be added for %s.', $entity::class, $type));
        }
    }

    /**
     * 校验是否已经为更新实体.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateUpdateAlreadyExists(Entity|\Closure $entity, string $type): void
    {
        if (isset($this->entityUpdates[spl_object_id($entity)])) {
            throw new \InvalidArgumentException(sprintf('Updated entity `%s` cannot be added for %s.', $entity::class, $type));
        }
    }

    /**
     * 校验是否已经为替换实体.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateReplaceAlreadyExists(Entity|\Closure $entity, string $type): void
    {
        if (isset($this->entityReplaces[spl_object_id($entity)])) {
            throw new \InvalidArgumentException(sprintf('Replaced entity `%s` cannot be added for %s.', $entity::class, $type));
        }
    }

    /**
     * 校验是否已经为删除实体.
     *
     * @throws \InvalidArgumentException
     */
    protected function validateDeleteAlreadyExists(Entity|\Closure $entity, string $type): void
    {
        if (isset($this->entityDeletes[spl_object_id($entity)])) {
            throw new \InvalidArgumentException(sprintf('Deleted entity `%s` cannot be added for %s.', $entity::class, $type));
        }
    }

    /**
     * 校验实体主键值.
     *
     * - 闭包为虚拟实体不用检查唯一键
     *
     * @throws \InvalidArgumentException
     */
    protected function validateUniqueKeyData(Entity $entity, string $type): void
    {
        if (false === $entity->id()) {
            throw new \InvalidArgumentException(sprintf('Entity `%s` has no unique key data for %s.', $entity::class, $type));
        }
    }

    /**
     * 处理实体数据.
     */
    protected function processingEntities(): void
    {
        $remainingUnprocessedEntities = true;
        while ($remainingUnprocessedEntities) {
            $remainingUnprocessedEntities = $this->persistEntitiesThroughRepository();
        }
    }

    /**
     * 通过仓储持久化实体.
     */
    protected function persistEntitiesThroughRepository(): bool
    {
        $remainingUnprocessedEntities = false;
        foreach (['Before', '', 'After'] as $position) {
            foreach (['creates', 'replaces', 'updates', 'deletes'] as $type) {
                foreach ($this->normalizeRepositoryEntity($type.'Flag'.$position) as $id => $_) {
                    if (self::STATE_DETACHED === $this->entityStates[$id]) {
                        continue;
                    }

                    $this->flushRepositoryEntity($type, $id);
                    $this->entityStates[$id] = self::STATE_DETACHED;
                    $remainingUnprocessedEntities = true;
                }
            }
        }

        return $remainingUnprocessedEntities;
    }

    /**
     * 整理仓储实体.
     */
    protected function normalizeRepositoryEntity(string $flag): array
    {
        if (!$this->{$flag}) {
            return [];
        }

        $entities = $this->{$flag};
        asort($entities);

        return $entities;
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

        // 闭包为虚拟实体
        if ($entity instanceof \Closure) {
            $this->flushResult[$id] = $entity(...$params);
        } else {
            $this->flushResult[$id] = $this->repository($entity)->{substr($type, 0, -1).'Entity'}(...$params);
        }

        if (isset($this->onCallbacks[$id])) {
            foreach ($this->onCallbacks[$id] as $callback) {
                $callback($entity, $this->flushResult[$id], $this);
            }
        }
    }

    /**
     * 处理持久化.
     */
    protected function persistNewEntry(string $position, string $method, Entity|\Closure $entity): void
    {
        switch (strtolower($method)) {
            case 'create':
                $this->{'create'.$position}($entity);

                break;

            case 'update':
                $this->{'update'.$position}($entity);

                break;

            case 'replace':
            case 'save':
            default:
                $this->{'replace'.$position}($entity);

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
            throw new \InvalidArgumentException('Unit of work has closed.');
        }
    }
}
