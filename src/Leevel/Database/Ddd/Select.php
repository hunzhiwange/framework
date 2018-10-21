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

use Closure;
use Exception;
use Leevel\Collection\Collection;
use Leevel\Database\Ddd\Relation\Relation;
use Leevel\Database\Select as DatabaseSelect;
use Leevel\Support\Arr;
use Leevel\Support\Str;

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
        if (method_exists($this->select, $method)) {
            $result = $this->select->{$method}(...$args);

            $result = $this->preLoadResult($result);

            return $result;
        }

        throw new Exception(
            sprintf(
                'Select do not implement magic method %s.',
                $method
            )
        );
    }

    /**
     * 获取模型实体.
     *
     * @param mixed $entity
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function getEntity($entity): IEntity
    {
        return $this->entity;
    }

    /**
     * 占位符返回本对象
     *
     * @return $this
     */
    public function querySelelct()
    {
        return $this;
    }

    /**
     * 注册查询.
     *
     * @param \Leevel\Database\Select $select
     */
    public function registerSelect(DatabaseSelect $select)
    {
        $this->select = $select;

        return $this;
    }

    /**
     * 添加预载入的关联.
     *
     * @param mixed $relation
     *
     * @return $this
     */
    public function with($relation)
    {
        if (is_string($relation)) {
            $relation = func_get_args();
        }

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

        if (is_array($result)) {
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
     * @param mixed $id
     * @param array $column
     *
     * @return null|\Leevel\Collection\Collection|\Leevel\Database\Ddd\IEntity
     */
    public function find($id, array $column = ['*'])
    {
        if (is_array($id)) {
            return $this->findMany($id, $column);
        }

        return $this->select->
        where(
            $this->entity->getPrimaryKeyNameForQuery(),
            '=',
            $id
        )->

        setColumns($column)->

        findOne();
    }

    /**
     * 根据主键查找模型实体.
     *
     * @param array $ids
     * @param array $column
     *
     * @return \Leevel\Collection\Collection
     */
    public function findMany(array $ids, array $column = ['*'])
    {
        if (empty($ids)) {
            return $this->entity->collection();
        }

        return $this->select->
        whereIn(
            $this->entity->getPrimaryKeyNameForQuery(),
            $ids
        )->

        setColumns($column)->

        findAll();
    }

    /**
     * 通过主键查找模型实体，未找到则抛出异常.
     *
     * @param mixed $id
     * @param array $column
     *
     * @return \Leevel\Collection\Collection|\Leevel\Database\Ddd\IEntity
     */
    public function findOrFail($id, array $column = ['*'])
    {
        $result = $this->find($id, $column);

        if (is_array($id)) {
            if (count($result) === count(array_unique($id))) {
                return $result;
            }
        } elseif (null !== $result) {
            return $result;
        }

        throw (new EntityNotFoundException())->
        entity(get_class($this->entity));
    }

    /**
     * 从模型实体中软删除数据.
     *
     * @return int
     */
    public function softDelete()
    {
        $select = $this->select->where(
            $this->entity->getKeyConditionForQuery()
        );

        $this->entity->{$this->getDeletedAtColumn()} = $time = $this->entity->carbon();

        $this->entity->runEvent(Entity::BEFORE_SOFT_DELETE_EVENT);

        $num = $select->update([
            $this->getDeletedAtColumn() => $this->entity->fromDateTime($time),
        ]);

        $this->entity->runEvent(Entity::AFTER_SOFT_DELETE_EVENT);

        return $num;
    }

    /**
     * 根据主键 ID 删除模型实体.
     *
     * @param array|int $ids
     * @param mixed     $id
     *
     * @return int
     */
    public function softDestroy($id)
    {
        $count = 0;
        $id = (array) $id;

        $instance = $this->entity->newInstance();

        foreach (
            $instance->whereIn(
                $instance->getPrimaryKeyNameForQuery(),
                $id
            )->
            findAll() as $entity) {
            if ($entity->softDelete()) {
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
    public function softRestore()
    {
        $this->entity->runEvent(Entity::BEFORE_SOFT_RESTORE_EVENT);

        $this->entity->{$this->getDeletedAtColumn()} = null;

        $num = $this->entity->update();

        $this->entity->runEvent(Entity::AFTER_SOFT_RESTORE_EVENT);

        return $num;
    }

    /**
     * 获取不包含软删除的数据.
     *
     * @return \Leevel\Database\Select
     */
    public function withoutSoftDeleted()
    {
        return $this->select->whereNull(
            $this->getDeletedAtColumn()
        );
    }

    /**
     * 获取只包含软删除的数据.
     *
     * @return \Leevel\Database\Select
     */
    public function onlySoftDeleted()
    {
        return $this->select->whereNotNull(
            $this->getDeletedAtColumn()
        );
    }

    /**
     * 检查模型实体是否已经被软删除了.
     *
     * @return bool
     */
    public function softDeleted()
    {
        return null !== $this->entity->{$this->getDeletedAtColumn()};
    }

    /**
     * 获取软删除字段.
     *
     * @return string
     */
    public function getDeletedAtColumn()
    {
        if (defined(get_class($this->entity).'::DELETED_AT')) {
            eval('$deleteAt = '.get_class($this->entity).'::DELETED_AT;');
        } else {
            $deleteAt = 'deleted_at';
        }

        if (!$this->entity->hasField($deleteAt)) {
            throw new Exception(
                sprintf(
                    'Entity %s do not have soft delete field [%s]',
                    get_class($this->entity),
                    $deleteAt
                )
            );
        }

        return $deleteAt;
    }

    /**
     * 获取删除表加字段.
     *
     * @return string
     */
    public function getFullDeletedAtColumn()
    {
        return $this->entity->table().'.'.
            $this->getDeletedAtColumn();
    }

    /**
     * 查询范围.
     *
     * @param mixed $scope
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function scope($scope)
    {
        if ($scope instanceof DatabaseSelect) {
            return $scope;
        }

        $select = $this->select;

        $args = func_get_args();
        array_shift($args);
        array_unshift($args, $select);

        if ($scope instanceof Closure) {
            $resultCallback = call_user_func_array($scope, $args);

            if ($resultCallback instanceof DatabaseSelect) {
                $select = $resultCallback;
            }

            unset($resultCallback);

            $this->entity->withSelectForQuery($select);
        } else {
            foreach (Arr::normalize($scope) as $scope) {
                $scope = 'scope'.ucwords($scope);

                if (method_exists($this->entity, $scope)) {
                    $resultCallback = call_user_func_array([
                        $this->entity,
                        $scope,
                    ], $args);

                    if ($resultCallback instanceof DatabaseSelect) {
                        $select = $resultCallback;
                    }

                    unset($resultCallback);

                    $this->entity->withSelectForQuery($select);
                }
            }
        }

        unset($select, $args, $scope);

        return $this->entity;
    }

    /**
     * 预载入模型实体.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     *
     * @return array
     */
    protected function preLoadRelation(array $entitys)
    {
        foreach ($this->preLoads as $name => $condition) {
            if (false === strpos($name, '.')) {
                $entitys = $this->loadRelation(
                    $entitys,
                    $name,
                    $condition
                );
            }
        }

        return $entitys;
    }

    /**
     * 取得关联模型实体.
     *
     * @param string $name
     * @param mixed  $name
     *
     * @return \leevel\Mvc\Relation\Relation
     */
    protected function getRelation($name)
    {
        $relation = Relation::withoutRelationCondition(function () use ($name) {
            return $this->entity->{$name}();
        });

        $nested = $this->nestedRelation($name);

        if (count($nested) > 0) {
            $relation->getSelect()->with($nested);
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
    protected function nestedRelation($relation)
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
    protected function isNested($name, $relation)
    {
        return Str::contains($name, '.') &&
            Str::startsWith($name, $relation.'.');
    }

    /**
     * 格式化预载入关联.
     *
     * @param array $relation
     *
     * @return array
     */
    protected function parseWithRelation(array $relation)
    {
        $arr = [];

        foreach ($relation as $name => $condition) {
            if (is_numeric($name)) {
                list($name, $condition) = [
                    $condition,
                    function () {
                    },
                ];
            }

            $arr = $this->parseNestedWith($name, $arr);

            $arr[$name] = $condition;
        }

        return $arr;
    }

    /**
     * 解析嵌套关联.
     *
     * @param string $name
     * @param array  $result
     *
     * @return array
     */
    protected function parseNestedWith($name, array $result)
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
    protected function conversionToEntitys($result)
    {
        $type = '';

        if ($result instanceof Collection) {
            $arr = [];

            foreach ($result as $entity) {
                $arr[] = $entity;
            }

            $result = $arr;
            $type = 'collection';
        } elseif ($result instanceof IEntity) {
            $result = [
                $result,
            ];

            $type = 'entity';
        }

        return [
            $result,
            $type,
        ];
    }

    /**
     * 关联数据设置到模型实体上.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     * @param string                         $name
     * @param callable                       $condition
     *
     * @return array
     */
    protected function loadRelation(array $entitys, $name, callable $condition)
    {
        $relation = $this->getRelation($name);

        $relation->preLoadCondition($entitys);

        call_user_func($condition, $relation);

        return $relation->matchPreLoad(
            $entitys,
            $relation->getPreLoad(),
            $name
        );
    }
}
