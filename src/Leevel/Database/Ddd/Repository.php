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
use Leevel\Collection\Collection;

/**
 * 仓储基础
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.10.14
 * @since 2018.10 重新实现规约查询
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
    public function find(int $id, array $column = ['*']): IEntity
    {
        return $this->entity->find($id, $column);
    }

    /**
     * 取得一条数据，未找到记录抛出异常.
     *
     * @param int   $id
     * @param array $column
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function findOrFail(int $id, array $column = ['*']): IEntity
    {
        return $this->entity->findOrFail($id, $column);
    }

    /**
     * 取得所有记录.
     *
     * @param null|\Closure|\Leevel\Database\Ddd\ISpecification $condition
     *
     * @return \Leevel\Collection\Collection
     */
    public function findAll($condition = null): Collection
    {
        $select = $this->entity->selfDatabaseSelect();

        if ($condition) {
            $this->normalizeCondition($condition, $select);
        }

        return $select->findAll();
    }

    /**
     * 取得记录数量.
     *
     * @param null|\Closure|\Leevel\Database\Ddd\ISpecification $condition
     * @param string                                            $field
     *
     * @return int
     */
    public function findCount($condition = null, string $field = '*'): int
    {
        $select = $this->entity->selfDatabaseSelect();

        if ($condition) {
            $this->normalizeCondition($condition, $select);
        }

        return $select->findCount($field);
    }

    /**
     * 分页查询记录.
     *
     * @param int                                               $currentPage
     * @param int                                               $perPage
     * @param null|\Closure|\Leevel\Database\Ddd\ISpecification $condition
     * @param bool                                              $flag
     * @param bool                                              $withTotal
     * @param string                                            $column
     *
     * @return array
     */
    public function findPage(int $currentPage, int $perPage = 10, $condition = null, bool $flag = false, bool $withTotal = true, string $column = '*'): array
    {
        $select = $this->entity->selfDatabaseSelect();

        if ($condition) {
            $this->normalizeCondition($condition, $select);
        }

        return $select->page($currentPage, $perPage, $flag, $withTotal, $column);
    }

    /**
     * 分页查询.
     * 可以渲染 HTML.
     *
     * @param int                                               $currentPage
     * @param int                                               $perPage
     * @param null|\Closure|\Leevel\Database\Ddd\ISpecification $condition
     * @param bool                                              $flag
     * @param string                                            $column
     * @param array                                             $option
     *
     * @return array
     */
    public function findPageHtml(int $currentPage, int $perPage = 10, $condition = null, bool $flag = false, string $column = '*', array $option = []): array
    {
        $select = $this->entity->selfDatabaseSelect();

        if ($condition) {
            $this->normalizeCondition($condition, $select);
        }

        return $select->pageHtml($currentPage, $perPage, $flag, $column, $option);
    }

    /**
     * 创建一个无限数据的分页查询.
     *
     * @param int                                               $currentPage
     * @param int                                               $perPage
     * @param null|\Closure|\Leevel\Database\Ddd\ISpecification $condition
     * @param bool                                              $flag
     * @param array                                             $option
     *
     * @return array
     */
    public function findPageMacro(int $currentPage, int $perPage = 10, $condition = null, bool $flag = false, array $option = []): array
    {
        $select = $this->entity->selfDatabaseSelect();

        if ($condition) {
            $this->normalizeCondition($condition, $select);
        }

        return $select->pageMacro($currentPage, $perPage, $flag, $option);
    }

    /**
     * 创建一个只有上下页的分页查询.
     *
     * @param int                                               $currentPage
     * @param int                                               $perPage
     * @param null|\Closure|\Leevel\Database\Ddd\ISpecification $condition
     * @param bool                                              $flag
     * @param array                                             $option
     *
     * @return array
     */
    public function findPagePrevNext(int $currentPage, int $perPage = 10, $condition = null, bool $flag = false, array $option = []): array
    {
        $select = $this->entity->selfDatabaseSelect();

        if ($condition) {
            $this->normalizeCondition($condition, $select);
        }

        return $select->pagePrevNext($currentPage, $perPage, $flag, $option);
    }

    /**
     * 条件查询器.
     *
     * @param \Closure|\Leevel\Database\Ddd\ISpecification $condition
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function condition($condition): Select
    {
        $select = $this->entity->selfDatabaseSelect();

        $this->normalizeCondition($condition, $select);

        return $select;
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
            return false;
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
            return false;
        }

        return $entity->update()->flush();
    }

    /**
     * 响应不存在则新增否则更新.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return mixed
     */
    public function replace(IEntity $entity)
    {
        if ($entity->flushed()) {
            return false;
        }

        return $entity->replace()->flush();
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
            return false;
        }

        return $entity->destroy()->flush();
    }

    /**
     * 重新载入.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     */
    public function refresh(IEntity $entity): void
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
     * 处理查询条件.
     *
     * @param \Closure|\Leevel\Database\Ddd\ISpecification $condition
     * @param \Leevel\Database\Ddd\Select                  $select
     */
    protected function normalizeCondition($condition, Select $select): void
    {
        if (is_object($condition) && $condition instanceof ISpecification) {
            $this->normalizeSpec($select, $condition);
        } elseif (is_object($condition) && $condition instanceof Closure) {
            $condition($select, $this->entity);
        } else {
            throw new InvalidArgumentException('Invalid condition type.');
        }
    }

    /**
     * 处理规约查询.
     *
     * @param \Leevel\Database\Ddd\Select         $select
     * @param \Leevel\Database\Ddd\ISpecification $spec
     */
    protected function normalizeSpec(Select $select, ISpecification $spec): void
    {
        if ($spec instanceof ISpecification && $spec->isSatisfiedBy($this->entity)) {
            $spec->handle($select, $this->entity);
        }
    }
}
