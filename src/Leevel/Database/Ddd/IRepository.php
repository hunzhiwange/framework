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

use Leevel\Collection\Collection;

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
    public function find(int $id, array $column = ['*']): IEntity;

    /**
     * 取得一条数据，未找到记录抛出异常.
     *
     * @param int   $id
     * @param array $column
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function findOrFail(int $id, array $column = ['*']): IEntity;

    /**
     * 取得所有记录.
     *
     * @param null|array|\Closure|\Leevel\Database\Ddd\ISpecification|string $condition
     *
     * @return \Leevel\Collection\Collection
     */
    public function findAll($condition = null): Collection;

    /**
     * 返回一列数据.
     *
     * @param null|array|\Closure|\Leevel\Database\Ddd\ISpecification|string $condition
     * @param mixed                                                          $fieldValue
     * @param null|string                                                    $fieldKey
     *
     * @return array
     */
    public function findList($condition, $fieldValue, ?string $fieldKey = null): array;

    /**
     * 取得记录数量.
     *
     * @param null|array|\Closure|\Leevel\Database\Ddd\ISpecification|string $condition
     * @param string                                                         $field
     *
     * @return int
     */
    public function findCount($condition = null, string $field = '*'): int;

    /**
     * 分页查询记录.
     *
     * @param int                                                            $currentPage
     * @param int                                                            $perPage
     * @param null|array|\Closure|\Leevel\Database\Ddd\ISpecification|string $condition
     * @param bool                                                           $flag
     * @param bool                                                           $withTotal
     * @param string                                                         $column
     *
     * @return array
     */
    public function findPage(int $currentPage, int $perPage = 10, $condition = null, bool $flag = false, bool $withTotal = true, string $column = '*'): array;

    /**
     * 分页查询.
     * 可以渲染 HTML.
     *
     * @param int                                                            $currentPage
     * @param int                                                            $perPage
     * @param null|array|\Closure|\Leevel\Database\Ddd\ISpecification|string $condition
     * @param bool                                                           $flag
     * @param string                                                         $column
     * @param array                                                          $option
     *
     * @return array
     */
    public function findPageHtml(int $currentPage, int $perPage = 10, $condition = null, bool $flag = false, string $column = '*', array $option = []): array;

    /**
     * 创建一个无限数据的分页查询.
     *
     * @param int                                                            $currentPage
     * @param int                                                            $perPage
     * @param null|array|\Closure|\Leevel\Database\Ddd\ISpecification|string $condition
     * @param bool                                                           $flag
     * @param array                                                          $option
     *
     * @return array
     */
    public function findPageMacro(int $currentPage, int $perPage = 10, $condition = null, bool $flag = false, array $option = []): array;

    /**
     * 创建一个只有上下页的分页查询.
     *
     * @param int                                                            $currentPage
     * @param int                                                            $perPage
     * @param null|array|\Closure|\Leevel\Database\Ddd\ISpecification|string $condition
     * @param bool                                                           $flag
     * @param array                                                          $option
     *
     * @return array
     */
    public function findPagePrevNext(int $currentPage, int $perPage = 10, $condition = null, bool $flag = false, array $option = []): array;

    /**
     * 条件查询器.
     *
     * @param array|\Closure|\Leevel\Database\Ddd\ISpecification|string $condition
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function condition($condition): Select;

    /**
     * 响应新建.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return mixed
     */
    public function create(IEntity $entity);

    /**
     * 响应修改.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return mixed
     */
    public function update(IEntity $entity);

    /**
     * 响应不存在则新增否则更新.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return mixed
     */
    public function replace(IEntity $entity);

    /**
     * 响应删除.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     *
     * @return mixed
     */
    public function delete(IEntity $entity);

    /**
     * 重新载入.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     */
    public function refresh(IEntity $entity): void;

    /**
     * 返回实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function entity(): IEntity;
}
