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

namespace Leevel\Database\Ddd\Relation;

use Closure;
use Leevel\Collection\Collection;
use Leevel\Database\Ddd\IEntity;
use Leevel\Database\Ddd\Select;

/**
 * 关联模型实体基类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.28
 *
 * @version 1.0
 */
abstract class Relation
{
    /**
     * 查询对象
     *
     * @var \Leevel\Database\Ddd\Select
     */
    protected $select;

    /**
     * 关联目标模型实体.
     *
     * @var \Leevel\Database\Ddd\IEntity
     */
    protected $targetEntity;

    /**
     * 源模型实体.
     *
     * @var \Leevel\Database\Ddd\IEntity
     */
    protected $sourceEntity;

    /**
     * 目标关联字段.
     *
     * @var string
     */
    protected $targetKey;

    /**
     * 源关联字段.
     *
     * @var string
     */
    protected $sourceKey;

    /**
     * 是否初始化查询.
     *
     * @var bool
     */
    protected static $relationCondition = true;

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Ddd\IEntity $targetEntity
     * @param \Leevel\Database\Ddd\IEntity $sourceEntity
     * @param string                       $targetKey
     * @param string                       $sourceKey
     */
    public function __construct(IEntity $targetEntity, IEntity $sourceEntity, string $targetKey, string $sourceKey)
    {
        $this->targetEntity = $targetEntity;
        $this->sourceEntity = $sourceEntity;
        $this->targetKey = $targetKey;
        $this->sourceKey = $sourceKey;

        $this->getSelectFromEntity();
        $this->addRelationCondition();
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
        $select = $this->select->{$method}(...$args);

        if ($this->getSelect() === $select) {
            return $this;
        }

        return $select;
    }

    /**
     * 返回查询.
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function getSelect(): Select
    {
        return $this->select;
    }

    /**
     * 取得预载入关联模型实体.
     *
     * @return mixed
     */
    public function getPreLoad()
    {
        return $this->targetEntity
            ->selectForEntity()
            ->preLoadResult(
                $this->findAll()
            );
    }

    /**
     * 取得关联目标模型实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function getTargetEntity(): IEntity
    {
        return $this->targetEntity;
    }

    /**
     * 取得源模型实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function getSourceEntity(): IEntity
    {
        return $this->sourceEntity;
    }

    /**
     * 取得目标字段.
     *
     * @return string
     */
    public function getTargetKey(): string
    {
        return $this->targetKey;
    }

    /**
     * 取得源字段.
     *
     * @return string
     */
    public function getSourceKey(): string
    {
        return $this->sourceKey;
    }

    /**
     * 获取不带关联条件的关联对象.
     *
     * @param \Closure $returnRelation
     *
     * @return \Leevel\Database\Ddd\Relation\Relation
     */
    public static function withoutRelationCondition(Closure $returnRelation): self
    {
        $old = static::$relationCondition;
        static::$relationCondition = false;

        $relation = call_user_func($returnRelation);

        static::$relationCondition = $old;

        return $relation;
    }

    /**
     * 关联基础查询条件.
     */
    abstract public function addRelationCondition(): void;

    /**
     * 设置预载入关联查询条件.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     */
    abstract public function preLoadCondition(array $entitys): void;

    /**
     * 匹配关联查询数据到模型实体 HasMany.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     * @param \Leevel\Collection\Collection  $result
     * @param string                         $relation
     *
     * @return array
     */
    abstract public function matchPreLoad(array $entitys, collection $result, string $relation): array;

    /**
     * 查询关联对象
     *
     * @return mixed
     */
    abstract public function sourceQuery();

    /**
     * 返回模型实体的主键.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     * @param null|string                    $key
     *
     * @return array
     */
    protected function getEntityKey(array $entitys, ?string $key = null): array
    {
        return array_unique(
            array_values(
                array_map(function ($entity) use ($key) {
                    return $key ?
                        $entity->__get($key) :
                        $entity->singleId();
                }, $entitys)
            )
        );
    }

    /**
     * 从模型实体返回查询.
     */
    protected function getSelectFromEntity(): void
    {
        $this->select = $this->targetEntity->selectForEntity();
    }
}
