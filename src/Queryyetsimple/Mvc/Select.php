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

namespace Leevel\Mvc;

use Closure;
use Exception;
use Leevel\Collection\Collection;
use Leevel\Database\Select as DatabaseSelect;
use Leevel\Mvc\Relation\Relation;
use Leevel\Support\Arr;
use Leevel\Support\Str;

/**
 * 模型查询.
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
     * 模型.
     *
     * @var \Leevel\Mvc\IModel
     */
    protected $objModel;

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
     * @param \Leevel\Mvc\IModel $objModel
     */
    public function __construct($objModel)
    {
        $this->objModel = $objModel;
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
     * 获取模型.
     *
     * @param mixed $objModel
     *
     * @return \Leevel\Mvc\IModel
     */
    public function getModel($objModel)
    {
        return $this->objModel;
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
        list($mixResult, $strType) = $this->conversionToModels($mixResult);

        if (is_array($mixResult)) {
            $mixResult = $this->preLoadRelation($mixResult);
            if ('model' === $strType) {
                $mixResult = reset($mixResult);
            } elseif ('collection' === $strType) {
                $mixResult = new collection($mixResult);
            }
        }

        return $mixResult;
    }

    /**
     * 通过主键查找模型.
     *
     * @param mixed $mixId
     * @param array $arrColumn
     *
     * @return null|\Leevel\Collection\Collection|\Leevel\Mvc\IModel
     */
    public function find($mixId, $arrColumn = ['*'])
    {
        if (is_array($mixId)) {
            return $this->findMany($mixId, $arrColumn);
        }

        return $this->objSelect->where($this->objModel->getPrimaryKeyNameForQuery(), '=', $mixId)->setColumns($arrColumn)->getOne();
    }

    /**
     * 根据主键查找模型.
     *
     * @param array $arrId
     * @param array $arrColumn
     *
     * @return \Leevel\Collection\Collection
     */
    public function findMany($arrId, $arrColumn = ['*'])
    {
        if (empty($arrId)) {
            return $this->objModel->collection();
        }

        return $this->objSelect->whereIn($this->objModel->getPrimaryKeyNameForQuery(), $arrId)->setColumns($arrColumn)->getAll();
    }

    /**
     * 通过主键查找模型，未找到则抛出异常.
     *
     * @param mixed $mixId
     * @param array $arrColumn
     *
     * @return \Leevel\Collection\Collection|\Leevel\Mvc\IModel
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

        throw (new ModelNotFoundException())->model(get_class($this->objModel));
    }

    /**
     * 通过主键查找模型，未找到初始化一个新的模型.
     *
     * @param mixed  $mixId
     * @param array  $arrColumn
     * @param array  $arrData
     * @param mixed  $mixConnect
     * @param string $strTable
     *
     * @return \Leevel\Mvc\IModel
     */
    public function findOrNew($mixId, $arrColumn = ['*'], $arrData = null, $mixConnect = null, $strTable = null)
    {
        if (null !== ($objModel = $this->find($mixId, $arrColumn))) {
            return $objModel;
        }

        return $this->objModel->newInstance($arrData, $mixConnect ?: $this->objModel->getConnect(), $strTable ?: $this->objModel->getTable());
    }

    /**
     * 查找第一个结果.
     *
     * @param array $columns
     * @param mixed $arrColumn
     *
     * @return null|\Leevel\Mvc\IModel|static
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
     * @return \Leevel\Mvc\IModel|static
     */
    public function firstOrFail($arrColumn = ['*'])
    {
        if (null !== (($objModel = $this->first($arrColumn)))) {
            return $objModel;
        }

        throw (new ModelNotFoundException())->model(get_class($this->objModel));
    }

    /**
     * 查找第一个结果，未找到则初始化一个新的模型.
     *
     * @param array  $arrProp
     * @param mixed  $mixConnect
     * @param string $strTable
     *
     * @return \Leevel\Mvc\IModel
     */
    public function firstOrNew(array $arrProp, $mixConnect = null, $strTable = null)
    {
        if (null !== (($objModel = $this->getFirstByProp($arrProp)))) {
            return $objModel;
        }

        return $this->objModel->newInstance($arrProp, $mixConnect ?: $this->objModel->getConnect(), $strTable ?: $this->objModel->getTable());
    }

    /**
     * 尝试根据属性查找一个模型，未找到则新建一个模型.
     *
     * @param array  $arrProp
     * @param mixed  $mixConnect
     * @param string $strTable
     *
     * @return \Leevel\Mvc\IModel
     */
    public function firstOrCreate(array $arrProp, $mixConnect = null, $strTable = null)
    {
        if (null !== (($objModel = $this->getFirstByProp($arrProp)))) {
            return $objModel;
        }

        return $this->objModel->newInstance($arrProp, $mixConnect ?: $this->objModel->getConnect(), $strTable ?: $this->objModel->getTable())->create();
    }

    /**
     * 尝试根据属性查找一个模型，未找到则新建或者更新一个模型.
     *
     * @param array  $arrProp
     * @param array  $arrData
     * @param mixed  $mixConnect
     * @param string $strTable
     *
     * @return \Leevel\Mvc\IModel
     */
    public function updateOrCreate(array $arrProp, array $arrData = [], $mixConnect = null, $strTable = null)
    {
        return $this->firstOrNew($arrProp, $mixConnect, $strTable)->forceProps($arrData)->save();
    }

    /**
     * 新建一个模型.
     *
     * @param array  $arrProp
     * @param mixed  $mixConnect
     * @param string $strTable
     *
     * @return \Leevel\Mvc\IModel
     */
    public function onlyCreate(array $arrProp = [], $mixConnect = null, $strTable = null)
    {
        return $this->objModel->newInstance($arrProp, $mixConnect ?: $this->objModel->getConnect(), $strTable ?: $this->objModel->getTable())->save();
    }

    /**
     * 从模型中软删除数据.
     *
     * @return int
     */
    public function softDelete()
    {
        $objSelect = $this->objSelect->where($this->objModel->getKeyConditionForQuery());
        $this->objModel->{$this->getDeletedAtColumn()} = $objTime = $this->objModel->carbon();
        $this->objModel->addDate($this->getDeletedAtColumn());

        $this->objModel->runEvent(model::BEFORE_SOFT_DELETE_EVENT);

        $intNum = $objSelect->update([
            $this->getDeletedAtColumn() => $this->objModel->fromDateTime($objTime),
        ]);

        $this->objModel->runEvent(model::AFTER_SOFT_DELETE_EVENT);

        return $intNum;
    }

    /**
     * 根据主键 ID 删除模型.
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
        $objInstance = $this->objModel->newInstance();
        foreach ($objInstance->whereIn($objInstance->getPrimaryKeyNameForQuery(), $mixId)->getAll() as $objModel) {
            if ($objModel->softDelete()) {
                ++$intCount;
            }
        }

        return $intCount;
    }

    /**
     * 恢复软删除的模型.
     *
     * @return null|bool
     */
    public function softRestore()
    {
        $this->objModel->runEvent(model::BEFORE_SOFT_RESTORE_EVENT);

        $this->objModel->{$this->getDeletedAtColumn()} = null;
        $intNum = $this->objModel->update();

        $this->objModel->runEvent(model::AFTER_SOFT_RESTORE_EVENT);

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
     * 检查模型是否已经被软删除了.
     *
     * @return bool
     */
    public function softDeleted()
    {
        return null !== $this->objModel->{$this->getDeletedAtColumn()};
    }

    /**
     * 获取软删除字段.
     *
     * @return string
     */
    public function getDeletedAtColumn()
    {
        if (defined(get_class($this->objModel).'::DELETED_AT')) {
            eval('$strDeleteAt = '.get_class($this->objModel).'::DELETED_AT;');
        } else {
            $strDeleteAt = 'deleted_at';
        }

        if (!$this->objModel->hasField($strDeleteAt)) {
            throw new Exception(sprintf('Model %s do not have soft delete field [%s]', get_class($this->objModel), $strDeleteAt));
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
        return $this->objModel->getTable().'.'.$this->getDeletedAtColumn();
    }

    /**
     * 查询范围.
     *
     * @param mixed $mixScope
     *
     * @return \Leevel\Mvc\IModel
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
            $this->objModel->setSelectForQuery($objSelect);
        } else {
            foreach (Arr::normalize($mixScope) as $strScope) {
                $strScope = 'scope'.ucwords($strScope);
                if (method_exists($this->objModel, $strScope)) {
                    $mixResultCallback = call_user_func_array([
                        $this->objModel,
                        $strScope,
                    ], $arrArgs);
                    if ($mixResultCallback instanceof DatabaseSelect) {
                        $objSelect = $mixResultCallback;
                    }
                    unset($mixResultCallback);
                    $this->objModel->setSelectForQuery($objSelect);
                }
            }
        }

        unset($objSelect, $arrArgs, $mixScope);

        return $this->objModel;
    }

    /**
     * 预载入模型.
     *
     * @param \Leevel\Mvc\IModel[] $arrModel
     *
     * @return array
     */
    protected function preLoadRelation(array $arrModel)
    {
        foreach ($this->arrPreLoad as $strName => $calCondition) {
            if (false === strpos($strName, '.')) {
                $arrModel = $this->loadRelation($arrModel, $strName, $calCondition);
            }
        }

        return $arrModel;
    }

    /**
     * 取得关联模型.
     *
     * @param string $name
     * @param mixed  $strName
     *
     * @return \leevel\Mvc\Relation\Relation
     */
    protected function getRelation($strName)
    {
        $objRelation = Relation::withoutRelationCondition(function () use ($strName) {
            return $this->objModel->{$strName}();
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
     * 转换结果到模型类型.
     *
     * @param mixed $mixResult
     *
     * @return array
     */
    protected function conversionToModels($mixResult)
    {
        $strType = '';

        if ($mixResult instanceof collection) {
            $arr = [];
            foreach ($mixResult as $objModel) {
                $arr[] = $objModel;
            }
            $mixResult = $arr;
            $strType = 'collection';
        } elseif ($mixResult instanceof IModel) {
            $mixResult = [
                $mixResult,
            ];
            $strType = 'model';
        }

        return [
            $mixResult,
            $strType,
        ];
    }

    /**
     * 关联数据设置到模型上.
     *
     * @param \Leevel\Mvc\IModel[] $arrModel
     * @param string               $strName
     * @param callable             $calCondition
     *
     * @return array
     */
    protected function loadRelation(array $arrModel, $strName, callable $calCondition)
    {
        $objRelation = $this->getRelation($strName);
        $objRelation->preLoadCondition($arrModel);
        call_user_func($calCondition, $objRelation);

        return $objRelation->matchPreLoad($arrModel, $objRelation->getPreLoad(), $strName);
    }

    /**
     * 尝试根据属性查找一个模型.
     *
     * @param array $arrProp
     *
     * @return null|\Leevel\Mvc\IModel
     */
    protected function getFirstByProp(array $arrProp)
    {
        if (null !== ($objModel = $this->objSelect->where($arrProp)->getOne())) {
            return $objModel;
        }

        return null;
    }
}
