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

namespace Leevel\Mvc;

/**
 * 仓储基础接口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.10.14
 *
 * @version 1.0
 */
interface IRepository
{
    /**
     * 取得一条数据.
     *
     * @param int   $intId
     * @param array $arrColumn
     *
     * @return \Leevel\Mvc\IEntity
     */
    public function find($intId, $arrColumn = ['*']);

    /**
     * 取得一条数据，未找到记录抛出异常.
     *
     * @param int   $intId
     * @param array $arrColumn
     *
     * @return \Leevel\Mvc\IEntity|void
     */
    public function findOrFail($intId, $arrColumn = ['*']);

    /**
     * 取得所有记录.
     *
     * @param null|callable $mixCallback
     *
     * @return \Leevel\Collection\Collection
     */
    public function count($mixSpecification = null);

    /**
     * 取得所有记录.
     *
     * @param null|callable $mixCallback
     *
     * @return \Leevel\Collection\Collection
     */
    public function all($mixSpecification = null);

    /**
     * 保存数据.
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     *
     * @return \Leevel\Mvc\IAggregateRoot
     */
    public function create(IAggregateRoot $objEntity);

    /**
     * 更新数据.
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     *
     * @return \Leevel\Mvc\IAggregateRoot
     */
    public function update(IAggregateRoot $objEntity);

    /**
     * 删除数据.
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     *
     * @return int
     */
    public function delete(IAggregateRoot $objEntity);

    /**
     * 注册保存数据.
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     *
     * @return \Leevel\Mvc\UnitOfWork
     */
    public function registerCreate(IAggregateRoot $objEntity);

    /**
     * 注册更新数据.
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     *
     * @return \Leevel\Mvc\UnitOfWork
     */
    public function registerUpdate(IAggregateRoot $objEntity);

    /**
     * 注册删除数据.
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     *
     * @return \Leevel\Mvc\UnitOfWork
     */
    public function registerDelete(IAggregateRoot $objEntity);

    /**
     * 响应新建.
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     *
     * @return \Leevel\Mvc\IAggregateRoot
     */
    public function handleCreate(IAggregateRoot $objEntity);

    /**
     * 响应修改.
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     *
     * @return \Leevel\Mvc\IAggregateRoot
     */
    public function handleUpdate(IAggregateRoot $objEntity);

    /**
     * 响应删除.
     *
     * @param \Leevel\Mvc\IAggregateRoot $objEntity
     *
     * @return int
     */
    public function handleDelete(IAggregateRoot $objEntity);

    /**
     * 启动事物.
     */
    public function beginTransaction();

    /**
     * 事务回滚.
     */
    public function rollback();

    /**
     * 事务自动提交.
     */
    public function commit();

    /**
     * 执行数据库事务
     *
     * @param callable $calAction
     *
     * @return mixed
     */
    public function transaction($calAction);

    /**
     * 设置聚合根.
     *
     * @param \Leevel\Mvc\IAggregateRoot $objAggregate
     */
    public function setAggregate(IAggregateRoot $objAggregate);

    /**
     * 返回聚合根.
     *
     * @return \Leevel\Mvc\IAggregateRoot
     */
    public function aggregate();

    /**
     * 返回工作单元.
     *
     * @return \Leevel\Mvc\IUnitOfWork
     */
    public function unitOfWork();

    /**
     * 返回数据库仓储.
     *
     * @return \Leevel\Database\IDatabase
     */
    public function databaseConnect();

    /**
     * 注册事务提交.
     */
    public function registerCommit();
}
