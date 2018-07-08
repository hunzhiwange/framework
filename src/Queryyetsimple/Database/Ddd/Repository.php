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

use Exception;

/**
 * 仓储基础
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.10.14
 *
 * @version 1.0
 */
class Repository implements IRepository
{
    /**
     * 工作单元.
     *
     * @var \Leevel\Database\Ddd\IUnitOfWork
     */
    protected $unitOfWork;

    /**
     * 聚合根.
     *
     * @var \Leevel\Database\Ddd\IEntity
     */
    protected $entity;

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     */
    public function __construct(IEntity $entity)
    {
        $this->setAggregate($entity);

        $this->createUnitOfWork();
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        return $this->entity->{$method}(...$args);
    }

    /**
     * 取得一条数据.
     *
     * @param int   $id
     * @param array $column
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function find($id, $column = ['*'])
    {
        return $this->entity->find($id, $column);
    }

    /**
     * 取得一条数据，未找到记录抛出异常.
     *
     * @param int   $id
     * @param array $column
     *
     * @return \Leevel\Database\Ddd\IEntity|void
     */
    public function findOrFail($id, $column = ['*'])
    {
        return $this->entity->findOrFail($id, $column);
    }

    /**
     * 取得记录数量.
     *
     * @param null|callable $callbacks
     * @param null|mixed    $specification
     *
     * @return int
     */
    public function count($specification = null)
    {
        $select = $this->entity->selfQuerySelect();

        if (!is_string($specification) && is_callable($specification)) {
            call_user_func($specification, $select);
        }

        return $select->getCount();
    }

    /**
     * 取得所有记录.
     *
     * @param null|callable $callbacks
     * @param null|mixed    $specification
     *
     * @return \Leevel\Collection\Collection
     */
    public function all($specification = null)
    {
        $select = $this->entity->selfQuerySelect();

        if (!is_string($specification) && is_callable($specification)) {
            call_user_func($specification, $select);
        }

        return $select->getAll();
    }

    /**
     * 保存数据.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function create(IEntity $entity)
    {
        return $this->handleCreate($entity);
    }

    /**
     * 更新数据.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function update(IEntity $entity)
    {
        return $this->handleUpdate($entity);
    }

    /**
     * 删除数据.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return int
     */
    public function delete(IEntity $entity)
    {
        return $this->handleDelete($entity);
    }

    /**
     * 注册保存数据.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function registerCreate(IEntity $entity)
    {
        $this->checkUnitOfWork();

        return $this->unitOfWork->registerCreate($entity, $this);
    }

    /**
     * 注册更新数据.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function registerUpdate(IEntity $entity)
    {
        $this->checkUnitOfWork();

        return $this->unitOfWork->registerUpdate($entity, $this);
    }

    /**
     * 注册删除数据.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function registerDelete(IEntity $entity)
    {
        $this->checkUnitOfWork();

        return $this->unitOfWork->registerDelete($entity, $this);
    }

    /**
     * 响应新建.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function handleCreate(IEntity $entity)
    {
        return $entity->create();
    }

    /**
     * 响应修改.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function handleUpdate(IEntity $entity)
    {
        return $entity->update();
    }

    /**
     * 响应删除.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return int
     */
    public function handleDelete(IEntity $entity)
    {
        return $entity->delete();
    }

    /**
     * 启动事物.
     */
    public function beginTransaction()
    {
        $this->databaseConnect()->beginTransaction();
    }

    /**
     * 事务回滚.
     */
    public function rollback()
    {
        $this->databaseConnect()->rollback();
    }

    /**
     * 事务自动提交.
     */
    public function commit()
    {
        $this->databaseConnect()->commit();
    }

    /**
     * 执行数据库事务
     *
     * @param callable $action
     *
     * @return mixed
     */
    public function transaction(callable $action)
    {
        return $this->databaseConnect()->transaction($action);
    }

    /**
     * 设置聚合根.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     */
    public function setAggregate(IEntity $entity)
    {
        return $this->entity = $entity;
    }

    /**
     * 返回聚合根.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function entity()
    {
        return $this->entity;
    }

    /**
     * 返回工作单元.
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    public function unitOfWork()
    {
        return $this->unitOfWork;
    }

    /**
     * 返回数据库仓储.
     *
     * @return \Leevel\Database\IDatabase
     */
    public function databaseConnect()
    {
        return $this->entity->databaseConnect();
    }

    /**
     * 注册事务提交.
     */
    public function registerCommit()
    {
        return $this->unitOfWork->registerCommit();
    }

    /**
     * 创建设计工作单元.
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
     */
    protected function createUnitOfWork()
    {
        return $this->unitOfWork = new UnitOfWork($this);
    }

    /**
     * 验证是否设计工作单元.
     */
    protected function checkUnitOfWork()
    {
        if (!$this->unitOfWork ||
            !($this->unitOfWork instanceof IUnitOfWork)) {
            throw new Exception(
                'UnitOfWork is not set,please use '.
                    'parent::__construct( IEntity $entity ) to set.'
            );
        }
    }

    /**
     * 自定义规约处理.
     *
     * @param callbable      $callbacks
     * @param null|callbable $specification
     *
     * @return callbable
     */
    protected function specification($callbacks, $specification = null)
    {
        if (null === $specification) {
            $specification = function ($select) use ($callbacks) {
                call_user_func($callbacks, $select);
            };
        } else {
            $specification = function ($select) use ($callbacks, $specification) {
                call_user_func($callbacks, $select);

                if (!is_string($specification) && is_callable($specification)) {
                    call_user_func($specification, $select);
                }
            };
        }

        return $specification;
    }
}
