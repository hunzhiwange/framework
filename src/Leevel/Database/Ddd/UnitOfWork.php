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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Database\Ddd;

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
    protected $entityInserts = [];

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
     * 实体当前状态
     *
     * @var array
     */
    protected $entityStates;

    /**
     * 工作单元是否关闭.
     *
     * @var bool
     */
    protected $closed = false;

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Ddd\IEntity $rootEntity
     *
     * @return $this
     */
    public function __construct(IEntity $rootEntity = null)
    {
        $this->rootEntity = $rootEntity;
    }

    /**
     * 创建一个工作单元.
     *
     * @param \Leevel\Database\Ddd\IEntity $rootEntity
     *
     * @return static
     */
    public static function make(IEntity $rootEntity = null)
    {
        return new static($rootEntity);
    }

    /**
     * 执行数据库事务.
     */
    public function flush()
    {
        $this->validateClosed();

        if (!($this->entityInserts ||
                $this->entityUpdates ||
                $this->entityDeletes)) {
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
     *
     * @return $this
     */
    public function persist(IEntity $entity)
    {
        $this->validateClosed();

        $id = spl_object_id($entity);

        $entityState = $this->getEntityState($entity, self::STATE_NEW);

        switch ($entityState) {
              case self::STATE_MANAGED:
                  break;
              case self::STATE_NEW:
                  $this->entityStates[$id] = self::STATE_MANAGED;

                  $this->insert($entity);

                  break;
              case self::STATE_REMOVED:
                  if (isset($this->entityDeletes[$id])) {
                      unset($this->entityDeletes[$id]);
                  }

                  $this->entityStates[$id] = self::STATE_MANAGED;

                  break;
             case self::STATE_DETACHED:
                  throw new InvalidArgumentException(
                      spintf('Detached entity `%s` cannot be persist.', get_class($entity))
                  );
              default:
                  throw new InvalidArgumentException(
                      sprintf('Invalid entity state `%d` of `%s`.', $entityState, get_class($entity))
                  );
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
    public function insert(IEntity $entity)
    {
        $this->validateClosed();

        $id = spl_object_id($entity);

        if (isset($this->entityUpdates[$id])) {
            throw new InvalidArgumentException(
                'Update entity can not be added for insert.'
            );
        }

        if (isset($this->entityDeletes[$id])) {
            throw new InvalidArgumentException(
                'Delete entity can not be added for insert.'
            );
        }

        if (!isset($this->entityInserts[$id])) {
            $this->entityInserts[$id] = $entity;

            if (!$this->rootEntity) {
                $this->rootEntity = $entity;
            }
        }

        return $this;
    }

    /**
     * 实体是否已经注册新增.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return bool
     */
    public function inserted(IEntity $entity): bool
    {
        return isset($this->entityInserts[spl_object_id($entity)]);
    }

    /**
     * 注册更新实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return $this
     */
    public function update(IEntity $entity)
    {
        $this->validateClosed();

        $id = spl_object_id($entity);

        if (isset($this->entityDeletes[$id])) {
            throw new InvalidArgumentException(
                'Delete entity can not be added for update.'
            );
        }

        if (isset($this->entityInserts[$id])) {
            throw new InvalidArgumentException(
                'Insert entity can not be added for update.'
            );
        }

        if (!isset($this->entityUpdates[$id])) {
            $this->entityUpdates[$id] = $entity;

            if (!$this->rootEntity) {
                $this->rootEntity = $entity;
            }
        }

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
     * 注册删除实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return $this
     */
    public function delete(IEntity $entity)
    {
        $this->validateClosed();

        $id = spl_object_id($entity);

        if (isset($this->entityInserts[$id])) {
            unset($this->entityInserts[$id], $this->entityStates[$id]);

            return $this;
        }

        if (isset($this->entityUpdates[$id])) {
            unset($this->entityUpdates[$id]);
        }

        if (!isset($this->entityDeletes[$id])) {
            $this->entityDeletes[$id] = $entity;
            $this->entityStates[$id] = self::STATE_REMOVED;

            if (!$this->rootEntity) {
                $this->rootEntity = $entity;
            }
        }

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

        return isset($this->entityInsertes[$id])
            || isset($this->entityUpdates[$id])
            || isset($this->entityDeletes[$id]);
    }

    /**
     * 刷新实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return $this
     */
    public function refresh(IEntity $entity)
    {
        $this->validateClosed();

        if (self::STATE_MANAGED !== $this->getEntityState($entity)) {
            throw new InvalidArgumentException(
                'Entity `%s` was not managed.', get_class($entity)
            );
        }

        $entity->refresh();

        return $this;
    }

    /**
     * 设置根实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $rootEntity
     *
     * @return $this
     */
    public function setRootEntity(IEntity $rootEntity)
    {
        $this->rootEntity = $rootEntity;
    }

    /**
     * 返回连接根实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function rootEntity(): ?IEntity
    {
        return $this->rootEntity;
    }

    /**
     * 返回数据库连接.
     *
     * @return \Leevel\Database\IConnect
     */
    public function connect(): IConnect
    {
        if (!$this->rootEntity) {
            throw new InvalidArgumentException('Root entity must be set before use connect.');
        }

        return $this->rootEntity->databaseConnect();
    }

    /**
     * 启动事物.
     */
    public function beginTransaction()
    {
        $this->connect()->beginTransaction();
    }

    /**
     * 事务回滚.
     */
    public function rollBack()
    {
        $this->connect()->rollBack();
    }

    /**
     * 事务自动提交.
     */
    public function commit()
    {
        $this->connect()->commit();
    }

    /**
     * 执行数据库事务
     *
     * @param \Closure $action
     *
     * @return mixed
     */
    public function transaction(Closure $action)
    {
        $this->beginTransaction();

        try {
            $result = $func($this);

            $this->flush();
            $this->commit();

            return $result;
        } catch (Throwable $e) {
            $this->close();
            $this->rollBack();

            throw $e;
        }
    }

    /**
     * 清理工作单元.
     */
    public function clear()
    {
        $this->rootEntity = null;
        $this->entityInserts = [];
        $this->entityUpdates = [];
        $this->entityDeletes = [];
        $this->entityStates = [];
    }

    /**
     * 关闭.
     */
    public function close()
    {
        $this->clear();
        $this->closed = true;
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
        if (!is_object($entity)) {
            $entity = new $entity();
        }

        if (defined(get_class($entity).'::REPOSITORY')) {
            $repositoryClass = $entity::REPOSITORY;
            $repository = new $repositoryClass($entity);
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
    public function getEntityState(IEntity $entity, int $defaults = self::STATE_NEW): int
    {
        $id = spl_object_id($entity);

        if (isset($this->entityStates[$id])) {
            return $this->entityStates[$id];
        }

        // 已经持久化数据，标识为游离状态
        if ($entity->flushed()) {
            return self::STATE_DETACHED;
        }

        return $defaults;
    }

    /**
     * 响应仓储.
     */
    protected function handleRepository()
    {
        foreach ($this->entityInserts as $entity) {
            $this->repository($entity)->create($entity);
        }

        foreach ($this->entityUpdates as $entity) {
            $this->repository($entity)->update($entity);
        }

        foreach ($this->entityDeletes as $entity) {
            $this->repository($entity)->delete($entity);
        }

        $this->entityInserts = [];
        $this->entityUpdates = [];
        $this->entityDeletes = [];
    }

    /**
     * 校验工作单元是否关闭.
     */
    protected function validateClosed()
    {
        if ($this->closed) {
            throw InvalidArgumentException('Unit of work has closed.');
        }
    }
}
