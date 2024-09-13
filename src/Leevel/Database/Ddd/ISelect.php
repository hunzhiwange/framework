<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd;

/**
 * 实体查询接口.
 */
interface ISelect
{
    /**
     * 获取实体.
     */
    public function entity(): Entity;

    /**
     * 获取不执行预载入的查询结果.
     */
    public static function withoutPreLoadsResult(\Closure $call): mixed;

    /**
     * 包含软删除数据的实体查询对象.
     *
     * - 获取包含软删除的数据.
     * - 会覆盖查询条件，需要首先调用.
     *
     * @todo remove this(注意关联模型)
     */
    public function withSoftDeleted(): self;

    /**
     * 仅仅包含软删除数据的实体查询对象.
     *
     * - 获取只包含软删除的数据.
     * - 会覆盖查询条件，需要首先调用.
     *
     * @todo remove this(注意关联模型)
     */
    public function onlySoftDeleted(): self;

    /**
     * 添加预载入关联查询.
     */
    public function eager(array $relation): self;

    /**
     * 尝试解析结果预载.
     */
    public function preLoadResult(mixed $result): mixed;

    /**
     * 通过主键或条件查找实体.
     */
    public function findEntity(null|array|\Closure|int|string $idOrCondition = null, array $column = ['*']): Entity;

    /**
     * 通过主键或条件查找多个实体.
     */
    public function findMany(null|array|\Closure $idsOrCondition = null, array $column = ['*']): EntityCollection;

    /**
     * 通过主键或条件查找实体，未找到则抛出异常.
     */
    public function findOrFail(null|array|\Closure|int|string $idOrCondition = null, array $column = ['*']): Entity;
}
