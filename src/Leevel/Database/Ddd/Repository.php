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
     * 实体.
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
        $this->entity = $entity;
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
        $select = $this->entity->selfDatabaseSelect();

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
        $select = $this->entity->selfDatabaseSelect();

        if (!is_string($specification) && is_callable($specification)) {
            call_user_func($specification, $select);
        }

        return $select->findAll();
    }

    /**
     * 响应新建.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return mixed
     */
    public function create(IEntity $entity)
    {
        if ($entity->flushed()) {
            return;
        }

        return $entity->create()->flush();
    }

    /**
     * 响应修改.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return mixed
     */
    public function update(IEntity $entity)
    {
        if ($entity->flushed()) {
            return;
        }

        return $entity->update()->flush();
    }

    /**
     * 响应删除.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return mixed
     */
    public function delete(IEntity $entity)
    {
        if ($entity->flushed()) {
            return;
        }

        return $entity->destroy()->flush();
    }

    /**
     * 重新载入.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     */
    public function refresh(IEntity $entity)
    {
        $entity->refresh();
    }

    /**
     * 返回实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function entity(): IEntity
    {
        return $this->entity;
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
