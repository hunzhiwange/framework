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
use Leevel\Database\Ddd\Relation\Relation;
use Leevel\Database\Select as DatabaseSelect;
use function Leevel\Support\Arr\normalize;
use Leevel\Support\Arr\normalize;
use function Leevel\Support\Str\contains;
use Leevel\Support\Str\contains;
use function Leevel\Support\Str\starts_with;
use Leevel\Support\Str\starts_with;

/**
 * 模型实体查询.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.07.10
 *
 * @version 1.0
 */
class Select
{
    /**
     * 模型实体.
     *
     * @var \Leevel\Database\Ddd\IEntity
     */
    protected $entity;

    /**
     * 查询.
     *
     * @var \Leevel\Database\Select
     */
    protected $select;

    /**
     * 关联预载入.
     *
     * @var array
     */
    protected $preLoads = [];

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Ddd\IEntity $entity
     */
    public function __construct(IEntity $entity)
    {
        $this->entity = $entity;

        $this->select = $this->entity->databaseSelect();
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
        $result = $this->select->{$method}(...$args);

        if ($result instanceof DatabaseSelect) {
            return $this;
        }

        if (!$this->preLoads) {
            return $result;
        }

        if (is_array($result) &&
            isset($result[DatabaseSelect::PAGE]) &&
            true === $result[DatabaseSelect::PAGE]) {
            $result[1] = $this->preLoadResult($result[1]);
        } else {
            $result = $this->preLoadResult($result);
        }

        return $result;
    }

    /**
     * 获取模型实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function entity(): IEntity
    {
        return $this->entity;
    }

    /**
     * 添加预载入的关联.
     *
     * @param array $relation
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function eager(array $relation): self
    {
        $this->preLoads = array_merge(
            $this->preLoads,
            $this->parseWithRelation($relation)
        );

        return $this;
    }

    /**
     * 尝试解析结果预载.
     *
     * @param mixed $result
     *
     * @return mixed
     */
    public function preLoadResult($result)
    {
        list($result, $type) = $this->conversionToEntitys($result);

        if ($type) {
            $result = $this->preLoadRelation($result);

            if ('entity' === $type) {
                $result = reset($result);
            } elseif ('collection' === $type) {
                $result = new Collection($result);
            }
        }

        return $result;
    }

    /**
     * 通过主键查找模型实体.
     *
     * @param int   $id
     * @param array $column
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function find(int $id, array $column = ['*']): IEntity
    {
        return $this->select
            ->where($this->entity->singlePrimaryKey(), '=', $id)
            ->setColumns($column)
            ->findOne();
    }

    /**
     * 根据主键查找模型实体.
     *
     * @param array $ids
     * @param array $column
     *
     * @return \Leevel\Collection\Collection
     */
    public function findMany(array $ids, array $column = ['*']): Collection
    {
        if (empty($ids)) {
            return $this->entity->collection();
        }

        return $this->select
            ->whereIn($this->entity->singlePrimaryKey(), $ids)
            ->setColumns($column)
            ->findAll();
    }

    /**
     * 通过主键查找模型实体，未找到则抛出异常.
     *
     * @param int   $id
     * @param array $column
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function findOrFail(int $id, array $column = ['*']): IEntity
    {
        $result = $this->find($id, $column);

        if (null !== $result->__get($this->entity->singlePrimaryKey())) {
            return $result;
        }

        throw (new EntityNotFoundException())
            ->setEntity(get_class($this->entity));
    }

    /**
     * 从模型实体中软删除数据.
     *
     * @return int
     */
    public function softDelete(): int
    {
        $this->entity->__set($this->deleteAtColumn(), date('Y-m-d H:i:s'));
        $this->entity->handleEvent(IEntity::BEFORE_SOFT_DELETE_EVENT);
        $num = $this->entity->update()->flush();
        $this->entity->handleEvent(IEntity::AFTER_SOFT_DELETE_EVENT);

        return $num;
    }

