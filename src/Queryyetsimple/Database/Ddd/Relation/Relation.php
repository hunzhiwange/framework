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

namespace Leevel\Database\Ddd\Relation;

use Closure;
use Exception;
use Leevel\Collection\Collection;
use Leevel\Database\Ddd\IEntity;

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
     * @var \Leevel\Database\Select
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
    public function __construct(IEntity $targetEntity, IEntity $sourceEntity, $targetKey, $sourceKey)
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
     * @return \Leevel\Database\Select
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * 取得预载入关联模型实体.
     *
     * @return \Leevel\Collection\Collection
     */
    public function getPreLoad()
    {
        return $this->querySelelct()->
        preLoadResult(
            $this->getAll()
        );
    }

    /**
     * 取得关联目标模型实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function getTargetEntity()
    {
        return $this->targetEntity;
    }

    /**
     * 取得源模型实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function getSourceEntity()
    {
        return $this->sourceEntity;
    }

    /**
     * 取得目标字段.
     *
     * @return string
     */
    public function getTargetKey()
    {
        return $this->targetKey;
    }

    /**
     * 取得源字段.
     *
     * @return string
     */
    public function getSourceKey()
    {
        return $this->sourceKey;
    }

    /**
     * 获取不带关联条件的关联对象
     *
     * @param \Closure $returnRelation
     *
     * @return \leevel\Mvc\Relation\Relation
     */
    public static function withoutRelationCondition(Closure $returnRelation)
    {
        $old = static::$relationCondition;
        static::$relationCondition = false;

        $relation = call_user_func($returnRelation);

        if (!($relation instanceof self)) {
            throw new Exception('The result must be relation.');
        }

        static::$relationCondition = $old;

        return $relation;
    }

    /**
     * 关联基础查询条件.
     */
    abstract public function addRelationCondition();

    /**
     * 设置预载入关联查询条件.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     */
    abstract public function preLoadCondition(array $entitys);

    /**
     * 匹配关联查询数据到模型实体 HasMany.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     * @param \Leevel\Collection\Collection  $result
     * @param string                         $relation
     *
     * @return array
     */
    abstract public function matchPreLoad(array $entitys, collection $result, $relation);

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
     * @param string                         $key
     *
     * @return array
     */
    protected function getEntityKey(array $entitys, $key = null)
    {
        return array_unique(
            array_values(
                array_map(function ($entity) use ($key) {
                    return $key ?
                        $entity->getProp($key) :
                        $entity->getPrimaryKeyForQuery();
                }, $entitys)
            )
        );
    }

    /**
     * 从模型实体返回查询.
     *
     * @return \Leevel\Database\Select
     */
    protected function getSelectFromEntity()
    {
        $this->select = $this->targetEntity->getClassCollectionQuery();
    }
}
