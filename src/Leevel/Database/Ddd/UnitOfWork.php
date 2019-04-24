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
use Leevel\Database\IConnect;
use Throwable;

/**
 * 工作单元.
 * 工作单元大量参考了 Doctrine2 以及 Java Bean 的实现和设计.
 * 最早基于 .NET 里面关于领域驱动设计代码实现，工作单元、仓储等概念均来源于此.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.10.14
 * @since 2018.10 参考 Doctrine2 进行一次重构
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
     * 注入的不存在则新建否则更新实体.
     *
     * @var array
     */
    protected $entityReplaces = [];

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
     * 工作单元是否关闭.
     *
     * @var bool
     */
    protected $closed = false;

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

            public function setter(string $prop, $value): IEntity
            {
                $this->{$this->prop($prop)} = $value;

                return $this;
            }

            public function getter(string $prop)
            {
                return $this->{$this->prop($prop)};
            }
        };
    }

    /**
     * 是否处于协程上下文.
     *
     * @return bool
     */
    public static function coroutineContext(): bool
    {
        return true;
    }

    /**
     * 创建一个工作单元.
     *
     * @param \Leevel\Database\Ddd\IEntity $rootEntity
     * @param mixed                        $connect
     *
     * @return static
     */
    public static function make(IEntity $rootEntity = null, $connect = null): IUnitOfWork
    {
        return new static($rootEntity, $connect);
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
     * 保持实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param string                       $method
     *
     * @return $this
     */
    public function persist(IEntity $entity, string $method = 'save'): IUnitOfWork
    {
        $this->validateClosed();

        $id = spl_object_id($entity);

        $entityState = $this->getEntityState($entity, self::STATE_NEW);

        switch ($entityState) {
            case self::STATE_MANAGED:
                break;
            case self::STATE_NEW:
                $this->persistNewEntry($method, $entity);

                break;
            case self::STATE_REMOVED:
                if (isset($this->entityDeletes[$id])) {
                    unset($this->entityDeletes[$id]);
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
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return $this
     */
    public function remove(IEntity $entity): IUnitOfWork
    {
        $id = spl_object_id($entity);

        $entityState = $this->getEntityState($entity);

        switch ($entityState) {
            case self::STATE_NEW:
            case self::STATE_REMOVED:
                break;
            case self::STATE_MANAGED:
                $this->delete($entity);

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
     *
     * @return $this
     */
    public function create(IEntity $entity): IUnitOfWork
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
     * 实体是否已经注册新增.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return bool
     */
    public function created(IEntity $entity): bool
    {
        return isset($this->entityCreates[spl_object_id($entity)]);
    }

    /**
     * 注册更新实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return $this
     */
    public function update(IEntity $entity): IUnitOfWork
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
     * 注册不存在则新增否则更新实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return $this
     */
    public function replace(IEntity $entity): IUnitOfWork
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
     * 注册删除实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return $this
     */
    public function delete(IEntity $entity): IUnitOfWork
    {
        $this->validateClosed();

        $id = spl_object_id($entity);

        if (isset($this->entityCreates[$id])) {
            unset($this->entityCreates[$id], $this->entityStates[$id]);

            return $this;
        }

        if (!$entity->id()) {
            $e = sprintf('Entity `%s` has no identity for delete.', get_class($entity));

            throw new InvalidArgumentException($e);
        }

        if (isset($this->entityUpdates[$id])) {
            unset($this->entityUpdates[$id]);
        }

        if (isset($this->entityReplaces[$id])) {
            unset($this->entityReplaces[$id]);
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
     * @return $this
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
     */
    public function setRootEntity(IEntity $rootEntity): void
    {
        $this->rootEntity = $rootEntity;
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
     * @return \Leevel\Database\IConnect
     */
    public function connect(): IConnect
    {
        return $this->rootEntity->databaseConnect();
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
     * 清理工作单元.
     */
    public function clear(): void
    {
        $this->entityCreates = [];
        $this->entityUpdates = [];
        $this->entityDeletes = [];
        $this->entityReplaces = [];
        $this->entityStates = [];
        $this->onCallbacks = [];
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
        $this->onCallbacks[spl_object_id($entity)] = $callbacks;
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
     * @param int                          $defaults
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
     * 响应仓储.
     */
    protected function handleRepository(): void
    {
        foreach ($this->entityCreates as $entity) {
            $id = spl_object_id($entity);

            $this->repository($entity)->create($entity);

            if (isset($this->onCallbacks[$id])) {
                $this->onCallbacks[$id]($entity);
            }

            $this->entityStates[$id] = self::STATE_DETACHED;
        }

        foreach ($this->entityReplaces as $entity) {
            $id = spl_object_id($entity);

            $this->repository($entity)->replace($entity);

            if (isset($this->onCallbacks[$id])) {
                $this->onCallbacks[$id]($entity);
            }

            $this->entityStates[$id] = self::STATE_DETACHED;
        }

        foreach ($this->entityUpdates as $entity) {
            $id = spl_object_id($entity);

            $this->repository($entity)->update($entity);

            if (isset($this->onCallbacks[$id])) {
                $this->onCallbacks[$id]($entity);
            }

            $this->entityStates[$id] = self::STATE_DETACHED;
        }

        foreach ($this->entityDeletes as $entity) {
            $id = spl_object_id($entity);

            $this->repository($entity)->delete($entity);

            if (isset($this->onCallbacks[$id])) {
                $this->onCallbacks[$id]($entity);
            }
        }

        $this->entityCreates = [];
        $this->entityUpdates = [];
        $this->entityDeletes = [];
        $this->entityReplaces = [];
        $this->onCallbacks = [];
    }

    /**
     * 处理持久化.
     *
     * @param string                       $method
     * @param \Leevel\Database\Ddd\IEntity $entity
     */
    protected function persistNewEntry(string $method, IEntity $entity): void
    {
        switch (strtolower($method)) {
            case 'create':
                $this->create($entity);

                break;
            case 'update':
                $this->update($entity);

                break;
            case 'replace':
                $this->replace($entity);

                break;
            case 'save':
            default:
                $ids = $entity->id();

                if (is_array($ids)) {
                    $this->replace($entity);
                } else {
                    if (empty($ids)) {
                        $this->create($entity);
                    } else {
                        $this->update($entity);
                    }
                }

                break;
        }
    }

    /**
     * 校验工作单元是否关闭.
     */
    protected function validateClosed(): void
    {
        if ($this->closed) {
            throw new InvalidArgumentException('Unit of work has closed.');
        }
    }
}
