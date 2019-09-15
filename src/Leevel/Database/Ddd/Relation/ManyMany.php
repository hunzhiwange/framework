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

use Leevel\Collection\Collection;
use Leevel\Database\Ddd\IEntity;
use Leevel\Database\Ddd\Select;

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
     * 中间表只包含软删除的数据.
     *
     * @var bool
     */
    protected $middleOnlySoftDeleted = false;

    /**
     * 中间表包含软删除的数据.
     *
     * @var bool
     */
    protected $middleWithSoftDeleted = false;

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
    public function __construct(IEntity $targetEntity, IEntity $sourceEntity, IEntity $middleEntity, string $targetKey, string $sourceKey, string $middleTargetKey, string $middleSourceKey)
    {
        $this->middleEntity = $middleEntity;
        $this->middleTargetKey = $middleTargetKey;
        $this->middleSourceKey = $middleSourceKey;

        parent::__construct($targetEntity, $sourceEntity, $targetKey, $sourceKey);
    }

    /**
     * 返回数据库查询集合对象 select.
     *
     * - 获取包含软删除的数据.
     *
     * @param int $softDeletedType
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function middleWithSoftDeleted(bool $middleWithSoftDeleted = true): self
    {
        $this->middleWithSoftDeleted = $middleWithSoftDeleted;

        return $this;
    }

    /**
     * 返回数据库查询集合对象 select.
     *
     * - 获取只包含软删除的数据.
     *
     * @param int $softDeletedType
     *
     * @return \Leevel\Database\Ddd\Select
     */
    public function middleOnlySoftDeleted(bool $middleOnlySoftDeleted = true): self
    {
        $this->middleOnlySoftDeleted = $middleOnlySoftDeleted;

        return $this;
    }

    /**
     * 关联基础查询条件.
     */
    public function addRelationCondition(): void
    {
        if (static::$relationCondition) {
            if (null === $sourceValue = $this->getSourceValue()) {
                $this->emptySourceData = true;
            } else {
                $this->selectRelationData([$sourceValue]);
            }
        }
    }

    /**
     * 设置预载入关联查询条件.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $entitys
     */
    public function preLoadCondition(array $entitys): void
    {
        if (!$sourceValue = $this->getPreLoadSourceValue($entitys)) {
            $this->emptySourceData = true;

            return;
        }

        $this->emptySourceData = false;
        $this->selectRelationData($sourceValue);
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
    public function matchPreLoad(array $entitys, collection $result, string $relation): array
    {
        $maps = $this->buildMap($result);
        foreach ($entitys as $entity) {
            $key = $entity->prop($this->sourceKey);
            $entity->withRelationProp(
                $relation,
                $this->targetEntity->collection($maps[$key] ?? []),
            );
        }

        return $entitys;
    }

    /**
     * 取回源模型实体对应数据.
     *
     * @return mixed
     */
    public function getSourceValue()
    {
        return $this->sourceEntity->prop($this->sourceKey);
    }

    /**
     * 查询关联对象.
     *
     * @return mixed
     */
    public function sourceQuery()
    {
        if (true === $this->emptySourceData) {
            return new Collection();
        }

        $tmps = Select::withoutPreLoadsResult(function () {
            return $this->select->findAll();
        });
        if (!$tmps) {
            return new Collection();
        }

        $result = [];
        $middelClass = get_class($this->middleEntity);
        $targetClass = get_class($this->targetEntity);

        foreach ($tmps as $value) {
            $value = (array) $value;
            $middleEnity = new $middelClass([
                $this->middleSourceKey => $value['middle_'.$this->middleSourceKey],
                $this->middleTargetKey => $value['middle_'.$this->middleTargetKey],
            ]);

            unset(
                $value['middle_'.$this->middleSourceKey],
                $value['middle_'.$this->middleTargetKey]
            );

            $targetEntity = new $targetClass($value);
            $targetEntity->withMiddle($middleEnity);
            $result[] = $targetEntity;
        }

        return new Collection($result, [$targetClass]);
    }

    /**
     * 取得预载入关联模型实体.
     *
     * @return mixed
     */
    public function getPreLoad()
    {
        return $this->getSelect()->preLoadResult($this->sourceQuery());
    }

    /**
     * 取得中间表模型实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function getMiddleEntity(): IEntity
    {
        return $this->middleEntity;
    }

    /**
     * 取得目标中间表关联字段.
     *
     * @return string
     */
    public function getMiddleTargetKey(): string
    {
        return $this->middleTargetKey;
    }

    /**
     * 取得源中间表关联字段.
     *
     * @return string
     */
    public function getMiddleSourceKey(): string
    {
        return $this->middleSourceKey;
    }

    /**
     * 查询关联数据.
     *
     * @param array $sourceValue
     */
    protected function selectRelationData(array $sourceValue): void
    {
        $this->emptySourceData = false;

        $middleCondition = [
            $this->middleTargetKey => '{['.$this->targetEntity->table().'.'.$this->targetKey.']}',
        ];
        $this->prepareMiddleSoftDeleted($middleCondition);

        $this->select
            ->join(
                $this->middleEntity->table(),
                [
                    'middle_'.$this->middleTargetKey => $this->middleTargetKey,
                    'middle_'.$this->middleSourceKey => $this->middleSourceKey,
                ],
                $middleCondition,
            )
            ->whereIn(
                $this->middleEntity->table().'.'.$this->middleSourceKey,
                $sourceValue,
            )
            ->asDefault()
            ->asCollection(false);
    }

    /**
     * 中间表软删除处理.
     *
     * @param array $middleCondition
     */
    protected function prepareMiddleSoftDeleted(array &$middleCondition): void
    {
        if (!defined(get_class($this->middleEntity).'::DELETE_AT')) {
            return;
        }

        if (true === $this->middleWithSoftDeleted) {
            return;
        }

        if (true === $this->middleOnlySoftDeleted) {
            $value = ['>', 0];
        } else {
            $value = 0;
        }

        $field = $this->middleEntity->table().'.'.$this->middleEntity::deleteAtColumn();
        $middleCondition[$field] = $value;
    }

    /**
     * 取回源模型实体对应数据.
     *
     * @return array
     */
    protected function getPreLoadSourceValue(array $entitys): array
    {
        $data = [];
        foreach ($entitys as $sourceEntity) {
            if (null !== $value = $sourceEntity->prop($this->sourceKey)) {
                $data[] = $value;
            }
        }

        return $data;
    }

    /**
     * 模型实体隐射数据.
     *
     * @param \Leevel\Collection\Collection $result
     *
     * @return array
     */
    protected function buildMap(Collection $result): array
    {
        $maps = [];
        foreach ($result as $entity) {
            $maps[$entity->middle()->prop($this->middleSourceKey)][] = $entity;
        }

        return $maps;
    }
}
