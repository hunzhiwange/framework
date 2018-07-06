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
    protected $objMiddleSelect;

    /**
     * 中间表模型实体.
     *
     * @var \Leevel\Database\Ddd\IEntity
     */
    protected $objMiddleEntity;

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
     * @param \Leevel\Database\Ddd\IEntity $objTargetEntity
     * @param \Leevel\Database\Ddd\IEntity $objSourceEntity
     * @param \Leevel\Database\Ddd\IEntity $objMiddleEntity
     * @param string                       $strTargetKey
     * @param string                       $strSourceKey
     * @param string                       $strMiddleTargetKey
     * @param string                       $strMiddleSourceKey
     */
    public function __construct(IEntity $objTargetEntity, IEntity $objSourceEntity, IEntity $objMiddleEntity, $strTargetKey, $strSourceKey, $strMiddleTargetKey, $strMiddleSourceKey)
    {
        $this->objMiddleEntity = $objMiddleEntity;
        $this->strMiddleTargetKey = $strMiddleTargetKey;
        $this->strMiddleSourceKey = $strMiddleSourceKey;

        parent::__construct($objTargetEntity, $objSourceEntity, $strTargetKey, $strSourceKey);
    }

    /**
     * 关联基础查询条件.
     */
    public function addRelationCondition()
    {
        if (static::$booRelationCondition) {
            $this->objMiddleSelect = $this->objMiddleEntity->where($this->strMiddleSourceKey, $this->getSourceValue());
        }
    }

    /**
     * 设置预载入关联查询条件.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $arrEntity
     */
    public function preLoadCondition(array $arrEntity)
    {
        $this->preLoadRelationCondition($arrEntity);
        $this->parseSelectCondition();
    }

    /**
     * 匹配关联查询数据到模型实体.
     *
     * @param \Leevel\Database\Ddd\IEntity[] $arrEntity
     * @param \Leevel\Collection\Collection  $objResult
     * @param string                         $strRelation
     *
     * @return array
     */
    public function matchPreLoad(array $arrEntity, collection $objResult, $strRelation)
    {
        $arrMap = $this->buildMap($objResult);

        foreach ($arrEntity as &$objEntity) {
            $mixKey = $objEntity->getProp($this->strSourceKey);
            if (isset($arrMap[$mixKey])) {
                $objEntity->setRelationProp($strRelation, $this->objTargetEntity->collection($arrMap[$mixKey]));
            }
        }

        return $arrEntity;
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
     * 取回源模型实体对应数据.
     *
     * @return mixed
     */
    public function getSourceValue()
    {
        return $this->objSourceEntity->getProp($this->strSourceKey);
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
     * 取得中间表模型实体.
     *
     * @return \Leevel\Database\Ddd\IEntity
     */
    public function getMiddleEntity()
    {
        return $this->objMiddleEntity;
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
        return $this->objSourceEntity->getProp($this->strSourceKey);
    }

    /**
     * 预载入关联基础查询条件.
     */
    protected function preLoadRelationCondition(array $arrEntity)
    {
        $this->objMiddleSelect = $this->objMiddleEntity->whereIn($this->strMiddleSourceKey, $this->getPreLoadSourceValue($arrEntity));
    }

    /**
     * 取回源模型实体对应数据.
     *
     * @return mixed
     */
    protected function getPreLoadSourceValue(array $arrEntity)
    {
        $arr = [];

        foreach ($arrEntity as $objSourceEntity) {
            $arr[] = $objSourceEntity->{$this->strSourceKey};
        }

        return $arr;
    }

    /**
     * 模型实体隐射数据.
     *
     * @param \Leevel\Collection\Collection $objResult
     *
     * @return array
     */
    protected function buildMap(collection $objResult)
    {
        $arrMap = [];

        foreach ($objResult as $objResultEntity) {
            $mixKey = $objResultEntity->getProp($this->strTargetKey);
            if (isset($this->arrMiddleMap[$mixKey])) {
                foreach ($this->arrMiddleMap[$mixKey] as $mixValue) {
                    $arrMap[$mixValue][] = $objResultEntity;
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

        foreach ($this->objMiddleSelect->getAll() as $objMiddleEntity) {
            $arr[$objMiddleEntity->{$this->strMiddleTargetKey}][] = $objMiddleEntity->{$this->strMiddleSourceKey};
            $arrTargetId[] = $objMiddleEntity->{$this->strMiddleTargetKey};
        }

        $this->arrMiddleMap = $arr;

        return array_unique($arrTargetId);
    }
}
