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

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\IEntity;

/**
 * 关联模型实体 ManyMany.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.09.28
 *
 * @version 1.0
 */
class ManyMany extends Relation
{
    /**
     * 中间表查询对象
     *
     * @var \Leevel\Database\Select
     */
    protected $middleSelect;

    /**
     * 中间表模型实体.
     *
     * @var \Leevel\Database\Ddd\IEntity
     */
    protected $middleEntity;

    /**
     * 目标中间表关联字段.
     *
     * @var string
     */
    protected $middleTargetKey;

    /**
     * 源中间表关联字段.
     *
     * @var string
     */
    protected $middleSourceKey;

    /**
     * 中间表隐射数据.
     *
     * @var array
     */
    protected $middleMaps = [];

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Ddd\IEntity $targetEntity
     * @param \Leevel\Database\Ddd\IEntity $sourceEntity
     * @param \Leevel\Database\Ddd\IEntity $middleEntity
     * @param string                       $targetKey
     * @param string                       $sourceKey
     * @param string                       $middleTargetKey
     * @param string                       $middleSourceKey
     */
    public function __construct(IEntity $targetEntity, IEntity $sourceEntity, IEntity $middleEntity, $targetKey, $sourceKey, $middleTargetKey, $middleSourceKey)
    {
        $this->middleEntity = $middleEntity;
        $this->middleTargetKey = $middleTargetKey;
        $this->middleSourceKey = $middleSourceKey;

        parent::__construct($targetEntity, $sourceEntity, $targetKey, $sourceKey);
    }

    /**
     * 关联基础查询条件.
     */
    public function addRelationCondition()
    {
        if (static::$booRelationCondition) {
            $this->middleSelect = $this->middleEntity->where(
                $this->middleSourceKey,
                $this->getSourceValue()
            );
        }
    }

    /**
     * 设置预载入关联查询条件.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     */
    public function preLoadCondition(array $entitys)
    {
        $this->preLoadRelationCondition($entitys);
        $this->parseSelectCondition();
    }

    /**
     * 匹配关联查询数据到模型实体.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     * @param \Leevel\Collection\Collection  $result
     * @param string                         $relation
     *
     * @return array
     */
    public function matchPreLoad(array $entitys, collection $result, $relation)
    {
        $maps = $this->buildMap($result);

        foreach ($entitys as &$entity) {
            $key = $entity->getProp($this->sourceKey);

            if (isset($maps[$key])) {
                $entity->setRelationProp(
                    $relation,
                    $this->targetEntity->collection($maps[$key])
                );
            }
        }

        return $entitys;
    }

    /**
     * 中间表查询回调处理.
     *
     * @param callable $callbacks
     *
     * @return $this
     */
    public function middleCondition(callable $callbacks)
    {
        call_user_func_array($callbacks, [
            $this->middleSelect,
            $this,
        ]);

        return $this;
    }

    /**
     * 取回源模型实体对应数据.
     *
     * @return mixed
     */
    public function getSourceValue()
    {
        return $this->sourceEntity->getProp($this->sourceKey);
    }

    /**
     * 查询关联对象
     *
     * @return mixed
     */
    public function sourceQuery()
    {
        if (false === $this->parseSelectCondition()) {
            return new Collection();
        }

        return $this->select->getAll();
    }

    /**
     * 取得中间表查询对象
     *
     * @return \Leevel\Database\Select
     */
    public function getMiddleSelect()
    {
        return $this->middleSelect;
    }

    /**
     * 取得中间表模型实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function getMiddleEntity()
    {
        return $this->middleEntity;
    }

    /**
     * 取得目标中间表关联字段.
     *
     * @return string
     */
    public function getTargetKey()
    {
        return $this->middleTargetKey;
    }

    /**
     * 取得源中间表关联字段.
     *
     * @return string
     */
    public function getSourceKey()
    {
        return $this->middleSourceKey;
    }

    /**
     * 取得中间表隐射数据.
     *
     * @return array
     */
    public function getMiddleMap()
    {
        return $this->middleMaps;
    }

    /**
     * 取得源外键值
     *
     * @return mixed
     */
    public function getSourceKeyValue()
    {
        return $this->sourceEntity->getProp($this->sourceKey);
    }

    /**
     * 预载入关联基础查询条件.
     */
    protected function preLoadRelationCondition(array $entitys)
    {
        $this->middleSelect = $this->middleEntity->whereIn($this->middleSourceKey, $this->getPreLoadSourceValue($entitys));
    }

    /**
     * 取回源模型实体对应数据.
     *
     * @return mixed
     */
    protected function getPreLoadSourceValue(array $entitys)
    {
        $arr = [];

        foreach ($entitys as $sourceEntity) {
            $arr[] = $sourceEntity->{$this->sourceKey};
        }

        return $arr;
    }

    /**
     * 模型实体隐射数据.
     *
     * @param \Leevel\Collection\Collection $result
     *
     * @return array
     */
    protected function buildMap(Collection $result)
    {
        $maps = [];

        foreach ($result as $entity) {
            $key = $entity->getProp($this->targetKey);

            if (isset($this->middleMaps[$key])) {
                foreach ($this->middleMaps[$key] as $value) {
                    $maps[$value][] = $entity;
                }
            }
        }

        return $maps;
    }

    /**
     * 通过中间表获取目标 ID.
     *
     * @return array
     */
    protected function parseSelectCondition()
    {
        $targetId = $this->parseTargetId();

        $this->select->whereIn($this->targetKey, $targetId ?: [
            0,
        ]);

        if (!$targetId) {
            return false;
        }
    }

    /**
     * 通过中间表获取目标 ID.
     *
     * @return array
     */
    protected function parseTargetId()
    {
        $arr = $targetId = [];

        foreach ($this->middleSelect->getAll() as $entity) {
            $arr[$entity->{$this->middleTargetKey}][] = $entity->{$this->middleSourceKey};
            $targetId[] = $entity->{$this->middleTargetKey};
        }

        $this->middleMaps = $arr;

        return array_unique($targetId);
    }
}
