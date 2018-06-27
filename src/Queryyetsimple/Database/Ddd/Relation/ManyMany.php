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
use Leevel\Mvc\IModel;

/**
 * 关联模型 ManyMany.
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
    protected $objMiddleSelect;

    /**
     * 中间表模型.
     *
     * @var \Leevel\Mvc\IModel
     */
    protected $objMiddleModel;

    /**
     * 目标中间表关联字段.
     *
     * @var string
     */
    protected $strMiddleTargetKey;

    /**
     * 源中间表关联字段.
     *
     * @var string
     */
    protected $strMiddleSourceKey;

    /**
     * 中间表隐射数据.
     *
     * @var array
     */
    protected $arrMiddleMap = [];

    /**
     * 构造函数.
     *
     * @param \Leevel\Mvc\IModel $objTargetModel
     * @param \Leevel\Mvc\IModel $objSourceModel
     * @param \Leevel\Mvc\IModel $objMiddleModel
     * @param string             $strTargetKey
     * @param string             $strSourceKey
     * @param string             $strMiddleTargetKey
     * @param string             $strMiddleSourceKey
     */
    public function __construct(IModel $objTargetModel, IModel $objSourceModel, IModel $objMiddleModel, $strTargetKey, $strSourceKey, $strMiddleTargetKey, $strMiddleSourceKey)
    {
        $this->objMiddleModel = $objMiddleModel;
        $this->strMiddleTargetKey = $strMiddleTargetKey;
        $this->strMiddleSourceKey = $strMiddleSourceKey;

        parent::__construct($objTargetModel, $objSourceModel, $strTargetKey, $strSourceKey);
    }

    /**
     * 关联基础查询条件.
     */
    public function addRelationCondition()
    {
        if (static::$booRelationCondition) {
            $this->objMiddleSelect = $this->objMiddleModel->where($this->strMiddleSourceKey, $this->getSourceValue());
        }
    }

    /**
     * 设置预载入关联查询条件.
     *
     * @param \Leevel\Mvc\IModel[] $arrModel
     */
    public function preLoadCondition(array $arrModel)
    {
        $this->preLoadRelationCondition($arrModel);
        $this->parseSelectCondition();
    }

    /**
     * 匹配关联查询数据到模型.
     *
     * @param \Leevel\Mvc\IModel[]          $arrModel
     * @param \Leevel\Collection\Collection $objResult
     * @param string                        $strRelation
     *
     * @return array
     */
    public function matchPreLoad(array $arrModel, collection $objResult, $strRelation)
    {
        $arrMap = $this->buildMap($objResult);

        foreach ($arrModel as &$objModel) {
            $mixKey = $objModel->getProp($this->strSourceKey);
            if (isset($arrMap[$mixKey])) {
                $objModel->setRelationProp($strRelation, $this->objTargetModel->collection($arrMap[$mixKey]));
            }
        }

        return $arrModel;
    }

    /**
     * 中间表查询回调处理.
     *
     * @param callable $calCallback
     *
     * @return $this
     */
    public function middleCondition($calCallback)
    {
        call_user_func_array($calCallback, [
            $this->objMiddleSelect,
            $this,
        ]);

        return $this;
    }

    /**
     * 取回源模型对应数据.
     *
     * @return mixed
     */
    public function getSourceValue()
    {
        return $this->objSourceModel->getProp($this->strSourceKey);
    }

    /**
     * 查询关联对象
     *
     * @return mixed
     */
    public function sourceQuery()
    {
        if (false === $this->parseSelectCondition()) {
            return new collection();
        }

        return $this->objSelect->getAll();
    }

    /**
     * 取得中间表查询对象
     *
     * @return \Leevel\Database\Select
     */
    public function getMiddleSelect()
    {
        return $this->objMiddleSelect;
    }

    /**
     * 取得中间表模型.
     *
     * @return \Leevel\Mvc\IModel
     */
    public function getMiddleModel()
    {
        return $this->objMiddleModel;
    }

    /**
     * 取得目标中间表关联字段.
     *
     * @return string
     */
    public function getTargetKey()
    {
        return $this->strMiddleTargetKey;
    }

    /**
     * 取得源中间表关联字段.
     *
     * @return string
     */
    public function getSourceKey()
    {
        return $this->strMiddleSourceKey;
    }

    /**
     * 取得中间表隐射数据.
     *
     * @return array
     */
    public function getMiddleMap()
    {
        return $this->arrMiddleMap;
    }

    /**
     * 取得源外键值
     *
     * @return mixed
     */
    public function getSourceKeyValue()
    {
        return $this->objSourceModel->getProp($this->strSourceKey);
    }

    /**
     * 预载入关联基础查询条件.
     */
    protected function preLoadRelationCondition(array $arrModel)
    {
        $this->objMiddleSelect = $this->objMiddleModel->whereIn($this->strMiddleSourceKey, $this->getPreLoadSourceValue($arrModel));
    }

    /**
     * 取回源模型对应数据.
     *
     * @return mixed
     */
    protected function getPreLoadSourceValue(array $arrModel)
    {
        $arr = [];

        foreach ($arrModel as $objSourceModel) {
            $arr[] = $objSourceModel->{$this->strSourceKey};
        }

        return $arr;
    }

    /**
     * 模型隐射数据.
     *
     * @param \Leevel\Collection\Collection $objResult
     *
     * @return array
     */
    protected function buildMap(collection $objResult)
    {
        $arrMap = [];

        foreach ($objResult as $objResultModel) {
            $mixKey = $objResultModel->getProp($this->strTargetKey);
            if (isset($this->arrMiddleMap[$mixKey])) {
                foreach ($this->arrMiddleMap[$mixKey] as $mixValue) {
                    $arrMap[$mixValue][] = $objResultModel;
                }
            }
        }

        return $arrMap;
    }

    /**
     * 通过中间表获取目标 ID.
     *
     * @return array
     */
    protected function parseSelectCondition()
    {
        $arrTargetId = $this->parseTargetId();

        $this->objSelect->whereIn($this->strTargetKey, $arrTargetId ?: [
            0,
        ]);

        if (!$arrTargetId) {
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
        $arr = $arrTargetId = [];

        foreach ($this->objMiddleSelect->getAll() as $objMiddleModel) {
            $arr[$objMiddleModel->{$this->strMiddleTargetKey}][] = $objMiddleModel->{$this->strMiddleSourceKey};
            $arrTargetId[] = $objMiddleModel->{$this->strMiddleTargetKey};
        }

        $this->arrMiddleMap = $arr;

        return array_unique($arrTargetId);
    }
}
