<?php

declare(strict_types=1);

namespace Leevel\Database\Ddd\Relation;

use Leevel\Database\Ddd\Entity;
use Leevel\Database\Ddd\EntityCollection;
use Leevel\Database\Ddd\ISelect;
use Leevel\Database\Ddd\Select;

/**
 * 关联实体基类.                                                                                                                                                条件语句 fi.
 */
abstract class Relation implements ISelect
{
    /**
     * 查询对象.
     */
    protected Select $select;

    /**
     * 关联目标实体.
     */
    protected Entity $targetEntity;

    /**
     * 源实体.
     */
    protected Entity $sourceEntity;

    /**
     * 目标关联字段.
     */
    protected string $targetKey;

    /**
     * 源关联字段.
     */
    protected string $sourceKey;

    /**
     * 是否初始化关联查询条件.
     */
    protected static bool $relationCondition = true;

    /**
     * 源数据为空.
     */
    protected bool $emptySourceData = false;

    /**
     * 构造函数.
     */
    public function __construct(Entity $targetEntity, Entity $sourceEntity, string $targetKey, string $sourceKey, ?\Closure $scope = null)
    {
        $this->targetEntity = $targetEntity;
        $this->sourceEntity = $sourceEntity;
        $this->targetKey = $targetKey;
        $this->sourceKey = $sourceKey;
        $this->getSelectFromEntity();
        $this->scope($scope);
        $this->addRelationCondition();
    }

    /**
     * 实现魔术方法 __call.
     */
    public function __call(string $method, array $args): mixed
    {
        $select = $this->select->{$method}(...$args);
        if ($this->getSelect() === $select) {
            return $this;
        }

        return $select;
    }

    /**
     * 返回查询.
     */
    public function getSelect(): Select
    {
        return $this->select;
    }

    /**
     * 取得预载入关联实体.
     */
    public function getPreLoad(): mixed
    {
        if ($this->emptySourceData) {
            $data = $this->targetEntity->collection();
        } else {
            $data = $this->findAll();
        }

        return $this->getSelect()->preLoadResult($data);
    }

    /**
     * 取得关联目标实体.
     */
    public function getTargetEntity(): Entity
    {
        return $this->targetEntity;
    }

    /**
     * 取得源实体.
     */
    public function getSourceEntity(): Entity
    {
        return $this->sourceEntity;
    }

    /**
     * 取得目标字段.
     */
    public function getTargetKey(): string
    {
        return $this->targetKey;
    }

    /**
     * 取得源字段.
     */
    public function getSourceKey(): string
    {
        return $this->sourceKey;
    }

    /**
     * 获取不带关联条件的关联对象.
     */
    public static function withoutRelationCondition(\Closure $call): self
    {
        $old = static::$relationCondition;
        static::$relationCondition = false;

        try {
            $relation = $call();
            static::$relationCondition = $old;
        } catch (\Throwable $e) {
            static::$relationCondition = $old;

            throw $e;
        }

        return $relation;
    }

    /**
     * 取回源实体对应数据.
     */
    public function getSourceValue(): mixed
    {
        return $this->sourceEntity->prop($this->sourceKey);
    }

    /**
     * 关联基础查询条件.
     */
    abstract public function addRelationCondition(): void;

    /**
     * 设置预载入关联查询条件.
     */
    abstract public function preLoadCondition(array $entities): void;

    /**
     * 匹配关联查询数据到实体.
     */
    abstract public function matchPreLoad(array $entities, EntityCollection $result, string $relation): array;

    /**
     * 查询关联对象.
     */
    abstract public function sourceQuery(): mixed;

    /**
     * 准备关联查询条件.
     */
    protected function prepareRelationCondition(\Closure $call): void
    {
        if (!static::$relationCondition) {
            return;
        }

        if (!$sourceValue = $this->getSourceValue()) {
            $this->emptySourceData = true;
        } else {
            $this->emptySourceData = false;
            $call($sourceValue);
        }
    }

    /**
     * 返回实体数组指定键的值数组.
     */
    protected function getEntityKey(array $entitys, string $key): array
    {
        $values = array_map(static function (Entity $entity) use ($key): mixed {
            return $entity->prop($key);
        }, $entitys);

        return array_unique(array_values($values));
    }

    /**
     * 从实体返回查询.
     */
    protected function getSelectFromEntity(): void
    {
        $this->select = $this->targetEntity->select();
    }

    /**
     * 关联查询作用域.
     */
    protected function scope(?\Closure $scope = null): void
    {
        if (!$scope) {
            return;
        }

        $scope($this);
    }
}
