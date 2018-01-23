<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Mvc;

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
     * @var \Queryyetsimple\Mvc\IUnitOfWork
     */
    protected $objUnitOfWork;

    /**
     * 聚合根
     *
     * @var \Queryyetsimple\Mvc\IAggregateRoot
     */
    protected $objAggregate;

    /**
     * 构造函数
     *
     * @param \Queryyetsimple\Mvc\IAggregateRoot $objAggregate
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
     * @return \Queryyetsimple\Mvc\IEntity
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
     * @return \Queryyetsimple\Mvc\IEntity|void
     */
    public function findOrFail($intId, $arrColumn = ['*'])
    {
        return $this->objAggregate->findOrFail($intId, $arrColumn);
    }

    /**
     * 取得所有记录
     *
     * @param null|callback $mixCallback
     * @return \Queryyetsimple\Collection\Collection
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
     * @return \Queryyetsimple\Collection\Collection
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
     * @param \Queryyetsimple\Mvc\IAggregateRoot $objEntity
     * @return \Queryyetsimple\Mvc\IAggregateRoot
     */
    public function create(IAggregateRoot $objEntity)
    {
        return $this->handleCreate($objEntity);
    }

    /**
     * 更新数据
     *
     * @param \Queryyetsimple\Mvc\IAggregateRoot $objEntity
     * @return \Queryyetsimple\Mvc\IAggregateRoot
     */
    public function update(IAggregateRoot $objEntity)
    {
        return $this->handleUpdate($objEntity);
    }

    /**
     * 删除数据
     *
     * @param \Queryyetsimple\Mvc\IAggregateRoot $objEntity
     * @return int
     */
    public function delete(IAggregateRoot $objEntity)
    {
        return $this->handleDelete($objEntity);
    }

    /**
     * 注册保存数据
     *
     * @param \Queryyetsimple\Mvc\IAggregateRoot $objEntity
     * @return \Queryyetsimple\Mvc\UnitOfWork
     */
    public function registerCreate(IAggregateRoot $objEntity)
    {
        $this->checkUnitOfWork();
        return $this->objUnitOfWork->registerCreate($objEntity, $this);
    }

    /**
     * 注册更新数据
     *
     * @param \Queryyetsimple\Mvc\IAggregateRoot $objEntity
     * @return \Queryyetsimple\Mvc\UnitOfWork
     */
    public function registerUpdate(IAggregateRoot $objEntity)
    {
        $this->checkUnitOfWork();
        return $this->objUnitOfWork->registerUpdate($objEntity, $this);
    }

    /**
     * 注册删除数据
     *
     * @param \Queryyetsimple\Mvc\IAggregateRoot $objEntity
     * @return \Queryyetsimple\Mvc\UnitOfWork
     */
    public function registerDelete(IAggregateRoot $objEntity)
    {
        $this->checkUnitOfWork();
        return $this->objUnitOfWork->registerDelete($objEntity, $this);
    }

    /**
     * 响应新建
     *
     * @param \Queryyetsimple\Mvc\IAggregateRoot $objEntity
     * @return \Queryyetsimple\Mvc\IAggregateRoot
     */
    public function handleCreate(IAggregateRoot $objEntity)
    {
        return $objEntity->create();
    }

    /**
     * 响应修改
     *
     * @param \Queryyetsimple\Mvc\IAggregateRoot $objEntity
     * @return \Queryyetsimple\Mvc\IAggregateRoot
     */
    public function handleUpdate(IAggregateRoot $objEntity)
    {
        return $objEntity->update();
    }

    /**
     * 响应删除
     *
     * @param \Queryyetsimple\Mvc\IAggregateRoot $objEntity
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
     * @param \Queryyetsimple\Mvc\IAggregateRoot $objAggregate
     * @return void
     */
    public function setAggregate(IAggregateRoot $objAggregate)
    {
        return $this->objAggregate = $objAggregate;
    }

    /**
     * 返回聚合根
     *
     * @return \Queryyetsimple\Mvc\IAggregateRoot
     */
    public function aggregate()
    {
        return $this->objAggregate;
    }

    /**
     * 返回工作单元
     *
     * @return \Queryyetsimple\Mvc\IUnitOfWork
     */
    public function unitOfWork()
    {
        return $this->objUnitOfWork;
    }

    /**
     * 返回数据库仓储
     *
     * @return \Queryyetsimple\Database\IDatabase
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
     * @return \Queryyetsimple\Mvc\IUnitOfWork
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
