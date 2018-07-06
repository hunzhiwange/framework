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
    protected $objEntity;

    /**
     * 查询.
     *
     * @var \Leevel\Database\Select
     */
    protected $objSelect;

    /**
     * 关联预载入.
     *
     * @var array
     */
    protected $arrPreLoad = [];

    /**
     * 构造函数.
     *
     * @param \Leevel\Database\Ddd\IEntity $objEntity
     */
    public function __construct($objEntity)
    {
        $this->objEntity = $objEntity;
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $arrArgs
     *
     * @return mixed
     */
    public function __call(string $method, array $arrArgs)
    {
        if (method_exists($this->objSelect, $method)) {
            $mixResult = $this->objSelect->{$method}(...$arrArgs);

            $mixResult = $this->preLoadResult($mixResult);

            return $mixResult;
        }

        throw new Exception(sprintf('Select do not implement magic method %s.', $method));
    }

    /**
     * 获取模型实体.
     *
     * @param mixed $objEntity
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function getEntity($objEntity)
    {
        return $this->objEntity;
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
     * @param \Leevel\Database\Select $objSelect
     */
    public function registerSelect(DatabaseSelect $objSelect)
    {
        $this->objSelect = $objSelect;

        return $this;
    }

    /**
     * 添加预载入的关联.
     *
     * @param mixed $mixRelation
     *
     * @return $this
     */
    public function with($mixRelation)
    {
        if (is_string($mixRelation)) {
            $mixRelation = func_get_args();
        }
        $this->arrPreLoad = array_merge($this->arrPreLoad, $this->parseWithRelation($mixRelation));

        return $this;
    }

    /**
     * 尝试解析结果预载.
     *
     * @param mixed $mixResult
     *
     * @return mixed
     */
    public function preLoadResult($mixResult)
    {
        list($mixResult, $strType) = $this->conversionToEntitys($mixResult);

        if (is_array($mixResult)) {
            $mixResult = $this->preLoadRelation($mixResult);

            if ('entity' === $strType) {
                $mixResult = reset($mixResult);
            } elseif ('collection' === $strType) {
                $mixResult = new collection($mixResult);
            }
        }

        return $mixResult;
    }

    /**
     * 通过主键查找模型实体.
     *
     * @param mixed $mixId
     * @param array $arrColumn
     *
     * @return null|\Leevel\Collection\Collection|\Leevel\Database\Ddd\IEntity
     */
    public function find($mixId, $arrColumn = ['*'])
    {
        if (is_array($mixId)) {
            return $this->findMany($mixId, $arrColumn);
        }

        return $this->objSelect->where($this->objEntity->getPrimaryKeyNameForQuery(), '=', $mixId)->setColumns($arrColumn)->getOne();
    }

    /**
     * 根据主键查找模型实体.
     *
     * @param array $arrId
     * @param array $arrColumn
     *
     * @return \Leevel\Collection\Collection
     */
    public function findMany($arrId, $arrColumn = ['*'])
    {
        if (empty($arrId)) {
            return $this->objEntity->collection();
        }

        return $this->objSelect->whereIn($this->objEntity->getPrimaryKeyNameForQuery(), $arrId)->setColumns($arrColumn)->getAll();
    }

    /**
     * 通过主键查找模型实体，未找到则抛出异常.
     *
     * @param mixed $mixId
     * @param array $arrColumn
     *
     * @return \Leevel\Collection\Collection|\Leevel\Database\Ddd\IEntity
     */
    public function findOrFail($mixId, $arrColumn = ['*'])
    {
        $mixResult = $this->find($mixId, $arrColumn);

        if (is_array($mixId)) {
            if (count($mixResult) === count(array_unique($mixId))) {
                return $mixResult;
            }
        } elseif (null !== $mixResult) {
            return $mixResult;
        }

        throw (new EntityNotFoundException())->entity(get_class($this->objEntity));
    }

    /**
     * 通过主键查找模型实体，未找到初始化一个新的模型实体.
     *
     * @param mixed  $mixId
     * @param array  $arrColumn
     * @param array  $arrData
     * @param mixed  $mixConnect
     * @param string $strTable
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function findOrNew($mixId, $arrColumn = ['*'], $arrData = null, $mixConnect = null, $strTable = null)
    {
        if (null !== ($objEntity = $this->find($mixId, $arrColumn))) {
            return $objEntity;
        }

        return $this->objEntity->newInstance($arrData, $mixConnect ?: $this->objEntity->getConnect(), $strTable ?: $this->objEntity->getTable());
    }

    /**
     * 查找第一个结果.
     *
     * @param array $columns
     * @param mixed $arrColumn
     *
     * @return null|\Leevel\Database\Ddd\IEntity|static
     */
    public function first($arrColumn = ['*'])
    {
        return $this->objSelect->setColumns($arrColumn)->getOne();
    }

    /**
     * 查找第一个结果，未找到则抛出异常.
     *
     * @param array $arrColumn
     *
     * @return \Leevel\Database\Ddd\IEntity|static
     */
    public function firstOrFail($arrColumn = ['*'])
    {
        if (null !== (($objEntity = $this->first($arrColumn)))) {
            return $objEntity;
        }

        throw (new EntityNotFoundException())->entity(get_class($this->objEntity));
    }

    /**
     * 查找第一个结果，未找到则初始化一个新的模型实体.
     *
     * @param array  $arrProp
     * @param mixed  $mixConnect
     * @param string $strTable
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function firstOrNew(array $arrProp, $mixConnect = null, $strTable = null)
    {
        if (null !== (($objEntity = $this->getFirstByProp($arrProp)))) {
            return $objEntity;
        }

        return $this->objEntity->newInstance($arrProp, $mixConnect ?: $this->objEntity->getConnect(), $strTable ?: $this->objEntity->getTable());
    }

    /**
     * 尝试根据属性查找一个模型实体，未找到则新建一个模型实体.
     *
     * @param array  $arrProp
     * @param mixed  $mixConnect
     * @param string $strTable
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function firstOrCreate(array $arrProp, $mixConnect = null, $strTable = null)
    {
        if (null !== (($objEntity = $this->getFirstByProp($arrProp)))) {
            return $objEntity;
        }

        return $this->objEntity->newInstance($arrProp, $mixConnect ?: $this->objEntity->getConnect(), $strTable ?: $this->objEntity->getTable())->create();
    }

    /**
     * 尝试根据属性查找一个模型实体，未找到则新建或者更新一个模型实体.
     *
     * @param array  $arrProp
     * @param array  $arrData
     * @param mixed  $mixConnect
     * @param string $strTable
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function updateOrCreate(array $arrProp, array $arrData = [], $mixConnect = null, $strTable = null)
    {
        return $this->firstOrNew($arrProp, $mixConnect, $strTable)->forceProps($arrData)->save();
    }

    /**
     * 新建一个模型实体.
     *
     * @param array  $arrProp
     * @param mixed  $mixConnect
     * @param string $strTable
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function onlyCreate(array $arrProp = [], $mixConnect = null, $strTable = null)
    {
        return $this->objEntity->newInstance($arrProp, $mixConnect ?: $this->objEntity->getConnect(), $strTable ?: $this->objEntity->getTable())->save();
    }

    /**
     * 从模型实体中软删除数据.
     *
     * @return int
     */
    public function softDelete()
    {
        $objSelect = $this->objSelect->where($this->objEntity->getKeyConditionForQuery());
        $this->objEntity->{$this->getDeletedAtColumn()} = $objTime = $this->objEntity->carbon();
        $this->objEntity->addDate($this->getDeletedAtColumn());

        $this->objEntity->runEvent(Entity::BEFORE_SOFT_DELETE_EVENT);

        $intNum = $objSelect->update([
            $this->getDeletedAtColumn() => $this->objEntity->fromDateTime($objTime),
        ]);

        $this->objEntity->runEvent(Entity::AFTER_SOFT_DELETE_EVENT);

        return $intNum;
    }

    /**
     * 根据主键 ID 删除模型实体.
     *
     * @param array|int $ids
     * @param mixed     $mixId
     *
     * @return int
     */
    public function softDestroy($mixId)
    {
        $intCount = 0;
        $mixId = (array) $mixId;
        $objInstance = $this->objEntity->newInstance();
        foreach ($objInstance->whereIn($objInstance->getPrimaryKeyNameForQuery(), $mixId)->getAll() as $objEntity) {
            if ($objEntity->softDelete()) {
                $intCount++;
            }
        }

        return $intCount;
    }

    /**
     * 恢复软删除的模型实体.
     *
     * @return null|bool
     */
    public function softRestore()
    {
        $this->objEntity->runEvent(Entity::BEFORE_SOFT_RESTORE_EVENT);

        $this->objEntity->{$this->getDeletedAtColumn()} = null;
        $intNum = $this->objEntity->update();

        $this->objEntity->runEvent(Entity::AFTER_SOFT_RESTORE_EVENT);

        return $intNum;
    }

    /**
     * 获取不包含软删除的数据.
     *
     * @return \Leevel\Database\Select
     */
    public function withoutSoftDeleted()
    {
        return $this->objSelect->whereNull($this->getDeletedAtColumn());
    }

    /**
     * 获取只包含软删除的数据.
     *
     * @return \Leevel\Database\Select
     */
    public function onlySoftDeleted()
    {
        return $this->objSelect->whereNotNull($this->getDeletedAtColumn());
    }

    /**
     * 检查模型实体是否已经被软删除了.
     *
     * @return bool
     */
    public function softDeleted()
    {
        return null !== $this->objEntity->{$this->getDeletedAtColumn()};
    }

    /**
     * 获取软删除字段.
     *
     * @return string
     */
    public function getDeletedAtColumn()
    {
        if (defined(get_class($this->objEntity).'::DELETED_AT')) {
            eval('$strDeleteAt = '.get_class($this->objEntity).'::DELETED_AT;');
        } else {
            $strDeleteAt = 'deleted_at';
        }

        if (!$this->objEntity->hasField($strDeleteAt)) {
            throw new Exception(sprintf('Entity %s do not have soft delete field [%s]', get_class($this->objEntity), $strDeleteAt));
        }

        return $strDeleteAt;
    }

    /**
     * 获取删除表加字段.
     *
     * @return string
     */
    public function getFullDeletedAtColumn()
    {
        return $this->objEntity->getTable().'.'.$this->getDeletedAtColumn();
    }

    /**
     * 查询范围.
     *
     * @param mixed $mixScope
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function scope($mixScope)
    {
        if ($mixScope instanceof DatabaseSelect) {
            return $mixScope;
        }

        $objSelect = $this->objSelect;

        $arrArgs = func_get_args();
        array_shift($arrArgs);
        array_unshift($arrArgs, $objSelect);

        if ($mixScope instanceof Closure) {
            $mixResultCallback = call_user_func_array($mixScope, $arrArgs);
            if ($mixResultCallback instanceof DatabaseSelect) {
                $objSelect = $mixResultCallback;
            }
            unset($mixResultCallback);
            $this->objEntity->setSelectForQuery($objSelect);
        } else {
            foreach (Arr::normalize($mixScope) as $strScope) {
                $strScope = 'scope'.ucwords($strScope);
                if (method_exists($this->objEntity, $strScope)) {
                    $mixResultCallback = call_user_func_array([
                        $this->objEntity,
                        $strScope,
                    ], $arrArgs);
                    if ($mixResultCallback instanceof DatabaseSelect) {
                        $objSelect = $mixResultCallback;
                    }
                    unset($mixResultCallback);
                    $this->objEntity->setSelectForQuery($objSelect);
                }
            }
        }

        unset($objSelect, $arrArgs, $mixScope);

        return $this->objEntity;
    }

    /**
     * 预载入模型实体.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $arrEntity
     *
     * @return array
     */
    protected function preLoadRelation(array $arrEntity)
    {
        foreach ($this->arrPreLoad as $strName => $calCondition) {
            if (false === strpos($strName, '.')) {
                $arrEntity = $this->loadRelation($arrEntity, $strName, $calCondition);
            }
        }

        return $arrEntity;
    }

    /**
     * 取得关联模型实体.
     *
     * @param string $name
     * @param mixed  $strName
     *
     * @return \leevel\Mvc\Relation\Relation
     */
    protected function getRelation($strName)
    {
        $objRelation = Relation::withoutRelationCondition(function () use ($strName) {
            return $this->objEntity->{$strName}();
        });

        $arrNested = $this->nestedRelation($strName);
        if (count($arrNested) > 0) {
            $objRelation->getSelect()->with($arrNested);
        }

        return $objRelation;
    }

    /**
     * 尝试取得嵌套关联.
     *
     * @param string $strRelation
     *
     * @return array
     */
    protected function nestedRelation($strRelation)
    {
        $arrNested = [];

        foreach ($this->arrPreLoad as $strName => $calCondition) {
            if ($this->isNested($strName, $strRelation)) {
                $arrNested[substr($strName, strlen($strRelation.'.'))] = $calCondition;
            }
        }

        return $arrNested;
    }

    /**
     * 判断是否存在嵌套关联.
     *
     * @param string $strName
     * @param string $strRelation
     *
     * @return bool
     */
    protected function isNested($strName, $strRelation)
    {
        return Str::contains($strName, '.') && Str::startsWith($strName, $strRelation.'.');
    }

    /**
     * 格式化预载入关联.
     *
     * @param array $arrRelation
     *
     * @return array
     */
    protected function parseWithRelation(array $arrRelation)
    {
        $arr = [];

        foreach ($arrRelation as $mixName => $mixCondition) {
            if (is_numeric($mixName)) {
                list($mixName, $mixCondition) = [
                    $mixCondition,
                    function () {
                    },
                ];
            }

            $arr = $this->parseNestedWith($mixName, $arr);
            $arr[$mixName] = $mixCondition;
        }

        return $arr;
    }

    /**
     * 解析嵌套关联.
     *
     * @param string $strName
     * @param array  $arrResult
     *
     * @return array
     */
    protected function parseNestedWith($strName, array $arrResult)
    {
        $arrProgress = [];

        foreach (explode('.', $strName) as $strSegment) {
            $arrProgress[] = $strSegment;
            if (!isset($arrResult[$strLast = implode('.', $arrProgress)])) {
                $arrResult[$strLast] = function () {
                };
            }
        }

        return $arrResult;
    }

    /**
     * 转换结果到模型实体类型.
     *
     * @param mixed $mixResult
     *
     * @return array
     */
    protected function conversionToEntitys($mixResult)
    {
        $strType = '';

        if ($mixResult instanceof collection) {
            $arr = [];
            foreach ($mixResult as $objEntity) {
                $arr[] = $objEntity;
            }
            $mixResult = $arr;
            $strType = 'collection';
        } elseif ($mixResult instanceof IEntity) {
            $mixResult = [
                $mixResult,
            ];

            $strType = 'entity';
        }

        return [
            $mixResult,
            $strType,
        ];
    }

    /**
     * 关联数据设置到模型实体上.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $arrEntity
     * @param string                         $strName
     * @param callable                       $calCondition
     *
     * @return array
     */
    protected function loadRelation(array $arrEntity, $strName, callable $calCondition)
    {
        $objRelation = $this->getRelation($strName);
        $objRelation->preLoadCondition($arrEntity);
        call_user_func($calCondition, $objRelation);

        return $objRelation->matchPreLoad($arrEntity, $objRelation->getPreLoad(), $strName);
    }

    /**
     * 尝试根据属性查找一个模型实体.
     *
     * @param array $arrProp
     *
     * @return null|\Leevel\Database\Ddd\IEntity
     */
    protected function getFirstByProp(array $arrProp)
    {
        if (null !== ($objEntity = $this->objSelect->where($arrProp)->getOne())) {
            return $objEntity;
        }
    }
}