    /**
     * 根据主键 ID 删除模型实体.
     *
     * @param array $ids
     * @param mixed $id
     *
     * @return int
     */
    public function softDestroy(array $ids): int
    {
        $count = 0;

        $instance = $this->entity->make();
        $entitys = $instance
            ->select()
            ->whereIn($instance->singlePrimaryKey(), $ids)
            ->findAll();

        /** @var \Leevel\Database\Ddd\IEntity $entity */
        foreach ($entitys as $entity) {
            if ($entity->selectForEntity()->softDelete()) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * 恢复软删除的模型实体.
     *
     * @return int
     */
    public function softRestore(): int
    {
        $this->entity->handleEvent(IEntity::BEFORE_SOFT_RESTORE_EVENT);

        $this->entity->__set($this->deleteAtColumn(), null);

        $num = $this->entity->update()->flush();

        $this->entity->handleEvent(IEntity::AFTER_SOFT_RESTORE_EVENT);

        return $num;
    }

    /**
     * 获取不包含软删除的数据.
     *
     * @return \Leevel\Database\Select
     */
    public function withoutSoftDeleted(): DatabaseSelect
    {
        return $this->select->whereNull($this->deleteAtColumn());
    }

    /**
     * 获取只包含软删除的数据.
     *
     * @return \Leevel\Database\Select
     */
    public function onlySoftDeleted(): DatabaseSelect
    {
        return $this->select->whereNotNull($this->deleteAtColumn());
    }

    /**
     * 检查模型实体是否已经被软删除了.
     *
     * @return bool
     */
    public function softDeleted(): bool
    {
        return null !== $this->entity->__get($this->deleteAtColumn());
    }

    /**
     * 获取软删除字段.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function deleteAtColumn(): string
    {
        if (defined(get_class($this->entity).'::DELETE_AT')) {
            $deleteAt = $this->entity::DELETE_AT;
        } else {
            $deleteAt = 'delete_at';
        }

        if (!$this->entity->hasField($deleteAt)) {
            $e = sprintf(
                'Entity `%s` soft delete field `%s` was not found.',
                get_class($this->entity), $deleteAt
            );

            throw new InvalidArgumentException($e);
        }

        return $deleteAt;
    }

    /**
     * 预载入模型实体.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     *
     * @return array
     */
    protected function preLoadRelation(array $entitys): array
    {
        foreach ($this->preLoads as $name => $condition) {
            if (false === strpos($name, '.')) {
                $entitys = $this->loadRelation($entitys, $name, $condition);
            }
        }

        return $entitys;
    }

    /**
     * 取得关联模型实体.
     *
     * @param string $name
     *
     * @return \leevel\Database\Ddd\Relation\Relation
     */
    protected function getRelation(string $name): Relation
    {
        $relation = Relation::withoutRelationCondition(function () use ($name) {
            return $this->entity->{$name}();
        });

        $nested = $this->nestedRelation($name);

        if (count($nested) > 0) {
            $relation->getSelect()->eager($nested);
        }

        return $relation;
    }

    /**
     * 尝试取得嵌套关联.
     *
     * @param string $relation
     *
     * @return array
     */
    protected function nestedRelation(string $relation): array
    {
        $nested = [];

        foreach ($this->preLoads as $name => $condition) {
            if ($this->isNested($name, $relation)) {
                $nested[substr($name, strlen($relation.'.'))] = $condition;
            }
        }

        return $nested;
    }

    /**
     * 判断是否存在嵌套关联.
     *
     * @param string $name
     * @param string $relation
     *
     * @return bool
     */
    protected function isNested(string $name, string $relation): bool
    {
        return contains($name, '.') && starts_with($name, $relation.'.');
    }

    /**
     * 格式化预载入关联.
     *
     * @param array $relation
     *
     * @return array
     */
    protected function parseWithRelation(array $relation): array
    {
        $data = [];

        foreach ($relation as $name => $condition) {
            if (is_numeric($name)) {
                list($name, $condition) = [
                    $condition,
                    function () {
                    },
                ];
            }

            $data = $this->parseNestedWith($name, $data);
            $data[$name] = $condition;
        }

        return $data;
    }

    /**
     * 解析嵌套关联.
     *
     * @param string $name
     * @param array  $result
     *
     * @return array
     */
    protected function parseNestedWith(string $name, array $result): array
    {
        $progress = [];

        foreach (explode('.', $name) as $segment) {
            $progress[] = $segment;

            if (!isset($result[$last = implode('.', $progress)])) {
                $result[$last] = function () {
                };
            }
        }

        return $result;
    }

    /**
     * 转换结果到模型实体类型.
     *
     * @param mixed $result
     *
     * @return array
     */
    protected function conversionToEntitys($result): array
    {
        $type = '';

        if ($result instanceof Collection) {
            $data = [];

            foreach ($result as $entity) {
                $data[] = $entity;
            }

            $result = $data;
            $type = 'collection';
        } elseif (is_object($result) && $result instanceof IEntity) {
            $result = [$result];
            $type = 'entity';
        }

        return [$result, $type];
    }

    /**
     * 关联数据设置到模型实体上.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     * @param string                         $name
     * @param \Closure                       $condition
     *
     * @return array
     */
    protected function loadRelation(array $entitys, string $name, Closure $condition): array
    {
        $relation = $this->getRelation($name);

        $relation->preLoadCondition($entitys);

        call_user_func($condition, $relation);

        return $relation->matchPreLoad($entitys, $relation->getPreLoad(), $name);
    }
}

// import fn.
class_exists(normalize::class);
class_exists(contains::class);
class_exists(starts_with::class);
