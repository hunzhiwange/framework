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
use Leevel\Database\IDatabase;

/**
 * 工作单元.
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
     * 被标识为删除的实体状态.
     */
    public const STATE_REMOVED = 3;

    /**
     * 根实体.
     *
     * @var \Leevel\Database\Ddd\IEntity
     */
    protected $rootEntity;

    /**
     * 是否提交事务
     *
     * @var bool
     */
    protected $committed = false;

    /**
     * 注入的新建实体.
     *
     * @var array
     */
    protected $entitysInserts = [];

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
     * 执行数据库事务.
     *
     * @return mixed
     */
    public function flush()
    {
        if (!$this->rootEntity || $this->committed) {
            return;
        }

        $this->committed = true;

        return $this->rootEntity->databaseConnect()->transaction(function () {
            $this->handleRepository();
        });
    }

    /**
     * 注册实体.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return $this
     */
    public function register(IEntity $entity)
    {
        $visited = [];
        $this->doRegister($entity, $visited);

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
    public function isInserted(IEntity $entity): bool
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
    public function isUpdated(IEntity $entity): bool
    {
        return isset($this->entiteUpdates[spl_object_id($entity)]);
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
        $id = spl_object_id($entity);

        if (isset($this->entityInserts[$id])) {
            unset($this->entityInserts[$id], $this->entityStates[$id]);

            return;
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
    }

    /**
     * 实体是否已经注册删除.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return bool
     */
    public function isDeleted(IEntity $entity): bool
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
    public function isRegistered(IEntity $entity): bool
    {
        $id = spl_object_id($entity);

        return isset($this->entityInsertes[$id])
            || isset($this->entityUpdates[$id])
            || isset($this->entityDeletes[$id]);
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
     * 启动事物.
     */
    public function beginTransaction()
    {
        $this->connect()->beginTransaction();

        $this->committed = false;
    }

    /**
     * 事务回滚.
     */
    public function rollBack()
    {
        ${$this}->connect()->rollBack();

        $this->committed = false;
    }

    /**
     * 事务自动提交.
     */
    public function commit()
    {
        if (!$this->rootEntity || $this->committed) {
            return;
        }

        $this->connect()->commit();

        $this->committed = true;
    }

    /**
     * 是否已经提交事务
     *
     * @return bool
     */
    public function committed(): bool
    {
        return $this->committed;
    }

    /**
     * 返回数据库仓储.
     *
     * @return \Leevel\Database\IDatabase
     */
    public function connect(): IDatabase
    {
        if (!$this->rootEntity) {
            throw new InvalidArgumentException(
                'Root entity must be set before use connect.'
            );
        }

        return $this->rootEntity->databaseConnect();
    }

    /**
     * 取得实体仓储.
     *
     * @param \Leevel\Database\Ddd\IEntity|string $entity
     *
     * @return \Leevel\Database\Ddd\IRepository
     */
    public function getRepository($entity): IRepository
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
    public function getEntityState(IEntity $entity, int $defaults = self::STATE_NEW)
    {
        $id = spl_object_id($entity);

        if (isset($this->entityStates[$id])) {
            return $this->entityStates[$id];
        }

        return $defaults;
    }

    /**
     * 执行实体注册.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     * @param array                        $visited
     */
    protected function doRegister(IEntity $entity, array &$visited)
    {
        $id = spl_object_id($entity);

        if (isset($visited[$id])) {
            return;
        }

        $visited[$id] = $entity;

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
              default:
                  throw new InvalidArgumentException(
                      sprintf('Invalid entity state `%d` of `%s`.', $entityState, get_class($entity))
                  );
          }
    }

    /**
     * 响应仓储.
     */
    protected function handleRepository()
    {
        foreach ($this->entityInserts as $entity) {
            $this->getRepository($entity)->handleCreate($entity);
        }

        foreach ($this->entityUpdates as $entity) {
            $this->getRepository($entity)->handleUpdate($entity);
        }

        foreach ($this->entityDeletes as $entity) {
            $this->getRepository($entity)->handleDelete($entity);
        }
    }
}
