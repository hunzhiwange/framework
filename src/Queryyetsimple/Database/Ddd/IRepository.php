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
     * @param int   $id
     * @param array $column
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function find($id, $column = ['*']);

    /**
     * 取得一条数据，未找到记录抛出异常.
     *
     * @param int   $id
     * @param array $column
     *
     * @return \Leevel\Database\Ddd\IEntity|void
     */
    public function findOrFail($id, $column = ['*']);

    /**
     * 取得记录数量.
     *
     * @param null|mixed $specification
     *
     * @return int
     */
    public function count($specification = null);

    /**
     * 取得所有记录.
     *
     * @param null|callable $mixCallback
     * @param null|mixed    $specification
     *
     * @return \Leevel\Collection\Collection
     */
    public function all($specification = null);

    /**
     * 保存数据.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function create(IEntity $entity);

    /**
     * 更新数据.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function update(IEntity $entity);

    /**
     * 删除数据.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return int
     */
    public function delete(IEntity $entity);

    /**
     * 注册保存数据.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function registerCreate(IEntity $entity);

    /**
     * 注册更新数据.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function registerUpdate(IEntity $entity);

    /**
     * 注册删除数据.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return \Leevel\Database\Ddd\UnitOfWork
     */
    public function registerDelete(IEntity $entity);

    /**
     * 响应新建.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function handleCreate(IEntity $entity);

    /**
     * 响应修改.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function handleUpdate(IEntity $entity);

    /**
     * 响应删除.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return int
     */
    public function handleDelete(IEntity $entity);

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
     * @param callable $action
     *
     * @return mixed
     */
    public function transaction(callable $action);

    /**
     * 设置聚合根.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     */
    public function setAggregate(IEntity $entity);

    /**
     * 返回聚合根.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function aggregate();

    /**
     * 返回工作单元.
     *
     * @return \Leevel\Database\Ddd\IUnitOfWork
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
