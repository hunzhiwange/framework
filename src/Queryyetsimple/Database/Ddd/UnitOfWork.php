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
     * 基础仓储.
     *
     * @var \Leevel\Database\Ddd\IRepository
     */
    protected $repository;

    /**
     * 是否提交事务
     *
     * @var bool
     */
    protected $committed = false;

    /**
     * 新建对象
     *
     * @var array
     */
    protected $creates = [];

    /**
     * 更新对象
     *
     * @var array
     */
    protected $updates = [];

    /**
     * 删除对象
     *
     * @var array
     */
    protected $deletes = [];

    /**
     * 注册对象数量.
     *
     * @var int
     */
    protected $count = 0;

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Ddd\IRepository $repository
     *
     * @return $this
     */
    public function __construct(IRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 启动事物.
     */
    public function beginTransaction()
    {
        $this->repository->beginTransaction();

        $this->committed = false;
    }

    /**
     * 事务回滚.
     */
    public function rollback()
    {
        $this->repository->rollback();

        $this->committed = false;
    }

    /**
     * 事务自动提交.
     */
    public function commit()
    {
        if ($this->committed) {
            return;
        }

        $this->repository->commit();

        $this->committed = true;
    }

    /**
     * 事务回滚.
     *
     * @param callable $action
     *
     * @return mixed
     */
    public function transaction(callable $action)
    {
        if ($this->committed) {
            return;
        }

        $this->committed = true;

        return $this->repository->transaction($action);
    }

    /**
     * 是否已经提交事务
     *
     * @return bool
     */
    public function committed()
    {
        return $this->committed;
    }

    /**
     * 注册事务提交.
     */
    public function registerCommit()
    {
        if ($this->committed && 0 === $this->count) {
            return;
        }

        if ($this->count > 1) {
            $this->transaction(function () {
                $this->handleRepository();
            });
        } else {
            $this->handleRepository();
        }

        $this->committed = true;
    }

    /**
     * 注册新建.
     *
     * @param \Leevel\Database\Ddd\IEntity     $entity
     * @param \Leevel\Database\Ddd\IRepository $repository
     *
     * @return $this
     */
    public function registerCreate(IEntity $entity, IRepository $repository)
    {
        $hash = spl_object_hash($entity);

        if (!isset($this->creates[$hash])) {
            $this->creates[$hash] = [
                $entity,
                $repository,
            ];

            $this->count++;
        }

        return $this;
    }

    /**
     * 注册更新.
     *
     * @param \Leevel\Database\Ddd\IEntity     $entity
     * @param \Leevel\Database\Ddd\IRepository $repository
     *
     * @return $this
     */
    public function registerUpdate(IEntity $entity, IRepository $repository)
    {
        $hash = spl_object_hash($entity);

        if (!isset($this->updates[$hash])) {
            $this->updates[$hash] = [
                $entity,
                $repository,
            ];

            $this->count++;
        }

        return $this;
    }

    /**
     * 注册删除.
     *
     * @param \Leevel\Database\Ddd\IEntity     $entity
     * @param \Leevel\Database\Ddd\IRepository $repository
     *
     * @return $this
     */
    public function registerDelete(IEntity $entity, IRepository $repository)
    {
        $hash = spl_object_hash($entity);

        if (!isset($this->deletes[$hash])) {
            $this->deletes[$hash] = [
                $entity,
                $repository,
            ];

            $this->count++;
        }

        return $this;
    }

    /**
     * 响应仓储.
     */
    protected function handleRepository()
    {
        foreach ($this->creates as $create) {
            list($entity, $repository) = $create;

            $repository->handleCreate($entity);
        }

        foreach ($this->updates as $update) {
            list($entity, $repository) = $update;

            $repository->handleUpdate($entity);
        }

        foreach ($this->deletes as $delete) {
            list($entity, $repository) = $delete;

            $repository->handleDelete($entity);
        }
    }
}
