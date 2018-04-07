<?php
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
namespace Leevel\Mvc;

use Exception;

/**
 * 仓储基础
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.10.14
 * @version 1.0
 */
class Repository implements IRepository
{

    /**
     * 工作单元
     *
     * @var \Leevel\Mvc\IUnitOfWork
     */
    protected $objUnitOfWork;

    /**
     * 聚合根
     *
     * @var \Leevel\Mvc\IAggregateRoot
     */
    protected $objAggregate;

    /**
     * 构造函数
     *
     * @param \Leevel\Mvc\IAggregateRoot $objAggregate
     * @return void
     */
    public function __construct(IAggregateRoot $objAggregate)
    {
        $this->setAggregate($objAggregate);
        $this->createUnitOfWork();
    }

    /**
     * 取得一条数据
     *
     * @param int $intId
     * @param array $arrColumn
     * @return \Leevel\Mvc\IEntity
     */
    public function find($intId, $arrColumn = ['*'])
    {
        return $this->objAggregate->find($intId, $arrColumn);
    }

    /**
     * 取得一条数据，未找到记录抛出异常
     *
     * @param int $intId
     * @param array $arrColumn
     * @return \Leevel\Mvc\IEntity|void
     */
    public function findOrFail($intId, $arrColumn = ['*'])
    {
        return $this->objAggregate->findOrFail($intId, $arrColumn);
    }

    /**
     * 取得所有记录
     *
     * @param null|callback $mixCallback
     * @return \Leevel\Collection\Collection
     */
    public function count($mixSpecification = null)
    {
        $objSelect = $this->objAggregate->selfQuerySelect();

        if (! is_string($mixSpecification) && is_callable($mixSpecification)) {
            call_user_func($mixSpecification, $objSelect);
        }

        return $objSelect->getCount();
    }

    /**
     * 取得所有记录
     *
     * @param null|callback $mixCallback
     * @return \Leevel\Collection\Collection
     */
    public function all($mixSpecification = null)
    {
        $objSelect = $this->objAggregate->selfQuerySelect();

        if (! is_string($mixSpecification) && is_callable($mixSpecification)) {
            call_user_func($mixSpecification, $objSelect);
        }

        return $objSelect->getAll();
    }

    /**
     * 保存数据
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     * @return \Leevel\Mvc\IAggregateRoot
     */
    public function create(IAggregateRoot $objEntity)
    {
        return $this->handleCreate($objEntity);
    }

    /**
     * 更新数据
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     * @return \Leevel\Mvc\IAggregateRoot
     */
    public function update(IAggregateRoot $objEntity)
    {
        return $this->handleUpdate($objEntity);
    }

    /**
     * 删除数据
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     * @return int
     */
    public function delete(IAggregateRoot $objEntity)
    {
        return $this->handleDelete($objEntity);
    }

    /**
     * 注册保存数据
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     * @return \Leevel\Mvc\UnitOfWork
     */
    public function registerCreate(IAggregateRoot $objEntity)
    {
        $this->checkUnitOfWork();
        return $this->objUnitOfWork->registerCreate($objEntity, $this);
    }

    /**
     * 注册更新数据
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     * @return \Leevel\Mvc\UnitOfWork
     */
    public function registerUpdate(IAggregateRoot $objEntity)
    {
        $this->checkUnitOfWork();
        return $this->objUnitOfWork->registerUpdate($objEntity, $this);
    }

    /**
     * 注册删除数据
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     * @return \Leevel\Mvc\UnitOfWork
     */
    public function registerDelete(IAggregateRoot $objEntity)
    {
        $this->checkUnitOfWork();
        return $this->objUnitOfWork->registerDelete($objEntity, $this);
    }

    /**
     * 响应新建
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     * @return \Leevel\Mvc\IAggregateRoot
     */
    public function handleCreate(IAggregateRoot $objEntity)
    {
        return $objEntity->create();
    }

    /**
     * 响应修改
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     * @return \Leevel\Mvc\IAggregateRoot
     */
    public function handleUpdate(IAggregateRoot $objEntity)
    {
        return $objEntity->update();
    }

    /**
     * 响应删除
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     * @return int
     */
    public function handleDelete(IAggregateRoot $objEntity)
    {
        return $objEntity->delete();
    }

    /**
     * 启动事物
     *
     * @return void
     */
    public function beginTransaction()
    {
        $this->databaseConnect()->beginTransaction();
    }

    /**
     * 事务回滚
     *
     * @return void
     */
    public function rollback()
    {
        $this->databaseConnect()->rollback();
    }

    /**
     * 事务自动提交
     *
     * @return void
     */
    public function commit()
    {
        $this->databaseConnect()->commit();
    }

    /**
     * 执行数据库事务
     *
     * @param callable $calAction
     * @return mixed
     */
    public function transaction($calAction)
    {
        return $this->databaseConnect()->transaction($calAction);
    }

    /**
     * 设置聚合根
     *
     * @param \Leevel\Mvc\IAggregateRoot $objAggregate
     * @return void
     */
    public function setAggregate(IAggregateRoot $objAggregate)
    {
        return $this->objAggregate = $objAggregate;
    }

    /**
     * 返回聚合根
     *
     * @return \Leevel\Mvc\IAggregateRoot
     */
    public function aggregate()
    {
        return $this->objAggregate;
    }

    /**
     * 返回工作单元
     *
     * @return \Leevel\Mvc\IUnitOfWork
     */
    public function unitOfWork()
    {
        return $this->objUnitOfWork;
    }

    /**
     * 返回数据库仓储
     *
     * @return \Leevel\Database\IDatabase
     */
    public function databaseConnect()
    {
        return $this->objAggregate->databaseConnect();
    }

    /**
     * 注册事务提交
     *
     * @return void
     */
    public function registerCommit()
    {
        return $this->objUnitOfWork->registerCommit();
    }

    /**
     * 创建设计工作单元
     *
     * @return \Leevel\Mvc\IUnitOfWork
     */
    protected function createUnitOfWork()
    {
        return $this->objUnitOfWork = new UnitOfWork($this);
    }

    /**
     * 验证是否设计工作单元
     *
     * @return void
     */
    protected function checkUnitOfWork()
    {
        if (! $this->objUnitOfWork || ! ($this->objUnitOfWork instanceof IUnitOfWork)) {
            throw new Exception('UnitOfWork is not set,please use parent::__construct( IAggregateRoot $objAggregate ) to set.');
        }
    }

    /**
     * 自定义规约处理
     *
     * @param callbable $mixCallback
     * @param callbable|null $mixSpecification
     * @return callbable
     */
    protected function specification($mixCallback, $mixSpecification = null)
    {
        if (is_null($mixSpecification)) {
            $mixSpecification = function ($objSelect) use ($mixCallback) {
                call_user_func($mixCallback, $objSelect);
            };
        } else {
            $mixSpecification = function ($objSelect) use ($mixCallback, $mixSpecification) {
                call_user_func($mixCallback, $objSelect);
                if (! is_string($mixSpecification) && is_callable($mixSpecification)) {
                    call_user_func($mixSpecification, $objSelect);
                }
            };
        }

        return $mixSpecification;
    }

    /**
     * call 
     *
     * @param string $method
     * @param array $arrArgs
     * @return mixed
     */
    public function __call(string $method, array $arrArgs)
    {
        return $this->objAggregate->$method(...$arrArgs);
    }
}
